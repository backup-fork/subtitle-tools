<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubIdxStatsTable extends Migration
{
    public function up()
    {
        Schema::create('sub_idx_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->unique();
            $table->unsignedInteger('cache_hits')->default(0);
            $table->unsignedInteger('cache_misses')->default(0);
            $table->unsignedBigInteger('total_file_size')->default(0);
            $table->unsignedInteger('images_ocrd_count')->default(0);
            $table->unsignedBigInteger('milliseconds_spent_ocring')->default(0);
            $table->timestamps();
        });

        Schema::create('sub_idx_language_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language')->unique();
            $table->unsignedInteger('times_seen')->default(0);
            $table->unsignedInteger('times_extracted')->default(0);
            $table->unsignedInteger('times_failed')->default(0);
            $table->timestamps();
        });
    }
}
