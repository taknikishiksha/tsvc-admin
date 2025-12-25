<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recruiter;
use App\Models\RecruiterTeam;

class ContactController extends Controller
{
    public function recruitment()
    {
        $states = [
            'Andaman and Nicobar Islands', 'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chandigarh',
            'Chhattisgarh', 'Dadra and Nagar Haveli and Daman and Diu', 'Delhi', 'Goa', 'Gujarat', 'Haryana',
            'Himachal Pradesh', 'Jammu and Kashmir', 'Jharkhand', 'Karnataka', 'Kerala', 'Ladakh', 'Lakshadweep',
            'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Puducherry',
            'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal'
        ];

        sort($states);

        // Fetch recruiters
        $recruiters = Recruiter::all()->keyBy('state');

        // Fetch team data with unique members
        $team = RecruiterTeam::all()
            ->groupBy('state')
            ->map(function ($members) {
                return $members->unique('member_name') // डुप्लिकेट्स हटाएँ
                    ->map(function ($member) {
                        return [
                            'name' => $member->member_name,
                            'phone' => $member->contact_number,
                            'role' => $member->role ?? 'Team Member'
                        ];
                    })->values()->toArray();
            })->toArray();

        return view('pages.contact.recruitment-cell', compact('states', 'recruiters', 'team'));
    }
}