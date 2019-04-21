<?php

namespace App\Http\Controllers;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubMimeRule;
use App\Http\Rules\TextFileRule;
use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubIdxController
{
    public function index()
    {
        return view('tools.convert-sub-idx-to-srt');
    }

    public function post(Request $request)
    {
        //--- temporary debug logging

        $request->validate([
            'sub' => ['required', 'file', new FileNotEmptyRule],
            'idx' => ['required', 'file', new FileNotEmptyRule, new TextFileRule],
        ]);

        $subFile = $request->files->get('sub');
        $idxFile = $request->files->get('idx');

        if (file_mime($subFile) === 'application/octet-stream') {
            $name = substr(sha1(Str::random()), 0, 6);

            $destination = storage_path();

            copy($subFile->getRealPath(), $temp = "$destination/$name.sub");
            copy($idxFile->getRealPath(), "$destination/$name.idx");

            info($temp);
        }

        //---

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
            ->whereHas('subIdx', function (Builder $query) use ($urlKey) {
                $query->where('url_key', $urlKey);
            })
            ->firstOrFail();

        if (! $language->outputStoredFile) {
            abort(404);
        }

        // Don't update the SubIdx "updated_at" column, that column is used in "RandomizeSubIdxUrlKeysJob".
        $language->setTouchedRelations([])->increment('times_downloaded');

        return response()->download($language->outputStoredFile->file_path, $language->file_name, [
            'Content-type' => 'application/octet-stream', // this stops Safari on MacOS from adding a .txt extension when downloading
        ]);
    }

    public function downloadRedirect($urlKey, $index)
    {
        return redirect()->route('subIdx.show', $urlKey);
    }
}
