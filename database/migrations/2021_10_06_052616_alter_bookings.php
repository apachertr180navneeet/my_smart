<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings', 'booking_address_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('booking_address_id')->nullable();
                if (Schema::hasTable('provider_address_mappings')) {
                    $table->foreign('booking_address_id')->references('id')->on('provider_address_mappings')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
