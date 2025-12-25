<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruiterTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('recruiter_teams', function (Blueprint $table) {
            $table->id();
            $table->string('state');
            $table->string('member_name');
            $table->string('role')->nullable();
            $table->string('contact_number')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recruiter_teams');
    }
}