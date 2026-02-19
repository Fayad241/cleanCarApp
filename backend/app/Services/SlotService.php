<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ServiceVehiculeDuration;
use App\Models\StationSetting;
use Carbon\Carbon;

class SlotService
{
    /**
     * Génère les créneaux disponibles pour un service et une taille de véhicule
     */
    public function getAvailableSlots(string $date, string $serviceId, string $vehiculeSize)
    {
        // 1- Récupérer la durée du service pour cette taille de véhicule
        $serviceDuration = ServiceVehiculeDuration::where('service_id', $serviceId)
            ->where('vehicule_size', $vehiculeSize)
            ->first();

        if (!$serviceDuration) {
            throw new \Exception("Service non disponible pour cette taille de véhicule.");
        }

        $slotsNeeded = $serviceDuration->slot_size;
        $durationMinutes = $serviceDuration->duration_minutes;

        // 2- Récupérer les horaires de la station pour ce jour
        $openingHours = $this->getOpeningHoursForDate($date);

        if (!$openingHours || !$openingHours['is_open']) {
            return []; // Station fermée ce jour
        }

        // 3- Générer tous les créneaux de 30 min
        $allSlots = $this->generateTimeSlots(
            $openingHours['open'],
            $openingHours['close'],
            $date
        );

        // 4- Récupérer la capacité de la station
        $capacity = $this->getStationCapacity();

        // 5- Filtrer les créneaux disponibles
        $availableSlots = $this->filterAvailableSlots(
            $allSlots,
            $date,
            $slotsNeeded,
            $capacity
        );

        // 6- Formatter la réponse
        return array_map(function ($slot) use ($durationMinutes) {
            return [
                'time' => $slot,
                'duration_minutes' => $durationMinutes,
                'end_time' => Carbon::parse("$slot")->addMinutes($durationMinutes)->format('H:i'),
            ];
        }, $availableSlots);
    }

    /**
     * Récupère les horaires d'ouverture pour une date donnée
     */
    private function getOpeningHoursForDate(string $date)
    {
        $dayOfWeek = strtolower(Carbon::parse($date)->locale('en')->dayName);
        
        $settings = StationSetting::where('key', 'opening_hours')->first();
        
        if (!$settings) {
            // Horaires par défaut si non configuré
            return [
                'is_open' => true,
                'open' => '08:00',
                'close' => '19:00'
            ];
        }

        $hours = $settings->value[$dayOfWeek] ?? null;

        return $hours;
    }

    /**
     * Génère tous les créneaux de 30 minutes entre ouverture et fermeture
     */
    private function generateTimeSlots(string $openTime, string $closeTime, string $date)
    {
        $slots = [];
        $current = Carbon::parse("$date $openTime");
        $end = Carbon::parse("$date $closeTime");
        $now = Carbon::now();

        while ($current->lt($end)) {
            // Ne pas proposer de créneaux dans le passé
            if ($current->gt($now)) {
                $slots[] = $current->format('H:i');
            }
            $current->addMinutes(30);
        }

        return $slots;
    }

    /**
     * Récupère la capacité de la station
     */
    private function getStationCapacity()
    {
        $settings = StationSetting::where('key', 'capacity_per_slot')->first();

        if (!$settings) {
            return [
                'total_capacity' => 3,
                'online_capacity' => 2,
            ];
        }

        return [
            'total_capacity' => $settings->value['total_capacity'] ?? 3,
            'online_capacity' => $settings->value['online_capacity'] ?? 2,
        ];
    }

    /**
     * Filtre les créneaux disponibles selon les réservations existantes
     */
    private function filterAvailableSlots(array $allSlots, string $date, int $slotsNeeded, array $capacity)
    {
        $availableSlots = [];

        foreach ($allSlots as $index => $slot) {
            // Vérifier si X créneaux consécutifs sont disponibles
            if ($this->hasConsecutiveSlotsAvailable($allSlots, $index, $slotsNeeded, $date, $capacity)) {
                $availableSlots[] = $slot;
            }
        }

        return $availableSlots;
    }

    /**
     * Vérifie si X créneaux consécutifs sont disponibles
     */
    private function hasConsecutiveSlotsAvailable(array $slots, int $startIndex, int $slotsNeeded, string $date, array $capacity)
    {
        for ($i = 0; $i < $slotsNeeded; $i++) {
            $slotIndex = $startIndex + $i;

            // Vérifier qu'il reste assez de créneaux
            if (!isset($slots[$slotIndex])) {
                return false;
            }

            $slotTime = $slots[$slotIndex];

            // Compter les réservations à ce créneau
            $reservationsCount = Reservation::where('scheduled_date', $date)
                ->where('scheduled_time', $slotTime)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->count();

            // Vérifier si capacité disponible
            if ($reservationsCount >= $capacity['online_capacity']) {
                return false;
            }
        }

        return true;
    }
}