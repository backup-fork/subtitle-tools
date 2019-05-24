<?php

namespace App\Http\Controllers;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SupRule;
use App\Jobs\Sup\ExtractSupImagesJob;
use App\Models\StoredFile;
use App\Models\SupJob;
use App\Support\Facades\FileName;
use Illuminate\Http\Request;

class SupController
{
    public function index()
    {
        return view('tools.convert-sup-to-srt', [
            'languages' => config('st.tesseract.languages'),
        ]);
    }

    public function post(Request $request)
    {
        $request->validate([
            'subtitle' => ['bail', 'required', 'file', new FileNotEmptyRule, new SupRule],
            'ocrLanguage' => 'required|in:'.implode(',', config('st.tesseract.languages')),
        ]);

        $supFile = $request->file('subtitle');

        $ocrLanguage = $request->get('ocrLanguage');

        $hash = file_hash($supFile);

        $supJob = SupJob::query()
            ->where('input_file_hash', $hash)
            ->where('ocr_language', $ocrLanguage)
            ->first();

        if ($supJob) {
            // Don't update the "updated_at" column, that column is used in "RandomizeSupUrlKeysJob".
            $supJob->timestamps = false;

            $supJob->update([
                'last_cache_hit' => now(),
                'cache_hits' => $supJob->cache_hits + 1,
            ]);

            $supJob->timestamps = true;

            return redirect()->route('sup.show', $supJob->url_key);
        }

        $inputFile = StoredFile::getOrCreate($supFile);

        $supJob = SupJob::create([
            'url_key' => generate_url_key(),
            'input_stored_file_id' => $inputFile->id,
            'input_file_hash' => $hash,
            'ocr_language' => $ocrLanguage,
            'original_name' => basename($supFile->getClientOriginalName()),
        ]);

        ExtractSupImagesJob::dispatch($supJob);

        return redirect()->route('sup.show', $supJob->url_key);
    }

    public function show($urlKey)
    {
        $supJob = SupJob::where('url_key', $urlKey)->firstOrFail();

        return view('tool-results.sup-result', [
            'originalName' => $supJob->original_name,
            'ocrLanguage' => $supJob->ocr_language,
            'urlKey' => $urlKey,
        ]);
    }

    public function download($urlKey)
    {
        $supJob = SupJob::query()
            ->where('url_key', $urlKey)
            ->whereNull('error_message')
            ->whereNotNull('finished_at')
            ->firstOrFail();

        $filePath = $supJob->outputStoredFile->file_path;

        $fileName = FileName::changeExtension($supJob->original_name, 'srt');

        return response()->download($filePath, $fileName, [
            'Content-type' => 'application/octet-stream', // this stops Safari on MacOS from adding a .txt extension when downloading
        ]);
    }

    public function downloadRedirect($urlKey)
    {
        return redirect()->route('sup.show', $urlKey);
    }
}
