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
        if (Schema::hasTable('booking_activities')) {
            Schema::table('booking_activities', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_activities', 'datetime')) {
                    $table->dateTime('datetime')->nullable()->after('booking_id');
                }
                if (!Schema::hasColumn('booking_activities', 'activity_type')) {
                    $table->string('activity_type')->nullable()->after('datetime');
                }
                if (!Schema::hasColumn('booking_activities', 'activity_message')) {
                    $table->text('activity_message')->nullable()->after('activity_type');
                }
                if (!Schema::hasColumn('booking_activities', 'activity_data')) {
                    $table->json('activity_data')->nullable()->after('activity_message');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_activities')) {
            Schema::table('booking_activities', function (Blueprint $table) {
                if (Schema::hasColumn('booking_activities', 'datetime')) {
                    $table->dropColumn('datetime');
                }
                if (Schema::hasColumn('booking_activities', 'activity_type')) {
                    $table->dropColumn('activity_type');
                }
                if (Schema::hasColumn('booking_activities', 'activity_message')) {
                    $table->dropColumn('activity_message');
                }
                if (Schema::hasColumn('booking_activities', 'activity_data')) {
                    $table->dropColumn('activity_data');
                }
            });
        }
    }
};
