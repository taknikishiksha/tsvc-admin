<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recruiter;
use App\Models\RecruiterTeam;

class RecruiterSeeder extends Seeder
{
    public function run()
    {
        Recruiter::create([
            'state' => 'Delhi',
            'name' => 'Vijay Kumar',
            'contact_number' => '9841300800',
            'email' => 'hr@taknikishiksha.org.in',
        ]);
        RecruiterTeam::insert([
            [
                'state' => 'Delhi',
                'member_name' => 'Yogita Sharma',
                'role' => 'HR Manager',
                'contact_number' => '8010030620',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'state' => 'Delhi',
                'member_name' => 'Ranvijay Mishra',
                'role' => 'HR Coordinator',
                'contact_number' => '8052000207',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'state' => 'Delhi',
                'member_name' => 'Avnish Mishra',
                'role' => 'HR Coordinator',
                'contact_number' => '8052000929',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
