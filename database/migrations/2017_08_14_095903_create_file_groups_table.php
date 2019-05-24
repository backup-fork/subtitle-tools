<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('file_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tool_route');
            $table->string('url_key')->unique();
            $table->text('job_options')->nullable();
            $table->dateTime('file_jobs_finished_at')->nullable();
            $table->dateTime('archive_requested_at')->nullable();
            $table->dateTime('archive_finished_at')->nullable();
            $table->string('archive_error')->nullable();
            $table->unsignedInteger('archive_stored_file_id')->nullable();
            $table->timestamps();

            $table->foreign('archive_stored_file_id')->references('id')->on('stored_files');
        });
    }
}
