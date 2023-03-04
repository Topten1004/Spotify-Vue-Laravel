<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('spotify_link')->nullable();
            $table->string('soundcloud_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('itunes_link')->nullable();
            $table->string('deezer_link')->nullable();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->string('spotify_link')->nullable();
            $table->string('soundcloud_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('itunes_link')->nullable();
            $table->string('deezer_link')->nullable();
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('spotify_link')->nullable();
            $table->string('soundcloud_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('itunes_link')->nullable();
            $table->string('deezer_link')->nullable();
        });

        Schema::table('podcasts', function (Blueprint $table) {
            $table->string('spotify_link')->nullable();
            $table->string('soundcloud_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('itunes_link')->nullable();
            $table->string('deezer_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_link',
                'soundcloud_link',
                'youtube_link',
                'itunes_link',
                'deezer_link'
            ]);
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_link',
                'soundcloud_link',
                'youtube_link',
                'itunes_link',
                'deezer_link'
            ]);
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_link',
                'soundcloud_link',
                'youtube_link',
                'itunes_link',
                'deezer_link'
            ]);
        });

        Schema::table('podcasts', function (Blueprint $table) {
            $table->dropColumn([
                'spotify_link',
                'soundcloud_link',
                'youtube_link',
                'itunes_link',
                'deezer_link'
            ]);
        });
    }
}
