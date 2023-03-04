<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
        {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('page_id');
            $table->string('endpoint')->nullable();
            $table->string('source')->default('*');
            $table->string('layout')->default('section');
            // $table->string('item_layout')->nullable();
            $table->integer('rank')->default(0);
            $table->integer('nb_labels')->default(10);
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
        Schema::dropIfExists('sections');
    }
}
