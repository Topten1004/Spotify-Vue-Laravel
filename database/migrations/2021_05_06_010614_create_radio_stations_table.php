<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRadioStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radio_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('cover');
            $table->string('streamEndpoint');
            $table->boolean('highlight')->default(0);
            $table->boolean('proxy')->default(0);
            $table->string('serverType');
            $table->string('statsSource');
            $table->string('serverURL')->nullable();
            $table->string('serverID')->nullable();
            $table->string('serverMount')->nullable();
            $table->string('serverUsername')->nullable();
            $table->string('serverPassword')->nullable();
            $table->string('statsEndpoint')->nullable();
            $table->bigInteger('interval');
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
        Schema::dropIfExists('radio_stations');
    }
}
