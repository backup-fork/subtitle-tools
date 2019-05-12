<?php

namespace Tests;

use App\Models\FileGroup;
use App\Models\FileJob;
use App\Models\StoredFile;
use App\Models\SubIdx;
use App\Models\SupJob;
use App\Models\User;

trait CreatesModels
{
    use CreatesSubIdxBatches;

    public function createStoredFile($attributes = []): StoredFile
    {
        return factory(StoredFile::class)->create($attributes);
    }

    public function createSupJob($attributes = []): SupJob
    {
        return factory(SupJob::class)->create($attributes);
    }

    public function createFileGroup($attributes = []): FileGroup
    {
        /** @var FileGroup $fileGroup */
        $fileGroup = factory(FileGroup::class)->create($attributes);

        $fileGroup->fileJobs()->save(
            $this->makeFileJob()
        );

        return $fileGroup;
    }

    public function makeFileJob($attributes = []): FileJob
    {
        return factory(FileJob::class)->make($attributes);
    }

    public function createUser($attributes = []): User
    {
        return factory(User::class)->create($attributes + [
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
    }

    public function createAdmin($attributes = []): User
    {
        return $this->createUser(['is_admin' => true] + $attributes);
    }

    public function createSubIdx($attributes = []): SubIdx
    {
        return factory(SubIdx::class)->create($attributes);
    }

    /** @return SubIdx[] */
    public function createSubIdxes($count, $attributes = [])
    {
        return factory(SubIdx::class, $count)->create($attributes);
    }
}
