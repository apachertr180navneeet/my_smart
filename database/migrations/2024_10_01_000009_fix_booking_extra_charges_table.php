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
        if (Schema::hasTable('booking_extra_charges')) {
            Schema::table('booking_extra_charges', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_extra_charges', 'deleted_at')) {
                    $table->softDeletes();
                }
                if (!Schema::hasColumn('booking_extra_charges', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_extra_charges')) {
            Schema::table('booking_extra_charges', function (Blueprint $table) {
                if (Schema::hasColumn('booking_extra_charges', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
