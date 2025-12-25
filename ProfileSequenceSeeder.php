<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ProfileSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $i = 1;
        User::where('profile_sequence', 9999)->orderBy('id')->chunk(100, function($users) use (&$i) {
            foreach($users as $u) {
                $u->profile_sequence = $i * 10;
                $u->save();
                $i++;
            }
        });
    }
}
