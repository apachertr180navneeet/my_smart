<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('booking_statuses')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `booking_statuses` MODIFY `name` VARCHAR(255) NULL");
            }
            Schema::table('booking_statuses', function (Blueprint $table) {
                if (!Schema::hasColumn('booking_statuses', 'value')) {
                    $table->string('value')->nullable()->after('id');
                }
                if (!Schema::hasColumn('booking_statuses', 'label')) {
                    $table->string('label')->nullable()->after('value');
                }
                if (!Schema::hasColumn('booking_statuses', 'sequence')) {
                    $table->integer('sequence')->default(0)->after('label');
                }
            });
        }

        if (Schema::hasTable('bookings')) {
            if (DB::getDriverName() === 'mysql') {
                // Modify status column to varchar(255)
                DB::statement("ALTER TABLE `bookings` MODIFY `status` VARCHAR(255) DEFAULT 'pending'");
                // Modify tax column to nullable
                DB::statement("ALTER TABLE `bookings` MODIFY `tax` DOUBLE NULL DEFAULT 0");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_statuses')) {
            Schema::table('booking_statuses', function (Blueprint $table) {
                if (Schema::hasColumn('booking_statuses', 'value')) {
                    $table->dropColumn('value');
                }
                if (Schema::hasColumn('booking_statuses', 'label')) {
                    $table->dropColumn('label');
                }
                if (Schema::hasColumn('booking_statuses', 'sequence')) {
                    $table->dropColumn('sequence');
                }
            });
        }
    }
};
