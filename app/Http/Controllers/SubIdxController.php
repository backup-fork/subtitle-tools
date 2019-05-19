<?php

namespace App\Http\Controllers;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubMimeRule;
use App\Http\Rules\TextFileRule;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use App\Support\Archive\StoredFileArchive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SubIdxController
{
    public function index()
    {
        return view('tools.convert-sub-idx-to-srt');
    }

    public function post(Request $request)
    {
        $request->validate([
            'sub' => ['required', 'file', new FileNotEmptyRule, new SubMimeRule],
            'idx' => ['required', 'file', new FileNotEmptyRule, new TextFileRule],
        ]);

        $subIdx = SubIdx::getOrCreateFromUpload(
            $request->files->get('sub'),
            $request->files->get('idx')
        );

        if (! $subIdx->is_readable) {
            return back()->withErrors('The sub/idx file can not be read');
        }

        return redirect()->route('subIdx.show', $subIdx->url_key);
    }

    public function show($urlKey)
    {
        $subIdx = SubIdx::where('url_key', $urlKey)->firstOrFail();

        return view('tool-results.sub-idx-result', [
            'originalName' => $subIdx->original_name,
            'urlKey' => $urlKey,
        ]);
    }

    public function downloadSrt($urlKey, $index)
    {
        $language = SubIdxLanguage::query()
            ->with('outputStoredFile')
            ->where('index', $index)
            ->whereNull('error_message')
            ->whereNotNull('finished_at')
            ->whereNotNull('output_stored_file_id')
            ->whereHas('subIdx', function (Builder $query) use ($urlKey) {
                $query->where('url_key', $urlKey);
            })
            ->firstOrFail();

        // Don't update the SubIdx "updated_at" column, that column is used in "RandomizeSubIdxUrlKeysJob".
        $language->setTouchedRelations([])->increment('times_downloaded');

        return response()->download($language->outputStoredFile->file_path, $language->file_name, [
            'Content-type' => 'application/octet-stream', // this stops Safari on MacOS from adding a .txt extension when downloading
        ]);
    }

    public function downloadZip($urlKey)
    {
        $subIdx = SubIdx::query()
            ->with('languages', 'languages.outputStoredFile')
            ->where('url_key', $urlKey)
            ->firstOrFail();

        $archive = new StoredFileArchive();

        $subIdx->languages
            ->where('output_stored_file_id')
            ->whenEmpty(function () {
                abort(422, 'This sub/idx has no finished languages');
            })
            ->each(function (SubIdxLanguage $language) use ($archive) {
                $archive->add($language->outputStoredFile, $language->file_name);
            });

        $storedFile = $archive->store();

        return response()->download($storedFile->file_path, "$subIdx->original_name.zip");
    }

    public function downloadRedirect($urlKey, $index)
    {
        return redirect()->route('subIdx.show', $urlKey);
    }
}
