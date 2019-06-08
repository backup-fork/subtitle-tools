<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubIdxBatchesTable extends Migration
{
    public function up()
    {
        Schema::create('sub_idx_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('label');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'label']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('sub_idxes', function (Blueprint $table) {
            $table->uuid('sub_idx_batch_id')->after('id')->nullable();

            $table->foreign('sub_idx_batch_id')->references('id')->on('sub_idx_batches')->onDelete('cascade');
        });

        Schema::create('sub_idx_unlinked_batch_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sub_idx_batch_id');
            $table->string('original_name', 2000);
            $table->boolean('is_sub');
            $table->string('hash');
            $table->string('storage_file_path');
            $table->timestamps();

            $table->unique(['sub_idx_batch_id', 'hash']);

            $table->foreign('sub_idx_batch_id')->references('id')->on('sub_idx_batches')->onDelete('cascade');
        });

        Schema::create('sub_idx_batch_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sub_idx_batch_id');
            $table->string('sub_original_name', 2000);
            $table->string('idx_original_name', 2000);
            $table->string('sub_hash');
            $table->string('idx_hash');
            $table->string('sub_storage_file_path');
            $table->string('idx_storage_file_path');
            $table->timestamps();

            $table->unique(['sub_idx_batch_id', 'sub_hash', 'idx_hash']);

            $table->foreign('sub_idx_batch_id')->references('id')->on('sub_idx_batches')->onDelete('cascade');
        });
    }
}
