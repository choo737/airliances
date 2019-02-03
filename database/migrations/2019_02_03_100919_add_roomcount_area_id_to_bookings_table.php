<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoomcountAreaIdToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
            $table->integer('area_id');
            $table->integer('roomcount');
        });

        if (Schema::hasColumn('bookings', 'location')) { 
            Schema::table('bookings', function (Blueprint $table) {
                //
                $table->dropColumn('location');
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
        if (Schema::hasColumn('bookings', 'area_id')) { 
            Schema::table('bookings', function (Blueprint $table) {
                //
                $table->dropColumn('area_id');
            });
        }

        if (Schema::hasColumn('bookings', 'roomcount')) { 
            Schema::table('bookings', function (Blueprint $table) {
                //
                $table->dropColumn('roomcount');
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            //
            $table->string('location');
        });
    }
}
