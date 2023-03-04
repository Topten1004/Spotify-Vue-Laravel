<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->text('cover');
            $table->bigInteger('download_count')->default(0);
            $table->text('source');
            $table->string('source_format');
            $table->string('duration')->nullable();
            $table->string('uploaded_by');
            $table->integer('user_id');
            $table->integer('artist_id');
            $table->bigInteger('file_size')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('public')->default(0);

            // update V2.1
            $table->boolean('isProduct')->default(0);
            $table->boolean('isExclusive')->default(0);
            $table->boolean('isExplicit')->default(0);
            //
            $table->boolean('rank_on_album')->nullable();
            $table->integer('album_id')->unsigned()->nullable();
            $table->timestamps();
        });
        \DB::statement("ALTER TABLE songs AUTO_INCREMENT = 1687415;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
}
