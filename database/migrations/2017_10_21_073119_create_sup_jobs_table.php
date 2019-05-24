<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupJobsTable extends Migration
{
    public function up()
    {
        Schema::create('sup_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url_key')->unique();
            $table->string('original_name');
            $table->string('ocr_language');
            $table->unsignedInteger('input_stored_file_id')->nullable();
            $table->string('input_file_hash');
            $table->unsignedInteger('output_stored_file_id')->nullable();
            $table->string('error_message')->nullable();
            $table->string('internal_error_message')->nullable();
            $table->string('temp_dir')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('queue_time')->nullable();
            $table->integer('extract_time')->nullable();
            $table->integer('work_time')->nullable();
            $table->unsignedInteger('cache_hits')->default(0);
            $table->dateTime('last_cache_hit')->nullable();
            $table->timestamps();

            $table->foreign('input_stored_file_id')->references('id')->on('stored_files');
            $table->foreign('output_stored_file_id')->references('id')->on('stored_files');

            $table->unique(['ocr_language', 'input_file_hash']);
        });
    }
}
