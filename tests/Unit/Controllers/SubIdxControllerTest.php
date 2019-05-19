<?php

namespace Tests\Unit\Controllers;

use App\Jobs\ExtractSubIdxLanguageJob;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use App\Models\SubIdxLanguageStats;
use App\Models\SubIdxStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SubIdxControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function the_sub_and_idx_file_are_server_side_required()
    {
        $this->postSubIdx([])
            ->assertStatus(302)
            ->assertSessionHasErrors(['sub', 'idx']);
    }

    /** @test */
    function it_rejects_empty_sub_and_idx_files()
    {
        $this->postSubIdx([
                'sub' => $this->createUploadedFile('text/srt/empty.srt', 'empty.sub'),
                'idx' => $this->createUploadedFile('text/srt/empty.srt', 'empty.idx'),
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.file_is_empty', ['attribute' => 'sub']),
                'idx' => __('validation.file_is_empty', ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_fails_when_the_subidx_is_not_readable()
    {
        $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/unreadable.sub'),
                'idx' => $this->createUploadedFile('sub-idx/unreadable.idx'),
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->assertSame(
            ['The sub/idx file can not be read'],
            session('errors')->all()
        );

        $subIdx = SubIdx::findOrFail(1);

        $this->assertNull($subIdx->url_key);
        $this->assertFalse($subIdx->is_readable);
    }

    /** @test */
    function it_redirects_to_the_show_page()
    {
        $response = $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/error-and-nl.sub'),
                'idx' => $this->createUploadedFile('sub-idx/error-and-nl.idx'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $subIdx = SubIdx::findOrFail(1);

        $response->assertRedirect(route('subIdx.show', $subIdx->url_key));
    }

    /** @test */
    function it_creates_new_records_for_sub_idx_statistics()
    {
        $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/en-en-en-es.sub'),
                'idx' => $this->createUploadedFile('sub-idx/en-en-en-es.idx'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $stats = SubIdxStats::today();

        $this->assertSame(0, $stats->cache_hits);
        $this->assertSame(1, $stats->cache_misses);
        $this->assertTrue($stats->total_file_size > 8945664);

        $languages = SubIdxLanguageStats::all();

        $this->assertCount(2, $languages);

        $this->assertSame(3, $languages->where('language', 'en')->first()->times_seen);
        $this->assertSame(1, $languages->where('language', 'es')->first()->times_seen);
    }

    /** @test */
    function it_increments_existing_records_for_sub_idx_statistics()
    {
        factory(SubIdxStats::class)->create([
            'total_file_size' => 500,
            'cache_hits' => 5,
            'cache_misses' => 99,
        ]);

        factory(SubIdxLanguageStats::class)->create([
            'language' => 'es',
            'times_seen' => 3,
        ]);

        $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/en-en-en-es.sub'),
                'idx' => $this->createUploadedFile('sub-idx/en-en-en-es.idx'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $stats = SubIdxStats::today();

        $this->assertSame(5, $stats->cache_hits);
        $this->assertSame(100, $stats->cache_misses);
        $this->assertTrue($stats->total_file_size > 8945664);

        $languages = SubIdxLanguageStats::all();

        $this->assertCount(2, $languages);

        $this->assertSame(3, $languages->where('language', 'en')->first()->times_seen);
        $this->assertSame(4, $languages->where('language', 'es')->first()->times_seen);
    }

    /** @test */
    function it_automatically_starts_extracting_if_there_is_only_one_language()
    {
        Queue::fake();

        $this->postSubIdx([
            'sub' => $this->createUploadedFile('sub-idx/only-danish.sub'),
            'idx' => $this->createUploadedFile('sub-idx/only-danish.idx'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $subIdx = SubIdx::firstOrFail();

        $this->assertCount(1, $subIdx->languages);

        $language = $subIdx->languages->first();

        $languageStats = SubIdxLanguageStats::where('language', $language->language)->first();

        $this->assertSame(1, $languageStats->times_seen);
        $this->assertSame(1, $languageStats->times_extracted);

        Queue::assertPushed(ExtractSubIdxLanguageJob::class, function (ExtractSubIdxLanguageJob $job) use ($language) {
            return $job->subIdxLanguage->id === $language->id;
        });

        $this->assertNotNull($language->queued_at);
    }

    /** @test */
    function it_retrieves_sub_idx_from_cache()
    {
        /** @var SubIdx $subIdx */
        $subIdx = factory(SubIdx::class)->create([
            'sub_hash' => file_hash($this->testFilesStoragePath.'sub-idx/only-danish.sub'),
            'idx_hash' => file_hash($this->testFilesStoragePath.'sub-idx/only-danish.idx'),
            'cache_hits' => 0,
            'last_cache_hit' => null,
        ]);

        $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/only-danish.sub'),
                'idx' => $this->createUploadedFile('sub-idx/only-danish.idx'),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subIdx.show', $subIdx->url_key));

        $subIdx->refresh();

        $this->assertSame(1, $subIdx->cache_hits);
        $this->assertNow($subIdx->last_cache_hit);

        $stats = SubIdxStats::today();

        $this->assertSame(1, $stats->cache_hits);
        $this->assertSame(0, $stats->cache_misses);
    }

    /** @test */
    function it_can_show_the_detail_page()
    {
        $subIdx = factory(SubIdx::class)->create();

        $subIdx->languages()->saveMany([
            factory(SubIdxLanguage::class)->states('idle')->make(),
            factory(SubIdxLanguage::class)->states('queued')->make(),
            factory(SubIdxLanguage::class)->states('processing')->make(),
            factory(SubIdxLanguage::class)->states('failed')->make(),
            factory(SubIdxLanguage::class)->states('finished')->make(),
        ]);

        $this->showSubIdx($subIdx)->assertStatus(200);
    }

    /** @test */
    function it_can_download_a_finished_language()
    {
        $this->progressTimeInHours(1);

        $subIdx = factory(SubIdx::class)->create();

        $subIdx->languages()->save(
            $language = factory(SubIdxLanguage::class)->states('finished')->make(['times_downloaded' => 0])
        );

        $originalUpdatedAt = (string) $language->updated_at;

        $this->assertNow($subIdx->refresh()->updated_at);

        $this->progressTimeInHours(1);

        $this->downloadSubIdxLanguage($language)->assertStatus(200);

        $language->refresh();

        $this->assertSame(1, $language->times_downloaded);
        $this->assertNow($language->updated_at);

        // It should not touch the SubIdx relationship when incrementing the "times_downloaded"
        $this->assertSame($originalUpdatedAt, (string) $subIdx->refresh()->updated_at);
    }

    /** @test */
    function it_can_download_zip_of_finished_languages()
    {
        $subIdx = $this->createSubIdx();

        $subIdx->languages()->saveMany([
            $language1 = factory(SubIdxLanguage::class)->states('finished')->make(),
            $language2 = factory(SubIdxLanguage::class)->states('queued')->make(),
            $language3 = factory(SubIdxLanguage::class)->states('finished')->make(),
        ]);

        $this->downloadSubIdxZip($subIdx)
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/zip')
            ->assertHeader('content-disposition', "attachment; filename=$subIdx->original_name.zip");
    }

    /** @test */
    function it_wont_download_a_zip_when_no_languages_are_finished()
    {
        $subIdx = $this->createSubIdx();

        $subIdx->languages()->saveMany([
            factory(SubIdxLanguage::class)->states('failed')->make(),
            factory(SubIdxLanguage::class)->states('queued')->make(),
        ]);

        $this->downloadSubIdxZip($subIdx)
            ->assertStatus(422)
            ->assertSee('no finished languages');
    }

    /** @test */
    function getting_the_download_post_url_redirects_to_the_show_page()
    {
        $subIdx = factory(SubIdx::class)->create();

        $subIdx->languages()->save(
            $language = factory(SubIdxLanguage::class)->states('finished')->make()
        );

        $this->get($language->download_url)
            ->assertStatus(302)
            ->assertRedirect(route('subIdx.show', $language->subIdx->url_key));
    }

    private function postSubIdx($data)
    {
        return $this->post(route('subIdx.post'), $data);
    }

    private function showSubIdx($subIdx)
    {
        return $this->get(route('subIdx.show', $subIdx->url_key));
    }

    private function downloadSubIdxLanguage($language)
    {
        return $this->post($language->download_url);
    }

    private function downloadSubIdxZip($subIdx)
    {
        return $this->post(route('subIdx.downloadZip', $subIdx->url_key));
    }
}
