<?php

namespace Tests\Unit\Controllers\Admin;

use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Models\StoredFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoredFilesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_a_stored_file_without_meta()
    {
        $storedFile = $this->createStoredFile();

        $this->adminLogin()
            ->get(route('admin.storedFiles.show', $storedFile))
            ->assertStatus(200);
    }

    /** @test */
    function it_can_show_a_stored_file_with_meta()
    {
        $storedFile = $this->createStoredFile();

        (new CollectStoredFileMetaJob($storedFile))->handle();

        $this->adminLogin()
            ->get(route('admin.storedFiles.show', $storedFile))
            ->assertStatus(200);
    }

    /** @test */
    function it_can_wipe_stored_files()
    {
        $s1 = factory(StoredFile::class)->create();
        $s2 = factory(StoredFile::class)->create();

        $this->adminLogin()
            ->delete(route('admin.storedFiles.delete'), ['id' => $s2->id])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $this->assertNotNull(StoredFile::find($s1->id));
        $this->assertNotNull($fresh = StoredFile::find($s2->id));

        $this->assertSame('((deleted))', file_get_contents($fresh->file_path));
    }
}
