<?php

namespace Database\Factories;

use App\Models\YogaTeacher;
use App\Models\TeacherVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherVerificationFactory extends Factory
{
    protected $model = TeacherVerification::class;

    public function definition()
    {
        return [
            'teacher_id' => YogaTeacher::factory(),
            'document_type' => $this->faker->randomElement([
                'ycb_certificate', 'police_verification', 'id_proof', 'education_certificate'
            ]),
            'document_path' => 'documents/' . $this->faker->uuid . '.pdf',
            'status' => 'pending',
            'submitted_at' => now(),
            'verified_by' => null,
            'verified_at' => null,
            'rejection_reason' => null,
            'resubmission_instructions' => null,
            'admin_notes' => null,
            'document_number' => $this->faker->bothify('??##??##??'),
        ];
    }

    public function pending()
    {
        return $this->state([
            'status' => 'pending',
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function verified()
    {
        return $this->state([
            'status' => 'verified',
            'verified_by' => User::factory()->create()->id,
            'verified_at' => now(),
        ]);
    }

    public function rejected()
    {
        return $this->state([
            'status' => 'rejected',
            'verified_by' => User::factory()->create()->id,
            'verified_at' => now(),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function ycbCertificate()
    {
        return $this->state(['document_type' => 'ycb_certificate']);
    }

    public function policeVerification()
    {
        return $this->state(['document_type' => 'police_verification']);
    }

    public function idProof()
    {
        return $this->state(['document_type' => 'id_proof']);
    }
}