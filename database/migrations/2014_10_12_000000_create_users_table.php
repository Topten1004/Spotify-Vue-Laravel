<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('avatar');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('lang')->default('en');
            $table->string('payment_method')->nullable();
            $table->integer('facebook_id')->nullable();
            $table->float('available_disk_space')->default(0);
            $table->text('is_playing')->nullable();
            $table->boolean('requested_artist_account')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->boolean('hide_activity')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE users AUTO_INCREMENT = 54564;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
