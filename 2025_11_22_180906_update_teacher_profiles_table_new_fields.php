<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only alter if table exists
        if (! Schema::hasTable('teacher_profiles')) {
            return;
        }

        // Use Schema::table with checks to avoid errors on repeated runs
        Schema::table('teacher_profiles', function (Blueprint $table) {
            // NOTE: Schema::hasColumn cannot be reliably used inside the closure on some DB drivers,
            // so we will collect intended changes and apply them conditionally after checking.
        });

        // Apply column additions with separate checks to avoid issues on some hosts
        if (! Schema::hasColumn('teacher_profiles', 'specializations')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->json('specializations')->nullable()->after('bio');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'languages')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->json('languages')->nullable()->after('specializations');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'certifications')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->json('certifications')->nullable()->after('languages');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'locations_covered')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->json('locations_covered')->nullable()->after('certifications');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'max_clients')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                // default 5
                $table->integer('max_clients')->default(5)->after('locations_covered');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'working_days')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->json('working_days')->nullable()->after('max_clients');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'service_types')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->json('service_types')->nullable()->after('working_days');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'shift_start')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->time('shift_start')->nullable()->after('service_types');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'shift_end')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->time('shift_end')->nullable()->after('shift_start');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'ycb_certified')) {
            Schema::table('teacher_profiles', function (Blueprint $table) {
                $table->boolean('ycb_certified')->default(0)->after('shift_end');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('teacher_profiles')) {
            return;
        }

        // Drop columns only if they exist (reverse of up)
        Schema::table('teacher_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_profiles', 'ycb_certified')) {
                $table->dropColumn('ycb_certified');
            }
            if (Schema::hasColumn('teacher_profiles', 'shift_end')) {
                $table->dropColumn('shift_end');
            }
            if (Schema::hasColumn('teacher_profiles', 'shift_start')) {
                $table->dropColumn('shift_start');
            }
            if (Schema::hasColumn('teacher_profiles', 'service_types')) {
                $table->dropColumn('service_types');
            }
            if (Schema::hasColumn('teacher_profiles', 'working_days')) {
                $table->dropColumn('working_days');
            }
            if (Schema::hasColumn('teacher_profiles', 'max_clients')) {
                $table->dropColumn('max_clients');
            }
            if (Schema::hasColumn('teacher_profiles', 'locations_covered')) {
                $table->dropColumn('locations_covered');
            }
            if (Schema::hasColumn('teacher_profiles', 'certifications')) {
                $table->dropColumn('certifications');
            }
            if (Schema::hasColumn('teacher_profiles', 'languages')) {
                $table->dropColumn('languages');
            }
            if (Schema::hasColumn('teacher_profiles', 'specializations')) {
                $table->dropColumn('specializations');
            }
        });
    }
};
