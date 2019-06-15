<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->dateTime('email_verified_at')->nullable();
            $table->string('email_verification_token')->nullable();
            $table->unsignedInteger('batch_tokens_left')->default(0);
            $table->unsignedInteger('batch_tokens_used')->default(0);
            $table->dateTime('last_seen_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
}
