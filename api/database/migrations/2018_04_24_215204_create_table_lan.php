<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lan', function(Blueprint $table) {
            $table->increments('id');
            $table->dateTime('lan_start');
            $table->dateTime('lan_end');
            $table->dateTime('seat_reservation_start');
            $table->dateTime('tournament_reservation_start');
            $table->string('event_key_id'); // seats.io
            $table->string('public_key_id'); // seats.io
            $table->string('secret_key_id'); // seats.io
            $table->unsignedInteger('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lan');
    }
}
