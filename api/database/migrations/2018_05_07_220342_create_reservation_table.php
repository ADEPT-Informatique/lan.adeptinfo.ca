<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->unsignedInteger('lan_id');
            $table->unsignedInteger('user_id');
            $table->string('seat_id');
            $table->timestamps();

            $table->primary(['lan_id', 'user_id']);
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('lan_id')->references('id')->on('lan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation');
    }
}
