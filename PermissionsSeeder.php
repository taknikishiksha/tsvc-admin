<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Global / admin
            'view-dashboard',
            'manage-settings',
            'export-data',
            'view-reports',

            // Users & verification
            'manage-users',
            'verify-users',
            'ban-users',

            // Training / Courses
            'manage-courses',
            'manage-batches',
            'manage-enrollments',
            'manage-lms',
            'manage-certificates',

            // Teachers & Services
            'manage-teachers',
            'approve-teacher',
            'assign-bookings',
            'manage-bookings',
            'mark-attendance',
            'request-payout',
            'process-payout',

            // Recruitment & Exams
            'manage-jobs',
            'manage-applications',
            'conduct-online-exam',
            'grade-exams',
            'issue-offer',

            // Finance & NGO
            'manage-payments',
            'view-payments',
            'manage-donations',
            'generate-80g',
            'manage-salary',

            // Partners / Corporate / Affiliate
            'manage-partners',
            'manage-corporate-bookings',
            'manage-affiliates',

            // Content / Blog / Support
            'manage-blogs',
            'manage-pages',
            'manage-support-tickets',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $this->command->info('Permissions seeded: ' . count($permissions));
    }
}