<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('user_id');
            $table->text('cover');
            $table->string('created_by');
            $table->boolean('public')->default(0);
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE playlists AUTO_INCREMENT = 17415;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playlists');
    }
}
