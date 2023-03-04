<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->text('avatar');
            $table->float('funds')->default(0);
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('displayname');
            $table->string('address');
            $table->string('country');
            $table->string('email');
            $table->string('phone');
            $table->bigInteger('available_disk_space')->default(500);
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE artists AUTO_INCREMENT = 4797;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artists');
    }
}
