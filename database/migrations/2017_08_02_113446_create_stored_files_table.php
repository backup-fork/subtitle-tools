<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoredFilesTable extends Migration
{
    public function up()
    {
        Schema::create('stored_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hash')->unique();
            $table->string('storage_file_path');
            $table->timestamps();
        });
    }
}
