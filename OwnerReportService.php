<?php

namespace App\Services;

use App\Models\DemoBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OwnerReportService
{
    public function generate(Carbon $from, Carbon $to): array
    {
        // ===============================
        // DEMO FUNNEL
        // ===============================
        $totalDemos = DemoBooking::whereBetween('created_at', [$from, $to])->count();

        $completed = DemoBooking::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->count();

        $converted = DemoBooking::whereBetween('created_at', [$from, $to])
            ->where('converted_to_service', 1)
            ->count();

        $conversionRate = $completed > 0
            ? round(($converted / $completed) * 100, 2)
            : 0;

        // ===============================
        // FOLLOW-UPS
        // ===============================
        $pendingFollowUps = DemoBooking::where('follow_up_status', 'pending')->count();

        $convertedViaFollowUp = DemoBooking::where('follow_up_status', 'converted')->count();

        // ===============================
        // TOP TEACHER
        // ===============================
        $top = DemoBooking::select(
                'assigned_teacher_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN converted_to_service = 1 THEN 1 ELSE 0 END) as converted')
            )
            ->whereNotNull('assigned_teacher_id')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('assigned_teacher_id')
            ->orderByDesc('converted')
            ->first();

        $topTeacherName = '—';

        if ($top) {
            $teacher = User::find($top->assigned_teacher_id);
            $topTeacherName = $teacher?->name ?? '—';
        }

        return [
            'total_demos'           => $totalDemos,
            'completed_demos'       => $completed,
            'converted_demos'       => $converted,
            'conversion_rate'       => $conversionRate,
            'pending_followups'     => $pendingFollowUps,
            'converted_followups'   => $convertedViaFollowUp,
            'top_teacher'           => $topTeacherName,
        ];
    }
}
