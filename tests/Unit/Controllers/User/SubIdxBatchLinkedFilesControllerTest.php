<?php

namespace Tests\Unit\Controllers\User;

use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    function it_will_only_show_your_own_batches()
    {

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

    private function postUnlink($batchFile)
    {
        return $this->post(route('user.subIdxBatch.unlink', $batchFile));
    }
}
