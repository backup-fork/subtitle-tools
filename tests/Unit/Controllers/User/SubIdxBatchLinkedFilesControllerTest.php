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
            ->assertSee($batchFile->sub_original_name);
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
    function show_the_linked_page_for_an_empty_batch()
    {

    }

    /** @test */
    function show_the_linked_page_when_all_files_are_unlinked()
    {

    }

    /** @test */
    function show_the_linked_page_when_some_files_are_unlinked()
    {

    }

    /** @test */
    function it_can_unlink_a_batch_file()
    {
        $batchFile = $this->createSubIdxBatchFile($this->subIdxBatch);

        $this->assertCount(0, $this->subIdxBatch->unlinkedFiles);

        $this->actingAs($this->subIdxBatch->user)
            ->postUnlink($batchFile)
            ->assertSessionHasNoErrors()
            ->assertStatus(302)
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
