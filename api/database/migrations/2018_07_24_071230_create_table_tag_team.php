<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTagTeam extends Migration
{
    /**
     * Exécuter les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_team', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('team_id');
            $table->boolean('is_leader')->default(false);
            $table->timestamps();

            $table->foreign('tag_id')
                ->references('id')->on('tag');
            $table->foreign('team_id')
                ->references('id')->on('team');
        });
    }

    /**
     * Inverser les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_team');
    }
}
