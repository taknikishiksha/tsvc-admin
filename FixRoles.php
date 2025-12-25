<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class FixRoles extends Command
{
    protected $signature = 'fix:roles';
    protected $description = 'Fix roles, permissions and assign superadmin';

    public function handle()
    {
        $this->info("---- Creating Roles ----");

        $roles = [
            'superadmin', 'admin', 'hr', 'finance', 'training', 'exam',
            'usermgmt', 'service', 'client', 'teacher', 'student', 'partner',
            'consultant', 'volunteer', 'intern', 'donor', 'corporate', 'affiliate'
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $this->info("Roles created or already exist.");

        $this->info("---- Assigning SuperAdmin Role ----");

        // change this email to your real superadmin email
        $email = "shobhitsingh@taknikishiksha.org.in";

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found with email: ".$email);
            return 1;
        }

        $role = Role::where('name', 'superadmin')->first();

        if (!$role) {
            $this->error("superadmin role not found.");
            return 1;
        }

        // If assignRole() method exists on the User model (HasRoles trait), use it.
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($role);
            $this->info("Used assignRole() method to assign superadmin to: ".$email);
        } else {
            // Fallback: insert directly into model_has_roles table
            $modelType = get_class($user); // e.g., App\Models\User
            $exists = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', $modelType)
                ->where('model_id', $user->getKey())
                ->exists();

            if ($exists) {
                $this->info("User already has superadmin role (model_has_roles entry exists).");
            } else {
                DB::table('model_has_roles')->insert([
                    'role_id'    => $role->id,
                    'model_type' => $modelType,
                    'model_id'   => $user->getKey(),
                ]);
                $this->info("Inserted model_has_roles entry to assign superadmin to: ".$email);
            }
        }

        // Reset permission cache if spatie command exists
        try {
            $this->callSilent('permission:cache-reset');
            $this->info("Permission cache reset.");
        } catch (\Exception $e) {
            $this->warn("Could not reset permission cache: " . $e->getMessage());
        }

        $this->info("Done.");
        return 0;
    }
}
