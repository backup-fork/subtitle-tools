<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class SubIdxBatchUploadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();
    }

    /** @test */
    function you_can_only_upload_to_your_own_batches()
    {
        $anotherUser = $this->createUser();

        $this->actingAs($anotherUser)
            ->postUpload($this->subIdxBatch, [])
            ->assertStatus(403);
    }

    /** @test */
    function you_cant_upload_to_a_batch_that_has_already_started()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);

        $this->actingAs($subIdxBatch->user)
            ->postUpload($subIdxBatch, [])
            ->assertStatus(422);
    }

    /** @test */
    function it_redirects_if_the_batch_has_already_started()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);

        $this->actingAs($subIdxBatch->user)
            ->showUpload($subIdxBatch)
            ->assertRedirect(route('user.subIdxBatch.show', $subIdxBatch));
    }

    /** @test */
    function it_shows_the_upload_page()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->showUpload($this->subIdxBatch)
            ->assertStatus(200);
    }

    /** @test */
    function it_matches_sub_and_idx_based_on_file_name()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postUpload($this->subIdxBatch, [
                'sub-idx/only-danish.sub',
                'sub-idx/ara.idx',
                'sub-idx/only-danish.idx',
                'sub-idx/ara.sub',
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(200);

        $this->assertCount(0, $this->subIdxBatch->refresh()->unlinkedFiles);

        $this->assertCount(2, $subIdxBatchFiles = $this->subIdxBatch->files);

        $this->assertSame('ara', $subIdxBatchFiles[0]->sub_original_name);
        $this->assertSame('ara', $subIdxBatchFiles[0]->idx_original_name);

        $this->assertSame('only-danish', $subIdxBatchFiles[1]->sub_original_name);
        $this->assertSame('only-danish', $subIdxBatchFiles[1]->idx_original_name);
    }

    /** @test */
    function it_stores_files_it_cant_match_as_raw_files()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postUpload($this->subIdxBatch, [
                'sub-idx/only-danish.sub',
                'sub-idx/ara.idx',
                'archives/zip/one-srt.zip',
            ])
            ->assertStatus(200);

        $this->assertCount(0, $this->subIdxBatch->refresh()->files);

        $this->assertCount(2, $unlinkedFiles = $this->subIdxBatch->unlinkedFiles);

        $this->assertSame('ara', $unlinkedFiles[0]->original_name);
        $this->assertSame('only-danish', $unlinkedFiles[1]->original_name);

        Storage::assertExists($unlinkedFiles[0]->storage_file_path);
        Storage::assertExists($unlinkedFiles[1]->storage_file_path);
    }

    /** @test */
    function it_wont_upload_duplicate_unlinked_files_to_a_batch()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postUpload($this->subIdxBatch, [
                'sub-idx/ara.idx',
                'sub-idx/ara.idx',
            ])
            ->assertStatus(200)
            ->assertViewHas('duplicateUnlinkedNames', ['ara.idx']);

        $this->assertCount(1, $this->subIdxBatch->unlinkedFiles);
    }

    /** @test */
    function it_wont_upload_duplicate_linked_files_to_a_batch()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postUpload($this->subIdxBatch, [
                'sub-idx/ara.sub',
                'sub-idx/ara.idx',
                'sub-idx/ara-dupe.sub',
                'sub-idx/ara-dupe.idx',
            ])
            ->assertStatus(200)
            ->assertViewHas('duplicateLinkedNames', ['ara-dupe']);

        $this->assertCount(0, $this->subIdxBatch->unlinkedFiles);

        $this->assertCount(1, $this->subIdxBatch->refresh()->files);
    }

    /** @test */
    function it_does_not_store_invalid_files()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postUpload($this->subIdxBatch, [
                'archives/zip/one-srt.zip',
            ])
            ->assertStatus(200)
            ->assertViewHas(['invalidNames' => ['one-srt.zip']]);

        $this->assertCount(0, $this->subIdxBatch->refresh()->files);
        $this->assertCount(0, $this->subIdxBatch->unlinkedFiles);
    }

    /** @test */
    function it_always_matches_if_you_upload_one_sub_and_one_idx()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->postUpload($this->subIdxBatch, [
                'sub-idx/only-danish.sub',
                'sub-idx/ara.idx',
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(200);

        $this->assertCount(0, $this->subIdxBatch->refresh()->unlinkedFiles);

        $this->assertCount(1, $subIdxBatchFiles = $this->subIdxBatch->files);
    }

    /** @test */
    function it_wont_store_the_same_sub_file_twice()
    {
    }

    private function showUpload($subIdxBatch)
    {
        return $this->get(route('user.subIdxBatch.showUpload', $subIdxBatch));
    }

    private function postUpload($subIdxBatch, array $files)
    {
        return $this->post(route('user.subIdxBatch.upload', $subIdxBatch), [
            'files' => $this->createUploadedFiles($files),
        ]);
    }
}
