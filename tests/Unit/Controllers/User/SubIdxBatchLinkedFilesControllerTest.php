<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubIdxBatchLinkedFilesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();
    }

    /** @test */
    function it_shows_your_linked_files()
    {
        [$batchFile, , ] = $this->createSubIdxBatchFiles(3, $this->subIdxBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->showLinked($this->subIdxBatch)
            ->assertStatus(200)
            ->assertSee(e($batchFile->sub_original_name));
    }

    /** @test */
    function it_will_only_show_your_own_batches()
    {
        $anotherBatch = $this->createSubIdxBatch();

        $this->actingAs($this->subIdxBatch->user)
            ->showLinked($anotherBatch)
            ->assertStatus(403);
    }

    /** @test */
    function it_redirects_if_the_batch_has_already_started()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);

        $this->actingAs($subIdxBatch->user)
            ->showLinked($subIdxBatch)
            ->assertRedirect(route('user.subIdxBatch.show', $subIdxBatch));
    }

    /** @test */
    function show_the_linked_page_for_an_empty_batch()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->showLinked($this->subIdxBatch)
            ->assertStatus(200)
            ->assertDontSee('xlink:href="#svg-unlink"');
    }

    /** @test */
    function show_the_linked_page_when_all_files_are_unlinked()
    {
        $this->createUnlinkedBatchFile_sub($this->subIdxBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->showLinked($this->subIdxBatch)
            ->assertStatus(200)
            ->assertDontSee('xlink:href="#svg-unlink"');
    }

    /** @test */
    function show_the_linked_page_when_some_files_are_unlinked()
    {
        $this->createUnlinkedBatchFile_sub($this->subIdxBatch);

        $this->createSubIdxBatchFile($this->subIdxBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->showLinked($this->subIdxBatch)
            ->assertStatus(200)
            ->assertSee('xlink:href="#svg-unlink"');
    }

    /** @test */
    function it_removes_sub_files_when_unlinking_files_that_already_exist_as_unlinked_file()
    {
        $batchFile = $this->createSubIdxBatchFile($this->subIdxBatch);

        $batchFile->update(['sub_hash' => 'abcdef123']);

        $this->createUnlinkedBatchFile_sub($this->subIdxBatch)->update(['hash' => 'abcdef123']);

        $this->actingAs($this->subIdxBatch->user)
            ->postUnlink($batchFile)
            ->assertSessionHasNoErrors()
            ->assertStatus(200)
            ->assertDontSee($batchFile->sub_original_name)
            ->assertViewHas('subAlreadyExistsAsUnlinked', true)
            ->assertViewHas('idxAlreadyExistsAsUnlinked', false);

        $this->assertModelDoesntExist($batchFile);

        $this->subIdxBatch->refresh();

        $this->assertCount(2, $this->subIdxBatch->unlinkedFiles);

        $unlinkedIdx = $this->subIdxBatch->unlinkedFiles->where('original_name', $batchFile->idx_original_name)->first();

        Storage::assertExists($unlinkedIdx->storage_file_path);

        Storage::assertMissing($batchFile->sub_storage_file_path);
        Storage::assertMissing($batchFile->idx_storage_file_path);
    }

    /** @test */
    function it_removes_idx_files_when_unlinking_files_that_already_exist_as_unlinked_file()
    {
        $batchFile = $this->createSubIdxBatchFile($this->subIdxBatch);

        $batchFile->update(['idx_hash' => 'abcdef123']);

        $this->createUnlinkedBatchFile_idx($this->subIdxBatch)->update(['hash' => 'abcdef123']);

        $this->actingAs($this->subIdxBatch->user)
            ->postUnlink($batchFile)
            ->assertSessionHasNoErrors()
            ->assertStatus(200)
            ->assertDontSee($batchFile->sub_original_name)
            ->assertViewHas('subAlreadyExistsAsUnlinked', false)
            ->assertViewHas('idxAlreadyExistsAsUnlinked', true);

        $this->assertModelDoesntExist($batchFile);

        $this->subIdxBatch->refresh();

        $this->assertCount(2, $this->subIdxBatch->unlinkedFiles);

        $unlinkedIdx = $this->subIdxBatch->unlinkedFiles->where('original_name', $batchFile->sub_original_name)->first();

        Storage::assertExists($unlinkedIdx->storage_file_path);

        Storage::assertMissing($batchFile->sub_storage_file_path);
        Storage::assertMissing($batchFile->idx_storage_file_path);
    }

    /** @test */
    function you_cant_unlink_when_the_batch_has_already_started()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);

        $batchFile = $this->createSubIdxBatchFile($subIdxBatch);

        $this->actingAs($subIdxBatch->user)
            ->postUnlink($batchFile)
            ->assertStatus(422);
    }

    /** @test */
    function it_can_unlink_a_batch_file()
    {
        $batchFile = $this->createSubIdxBatchFile($this->subIdxBatch);

        $this->assertCount(0, $this->subIdxBatch->unlinkedFiles);

        $this->actingAs($this->subIdxBatch->user)
            ->postUnlink($batchFile)
            ->assertSessionHasNoErrors()
            ->assertStatus(200)
            ->assertDontSee($batchFile->sub_original_name);

        $this->assertModelDoesntExist($batchFile);

        $this->subIdxBatch->refresh();

        $this->assertCount(2, $this->subIdxBatch->unlinkedFiles);

        $unlinkedSub = $this->subIdxBatch->unlinkedFiles->where('original_name', $batchFile->sub_original_name)->first();
        $unlinkedIdx = $this->subIdxBatch->unlinkedFiles->where('original_name', $batchFile->idx_original_name)->first();

        $this->assertTrue($unlinkedSub->is_sub);
        $this->assertFalse($unlinkedIdx->is_sub);

        Storage::assertExists($unlinkedSub->storage_file_path);
        Storage::assertExists($unlinkedIdx->storage_file_path);

        Storage::assertMissing($batchFile->sub_storage_file_path);
        Storage::assertMissing($batchFile->idx_storage_file_path);
    }

    private function showLinked($subIdxBatch)
    {
        return $this->get(route('user.subIdxBatch.showLinked', $subIdxBatch));
    }

    private function postUnlink($batchFile)
    {
        return $this->post(route('user.subIdxBatch.unlink', $batchFile));
    }
}
