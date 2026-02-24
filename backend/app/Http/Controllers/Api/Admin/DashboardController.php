<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\StationSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Stats principales du dashboard
     */
    public function index(): JsonResponse
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Réservations du jour
        $todayReservations = Reservation::whereDate('scheduled_date', $today)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $caToday = $todayReservations
            ->where('payment_status', 'paid')
            ->sum('price');

        $caYesterday = Reservation::whereDate('scheduled_date', $yesterday)
            ->where('payment_status', 'paid')
            ->whereNotIn('status', ['cancelled'])
            ->sum('price');

        $trend = $caYesterday > 0
            ? round((($caToday - $caYesterday) / $caYesterday) * 100, 1)
            : 0;

        // Taux de remplissage
        $capacity      = StationSetting::getValue('capacity_per_slot', ['online_capacity' => 2]);
        $openingHours  = StationSetting::getValue('opening_hours');
        $dayName       = strtolower($today->locale('en')->dayName);
        $hours         = $openingHours[$dayName] ?? ['open' => true, 'start' => '08:00', 'end' => '19:00'];
        $totalSlots    = $this->calculateTotalSlots($hours, $capacity['online_capacity'] ?? 2);
        $occupiedSlots = $todayReservations->count();
        $fillRate      = $totalSlots > 0 ? round(($occupiedSlots / $totalSlots) * 100) : 0;

        // Panier moyen
        $paidReservations = $todayReservations->where('payment_status', 'paid');
        $averageBasket    = $paidReservations->count() > 0
            ? round($paidReservations->avg('price'), 0)
            : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'ca_today'       => $caToday,
                'ca_yesterday'   => $caYesterday,
                'trend'          => $trend,
                'reservations'   => [
                    'total'       => $todayReservations->count(),
                    'pending'     => $todayReservations->where('status', 'pending')->count(),
                    'confirmed'   => $todayReservations->where('status', 'confirmed')->count(),
                    'in_progress' => $todayReservations->where('status', 'in_progress')->count(),
                    'completed'   => $todayReservations->where('status', 'completed')->count(),
                ],
                'fill_rate'      => $fillRate,
                'average_basket' => $averageBasket,
                'date'           => $today->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * CA sur une période
     */
    public function revenue(Request $request): JsonResponse
    {
        $request->validate([
            'period' => ['required', 'in:week,month,year'],
        ]);

        $data = match($request->period) {
            'week'  => $this->revenueByDay(7),
            'month' => $this->revenueByDay(30),
            'year'  => $this->revenueByMonth(),
        };

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Stats par service
     */
    public function services(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $from   = $period === 'month' ? Carbon::now()->startOfMonth() : Carbon::now()->startOfYear();

        $stats = Reservation::with('service')
            ->where('scheduled_date', '>=', $from)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->groupBy('service_id')
            ->map(function ($reservations) {
                $service = $reservations->first()->service;
                return [
                    'service_id'   => $service->id,
                    'service_name' => $service->name,
                    'count'        => $reservations->count(),
                    'revenue'      => $reservations->where('payment_status', 'paid')->sum('price'),
                ];
            })
            ->values();

        $total = $stats->sum('count');

        $stats = $stats->map(function ($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0;
            return $item;
        })->sortByDesc('count')->values();

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }

    private function revenueByDay(int $days): array
    {
        $result = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $ca   = Reservation::whereDate('scheduled_date', $date)
                ->where('payment_status', 'paid')
                ->whereNotIn('status', ['cancelled'])
                ->sum('price');

            $result[] = [
                'date'    => $date->format('Y-m-d'),
                'label'   => $date->format('d/m'),
                'revenue' => $ca,
            ];
        }

        return $result;
    }

    private function revenueByMonth(): array
    {
        $result = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $ca    = Reservation::whereYear('scheduled_date', $month->year)
                ->whereMonth('scheduled_date', $month->month)
                ->where('payment_status', 'paid')
                ->whereNotIn('status', ['cancelled'])
                ->sum('price');

            $result[] = [
                'date'    => $month->format('Y-m'),
                'label'   => $month->format('M Y'),
                'revenue' => $ca,
            ];
        }

        return $result;
    }

    private function calculateTotalSlots(array $hours, int $capacity): int
    {
        if (!($hours['is_open'] ?? true)) return 0;

        $start     = Carbon::parse($hours['start'] ?? '08:00');
        $end       = Carbon::parse($hours['end'] ?? '19:00');
        $slotCount = $start->diffInMinutes($end) / 30;

        return (int) ($slotCount * $capacity);
    }
}
