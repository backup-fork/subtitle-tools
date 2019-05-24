<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubIdxBatchUnlinkedFilesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();
    }

    /** @test */
    function it_will_only_show_your_own_batches()
    {
        $anotherBatch = $this->createSubIdxBatch();

        $this->actingAs($this->subIdxBatch->user)
            ->showUnlinked($anotherBatch)
            ->assertStatus(403);
    }

    /** @test */
    function it_redirects_if_the_batch_has_already_started()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);

        $this->actingAs($subIdxBatch->user)
            ->showUnlinked($subIdxBatch)
            ->assertRedirect(route('user.subIdxBatch.show', $subIdxBatch));
    }

    /** @test */
    function show_the_unlinked_page_for_an_empty_batch()
    {
        $this->actingAs($this->subIdxBatch->user)
            ->showUnlinked($this->subIdxBatch)
            ->assertStatus(200);
    }

    /** @test */
    function show_the_unlinked_page_when_all_files_are_linked()
    {
    }

    /** @test */
    function show_the_unlinked_page_when_some_files_are_unlinked()
    {
    }

    /** @test */
    function you_can_only_link_files_in_your_own_batches()
    {
        $anotherUser = $this->createUser();

        $this->actingAs($anotherUser)
            ->postLink($this->subIdxBatch, '', '')
            ->assertStatus(403);
    }

    /** @test */
    function you_cant_link_when_the_batch_has_already_started()
    {
        $subIdxBatch = $this->createSubIdxBatch(['started_at' => now()]);

        $sub = $this->createUnlinkedBatchFile_sub($subIdxBatch);
        $idx = $this->createUnlinkedBatchFile_idx($subIdxBatch);

        $this->actingAs($subIdxBatch->user)
            ->postLink($subIdxBatch, $sub->id, $idx->id)
            ->assertStatus(422);
    }

    /** @test */
    function it_will_only_link_files_that_belong_to_the_batch()
    {
        $anotherBatch = $this->createSubIdxBatch($this->subIdxBatch->user);

        $sub = $this->createUnlinkedBatchFile_sub($anotherBatch);
        $idx = $this->createUnlinkedBatchFile_idx($anotherBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->postLink($this->subIdxBatch, $sub->id, $idx->id)
            ->assertSessionHasNoErrors()
            ->assertStatus(422);
    }

    /** @test */
    function it_wont_link_sub_with_sub_files()
    {
        $sub1 = $this->createUnlinkedBatchFile_sub($this->subIdxBatch);
        $sub2 = $this->createUnlinkedBatchFile_sub($this->subIdxBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->postLink($this->subIdxBatch, $sub1->id, $sub2->id)
            ->assertSessionHasNoErrors()
            ->assertStatus(422);
    }

    /** @test */
    function it_links_sub_and_idx_files()
    {
        $sub = $this->createUnlinkedBatchFile_sub($this->subIdxBatch);
        $idx = $this->createUnlinkedBatchFile_idx($this->subIdxBatch);

        $this->actingAs($this->subIdxBatch->user)
            ->postLink($this->subIdxBatch, $sub->id, $idx->id)
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $this->assertModelDoesntExist($sub);
        $this->assertModelDoesntExist($idx);

        $this->assertCount(1, $this->subIdxBatch->files);

        /** @var SubIdxBatchFile $batchFile */
        $batchFile = $this->subIdxBatch->files->first();

        $this->assertSame($sub->original_name, $batchFile->sub_original_name);
        $this->assertSame($idx->original_name, $batchFile->idx_original_name);

        Storage::assertMissing($sub->storage_file_path);
        Storage::assertMissing($idx->storage_file_path);

        Storage::assertExists($batchFile->sub_storage_file_path);
        Storage::assertExists($batchFile->idx_storage_file_path);
    }

    private function showUnlinked($subIdxBatch)
    {
        return $this->get(route('user.subIdxBatch.showUnlinked', $subIdxBatch));
    }

    private function postLink($subIdxBatch, $subId, $idxId)
    {
        return $this->post(route('user.subIdxBatch.link', $subIdxBatch), [
            'sub' => $subId,
            'idx' => $idxId,
        ]);
    }
}
