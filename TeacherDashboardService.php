<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class TeacherDashboardService
{
    public function getDashboardData($teacherId)
    {
        return [
            'total_earnings'     => $this->getTotalEarnings($teacherId),
            'pending_payments'   => $this->getPendingPayments($teacherId),
            'completed_sessions' => $this->getCompletedSessions($teacherId),
            'avg_rating'         => $this->getAverageRating($teacherId),

            // Charts
            'earnings_chart'     => $this->getMonthlyEarningsChart($teacherId),
            'service_chart'      => $this->getServiceTypeBreakdown($teacherId),
            'sessions_chart'     => $this->getSessionTrend($teacherId),

            // Performance summary
            'performance'        => $this->getPerformanceStats($teacherId),

            // Upcoming classes
            'upcoming_classes'   => $this->getUpcomingClasses($teacherId),
        ];
    }


    /* ===============================
       BASIC STATS
    ================================ */

    public function getTotalEarnings($teacherId)
    {
        return Payment::where('teacher_id', $teacherId)
            ->where('payout_status', 'paid')
            ->sum('net_teacher_share') ?? 0;
    }

    public function getPendingPayments($teacherId)
    {
        return Payment::where('teacher_id', $teacherId)
            ->where('payout_status', 'pending')
            ->sum('net_teacher_share') ?? 0;
    }

    public function getCompletedSessions($teacherId)
    {
        return Service::where('teacher_id', $teacherId)
            ->where('status', 'completed')
            ->count();
    }

    public function getAverageRating($teacherId)
    {
        return round(
            Service::where('teacher_id', $teacherId)
                ->whereNotNull('rating')
                ->avg('rating') ?? 0,
            1
        );
    }


    /* ===============================
       PERFORMANCE STATS
    ================================ */

    public function getPerformanceStats($teacherId)
    {
        return [
            'monthly_earnings' => Payment::where('teacher_id', $teacherId)
                ->where('payout_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('net_teacher_share') ?? 0,

            'completed_sessions' => $this->getCompletedSessions($teacherId),

            'total_clients' => Service::where('teacher_id', $teacherId)
                ->distinct('client_id')
                ->count('client_id'),
        ];
    }


    /* ===============================
       MONTHLY EARNINGS CHART (12 Months)
    ================================ */

    public function getMonthlyEarningsChart($teacherId)
    {
        $data = Payment::selectRaw("SUM(net_teacher_share) as total, MONTH(created_at) as month")
            ->where('teacher_id', $teacherId)
            ->where('payout_status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $d) {
            $labels[] = date('F', mktime(0, 0, 0, $d->month, 10));
            $values[] = $d->total;
        }

        return compact('labels', 'values');
    }


    /* ===============================
       SERVICE TYPE REVENUE BREAKDOWN
    ================================ */

    public function getServiceTypeBreakdown($teacherId)
    {
        $data = Payment::selectRaw("SUM(net_teacher_share) as total, service_type")
            ->where('teacher_id', $teacherId)
            ->groupBy('service_type')
            ->get();

        return [
            'labels' => $data->pluck('service_type'),
            'values' => $data->pluck('total')
        ];
    }


    /* ===============================
       SESSIONS TREND (Last 30 Days)
    ================================ */

    public function getSessionTrend($teacherId)
    {
        $data = Service::selectRaw("DATE(start_time) as date, COUNT(*) as total")
            ->where('teacher_id', $teacherId)
            ->where('status', 'completed')
            ->where('start_time', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date'),
            'values' => $data->pluck('total'),
        ];
    }


    /* ===============================
       UPCOMING CLASSES
    ================================ */

    public function getUpcomingClasses($teacherId)
    {
        return Service::where('teacher_id', $teacherId)
            ->where('status', 'confirmed')
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(10)
            ->get();
    }
}
