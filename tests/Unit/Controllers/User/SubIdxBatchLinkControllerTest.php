<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubIdxBatchLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @var SubIdxBatch $subIdxBatch */
    private $subIdxBatch;

    public function settingUp()
    {
        $this->subIdxBatch = $this->createSubIdxBatch();
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

    private function postLink($subIdxBatch, $subId, $idxId)
    {
        return $this->post(route('user.subIdxBatch.link', $subIdxBatch), [
            'sub' => $subId,
            'idx' => $idxId,
        ]);
    }
}
