<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Vehicule;
use App\Models\ServiceVehiculeDuration;
use App\Models\StationSetting;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\LoyaltyService;

class ReservationService
{
    public function __construct(
        private SlotService $slotService,
        private LoyaltyService $loyaltyService
    ) {}

    /**
     * Générer un numéro unique WA12345
     */
    private function generateReservationNumber(): string
    {
        do {
            $number = 'WA' . strtoupper(Str::random(5));
        } while (Reservation::where('reservation_number', $number)->exists());

        return $number;
    }

    /**
     * Récupérer la config prix/durée selon service + taille véhicule
     */
    private function getServiceConfig(string $serviceId, string $vehiculeSize): ServiceVehiculeDuration
    {
        $config = ServiceVehiculeDuration::where('service_id', $serviceId)
            ->where('vehicule_size', $vehiculeSize)
            ->first();

        if (!$config) {
            throw new \Exception("Ce service n'est pas disponible pour cette taille de véhicule.");
        }

        return $config;
    }

    /**
     * Vérifier disponibilité d'un créneau (réutilise la logique SlotService)
     */
    public function isSlotAvailable(string $date, string $time, string $serviceId, string $vehiculeSize, ?string $excludeId = null): bool
    {
        $slots = $this->slotService->getAvailableSlots($date, $serviceId, $vehiculeSize);
        $availableTimes = array_column($slots, 'time');

        if (!in_array($time, $availableTimes)) {
            return false;
        }

        // Si on modifie une réservation existante, on l'exclut du calcul
        if ($excludeId) {
            $capacity = $this->getOnlineCapacity();
            $count = Reservation::where('scheduled_date', $date)
                ->where('scheduled_time', $time)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->where('id', '!=', $excludeId)
                ->count();

            return $count < $capacity;
        }

        return true;
    }

    private function getOnlineCapacity(): int
    {
        $settings = StationSetting::where('key', 'capacity_per_slot')->first();
        return $settings->value['online_capacity'] ?? 2;
    }

    /**
     * Créer une réservation
     */
    public function create(array $data, string $userId): Reservation
    {
        $vehicule = Vehicule::where('id', $data['vehicule_id'])
            ->where('user_id', $userId)
            ->first();

        if (!$vehicule) {
            throw new \Exception("Ce véhicule ne vous appartient pas ou n'existe pas.");
        }

        $config = $this->getServiceConfig($data['service_id'], $vehicule->size);

        if (!$this->isSlotAvailable($data['scheduled_date'], $data['scheduled_time'], $data['service_id'], $vehicule->size)) {
            throw new \Exception("Ce créneau n'est plus disponible.");
        }

        // Vérifier si ce véhicule a déjà une réservation active
        $activeReservation = Reservation::where('vehicule_id', $data['vehicule_id'])
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->first();

        if ($activeReservation) {
            throw new \Exception(
                "Ce véhicule a déjà une réservation active (#{$activeReservation->reservation_number})."
            );
        }

        $reservation = Reservation::create([
            'reservation_number'   => $this->generateReservationNumber(),
            'user_id'              => $userId,
            'vehicule_id'          => $data['vehicule_id'],
            'service_id'           => $data['service_id'],
            'scheduled_date'       => $data['scheduled_date'],
            'scheduled_time'       => $data['scheduled_time'],
            'duration_minutes'     => $config->duration_minutes,
            'slots_occupied'       => $config->slot_size,
            'price'                => $config->price,
            'payment_method'       => $data['payment_method'] ?? null,
            'special_instructions' => $data['special_instructions'] ?? null,
            'source'               => $data['source'] ?? 'online',
            'status'               => 'pending',
            'payment_status'       => 'pending',
        ]);

        return $reservation->load(['user', 'vehicule', 'service']);
    }

    /**
     * Modifier date/heure/instructions
     */
    public function update(Reservation $reservation, array $data): Reservation
    {
        $this->ensureModifiable($reservation);

        $newDate = $data['scheduled_date'] ?? $reservation->scheduled_date->format('Y-m-d');
        $newTime = $data['scheduled_time'] ?? $reservation->scheduled_time;

        $dateChanged = $newDate !== $reservation->scheduled_date->format('Y-m-d');
        $timeChanged = $newTime !== $reservation->scheduled_time;

        if ($dateChanged || $timeChanged) {
            $vehicule = $reservation->vehicule;
            if (!$this->isSlotAvailable($newDate, $newTime, $reservation->service_id, $vehicule->size, $reservation->id)) {
                throw new \Exception("Ce créneau n'est plus disponible.");
            }
        }

        $reservation->update([
            'scheduled_date'       => $newDate,
            'scheduled_time'       => $newTime,
            'special_instructions' => $data['special_instructions'] ?? $reservation->special_instructions,
        ]);

        return $reservation->fresh(['user', 'vehicule', 'service']);
    }

    /**
     * Annuler une réservation
     */
    public function cancel(Reservation $reservation, ?string $reason = null): Reservation
    {
        $this->ensureCancellable($reservation);

        $reservation->update([
            'status'           => 'cancelled',
            'cancelled_at'     => now(),
            'cancelled_reason' => $reason,
        ]);

        return $reservation->fresh();
    }

    /**
     * Changer le statut (admin/employé) avec transitions validées
     */
    public function updateStatus(Reservation $reservation, string $newStatus): Reservation
    {
        $transitions = [
            'pending'     => ['confirmed', 'cancelled'],
            'confirmed'   => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
        ];

        $current = $reservation->status;

        if (!isset($transitions[$current]) || !in_array($newStatus, $transitions[$current])) {
            throw new \Exception("Transition invalide : {$current} → {$newStatus}");
        }

        $updates = ['status' => $newStatus];

        if ($newStatus === 'completed') {
            $reservation->update([
                'status'       => 'completed',
                'completed_at' => now(),
                'payment_status' => $reservation->payment_method === 'cash' ? 'paid' : $reservation->payment_status,
            ]);

            // Attribuer les points si réservation en ligne (user réel)
            if ($reservation->source === 'online' && $reservation->user) {
                $this->loyaltyService->earnPoints($reservation->user, $reservation);
            }

            return $reservation;
        }

        match ($newStatus) {
            'in_progress' => $updates['started_at'] = now(),
            'completed'   => $updates = array_merge($updates, [
                'completed_at'   => now(),
                'payment_status' => $reservation->payment_method === 'cash' ? 'paid' : $reservation->payment_status,
            ]),
            default => null,
        };

        $reservation->update($updates);

        return $reservation->fresh(['user', 'vehicule', 'service']);
    }

    /**
     * Règle : modification interdite à moins de 2h
     */
    private function ensureModifiable(Reservation $reservation): void
    {
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            throw new \Exception("Cette réservation ne peut plus être modifiée.");
        }

        $scheduledAt = Carbon::parse($reservation->scheduled_date->format('Y-m-d') . ' ' . $reservation->scheduled_time);

        if (now()->diffInMinutes($scheduledAt, false) < 120) {
            throw new \Exception("Modification impossible à moins de 2h du créneau.");
        }
    }

    /**
     * Règle : annulation interdite à moins de 1h
     */
    private function ensureCancellable(Reservation $reservation): void
    {
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            throw new \Exception("Cette réservation ne peut pas être annulée.");
        }

        $scheduledAt = Carbon::parse($reservation->scheduled_date->format('Y-m-d') . ' ' . $reservation->scheduled_time);

        if (now()->diffInMinutes($scheduledAt, false) < 60) {
            throw new \Exception("Annulation impossible à moins d'1h du créneau.");
        }
    }
}