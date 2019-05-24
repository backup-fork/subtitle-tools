<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiskUsagesTable extends Migration
{
    public function up()
    {
        Schema::create('disk_usages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('total_size');
            $table->unsignedBigInteger('total_used');
            $table->unsignedBigInteger('stored_files_dir_size');
            $table->unsignedBigInteger('sub_idx_dir_size');
            $table->unsignedBigInteger('temp_dirs_dir_size');
            $table->unsignedBigInteger('temp_files_dir_size');
            $table->timestamps();
        });
    }
}
