<?php

namespace App\Services;

use App\Models\LoyaltyTransaction;
use App\Models\Reservation;
use App\Models\StationSetting;
use App\Models\User;

class LoyaltyService
{
    private function getRules(): array
    {
        return StationSetting::getValue('loyalty_rules', [
            'points_per_1000_fcfa'  => 10,
            'review_bonus_points'   => 20,
            'referral_bonus_points' => 100,
            'rewards'               => [],
        ]);
    }

    /**
     * Calculer les points gagnés pour une réservation
     */
    public function calculatePoints(float $amount): int
    {
        $rules = $this->getRules();
        return (int) floor(($amount / 1000) * $rules['points_per_1000_fcfa']);
    }

    /**
     * Créditer des points après un lavage complété
     */
    public function earnPoints(User $user, Reservation $reservation): int
    {
        $points = $this->calculatePoints($reservation->price);

        if ($points <= 0) return 0;

        $user->increment('loyalty_points', $points);

        LoyaltyTransaction::create([
            'user_id'        => $user->id,
            'reservation_id' => $reservation->id,
            'type'           => 'earned',
            'points'         => $points,
            'description'    => "Points gagnés pour la réservation #{$reservation->reservation_number}",
        ]);

        return $points;
    }

    /**
     * Appliquer une pénalité (no-show, annulation tardive)
     */
    public function applyPenalty(User $user, Reservation $reservation, int $points, string $reason): void
    {
        $deducted = min($points, $user->loyalty_points);

        if ($deducted <= 0) return;

        $user->decrement('loyalty_points', $deducted);

        LoyaltyTransaction::create([
            'user_id'        => $user->id,
            'reservation_id' => $reservation->id,
            'type'           => 'penalty',
            'points'         => -$deducted,
            'description'    => $reason,
        ]);
    }

    /**
     * Utiliser des points pour obtenir une récompense
     */
    public function redeemReward(User $user, int $rewardIndex): array
    {
        $rules   = $this->getRules();
        $rewards = $rules['rewards'] ?? [];

        if (!isset($rewards[$rewardIndex])) {
            throw new \Exception("Récompense invalide.");
        }

        $reward = $rewards[$rewardIndex];

        if ($user->loyalty_points < $reward['points']) {
            throw new \Exception("Points insuffisants. Il vous faut {$reward['points']} points.");
        }

        $user->decrement('loyalty_points', $reward['points']);

        LoyaltyTransaction::create([
            'user_id'        => $user->id,
            'reservation_id' => null,
            'type'           => 'redeemed',
            'points'         => -$reward['points'],
            'description'    => $reward['type'] === 'free_wash'
                ? "Récompense : lavage gratuit"
                : "Récompense : réduction de {$reward['value']} FCFA",
        ]);

        return $reward;
    }

    /**
     * Bonus avis client
     */
    public function addReviewBonus(User $user): int
    {
        $rules  = $this->getRules();
        $points = $rules['review_bonus_points'] ?? 20;

        $user->increment('loyalty_points', $points);

        LoyaltyTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'earned',
            'points'      => $points,
            'description' => "Bonus pour avis laissé",
        ]);

        return $points;
    }

    /**
     * Bonus parrainage
     */
    public function addReferralBonus(User $user): int
    {
        $rules  = $this->getRules();
        $points = $rules['referral_bonus_points'] ?? 100;

        $user->increment('loyalty_points', $points);

        LoyaltyTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'earned',
            'points'      => $points,
            'description' => "Bonus parrainage",
        ]);

        return $points;
    }

    /**
     * Résumé du compte fidélité d'un client
     */
    public function getSummary(User $user): array
    {
        $rules           = $this->getRules();
        $rewards         = $rules['rewards'] ?? [];
        $currentPoints   = $user->loyalty_points;

        $availableRewards = collect($rewards)
            ->filter(fn($r) => $currentPoints >= $r['points'])
            ->values();

        $nextReward = collect($rewards)
            ->filter(fn($r) => $currentPoints < $r['points'])
            ->sortBy('points')
            ->first();

        return [
            'current_points'   => $currentPoints,
            'available_rewards'=> $availableRewards,
            'next_reward'      => $nextReward ? [
                'points_needed' => $nextReward['points'] - $currentPoints,
                'reward'        => $nextReward,
            ] : null,
        ];
    }
}