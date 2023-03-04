<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(1);
            $table->boolean('upgradable')->default(1);
            $table->string('badge')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('position');
            $table->boolean('free')->default(1);
            $table->string('currency');
            $table->string('currency_symbol')->nullable();
            $table->string('interval');
            $table->string('stripe_id')->nullable();
            $table->string('paypal_id')->nullable();
            $table->integer('interval_count');
            $table->boolean('recommended');
            $table->text('displayed_features');
            $table->bigInteger('storage_space')->default(100);
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
        Schema::dropIfExists('plans');
    }
}
