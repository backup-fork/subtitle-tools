<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadedFileMimesTable extends Migration
{
    public function up()
    {
        Schema::create('uploaded_file_mimes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uri');
            $table->string('mime');
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();
        });
    }
}
