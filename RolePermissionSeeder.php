<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // load permissions into array for convenience
        $all = Permission::pluck('name')->toArray();

        // superadmin = all permissions
        $super = Role::firstOrCreate(['name' => 'superadmin']);
        $super->syncPermissions($all);

        // admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'view-dashboard','manage-settings','export-data','view-reports',
            'manage-users','manage-pages','manage-blogs','manage-support-tickets'
        ]);

        // hr
        $hr = Role::firstOrCreate(['name' => 'hr']);
        $hr->syncPermissions(['manage-jobs','manage-applications','verify-users','issue-offer']);

        // finance
        $finance = Role::firstOrCreate(['name' => 'finance']);
        $finance->syncPermissions(['manage-payments','view-payments','manage-donations','generate-80g','manage-salary','export-data']);

        // training
        $training = Role::firstOrCreate(['name' => 'training']);
        $training->syncPermissions(['manage-courses','manage-batches','manage-enrollments','manage-lms','manage-certificates','view-reports']);

        // exam
        $exam = Role::firstOrCreate(['name' => 'exam']);
        $exam->syncPermissions(['conduct-online-exam','grade-exams','view-reports']);

        // usermgmt
        $um = Role::firstOrCreate(['name' => 'usermgmt']);
        $um->syncPermissions(['verify-users','ban-users','manage-users','manage-support-tickets']);

        // service
        $service = Role::firstOrCreate(['name' => 'service']);
        $service->syncPermissions(['assign-bookings','manage-bookings','view-reports']);

        // teacher
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->syncPermissions(['mark-attendance','request-payout','manage-blogs']); // adjust as needed

        // student & client (view-level)
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->syncPermissions(['manage-lms','manage-certificates']);

        $client = Role::firstOrCreate(['name' => 'client']);
        $client->syncPermissions(['manage-bookings','view-payments']);

        // partner / consultant / volunteer / intern / donor / corporate / affiliate
        Role::firstOrCreate(['name' => 'partner'])->syncPermissions(['manage-partners','manage-corporate-bookings']);
        Role::firstOrCreate(['name' => 'consultant'])->syncPermissions(['manage-applications']);
        Role::firstOrCreate(['name' => 'volunteer'])->syncPermissions([]);
        Role::firstOrCreate(['name' => 'intern'])->syncPermissions([]);
        Role::firstOrCreate(['name' => 'donor'])->syncPermissions(['manage-donations']);
        Role::firstOrCreate(['name' => 'corporate'])->syncPermissions(['manage-corporate-bookings']);
        Role::firstOrCreate(['name' => 'affiliate'])->syncPermissions(['manage-affiliates']);

        $this->command->info('Role -> Permission mapping applied.');
    }
}
