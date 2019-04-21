<?php

namespace App\Subtitles\Tools\Options;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubtitleFileRule;
use App\Models\StoredFile;
use Illuminate\Http\Request;

class MergeSubtitlesOptions extends ToolOptions
{
    public $mergeWithStoredFileId;

    public $mode = 'simple';

    public $nearestCueThreshold = 1000;

    public $glueOffset = 1000;

    public $shouldColorBaseSubtitle = false;

    public $shouldColorMergeSubtitle = false;

    public $baseSubtitleColor = '#FFFFFF';

    public $mergeSubtitleColor = '#FFFFFF';

    public function rules(): array
    {
        return [
            'mode' => 'required|in:simple,nearestCueThreshold,topBottom,glue',
            'nearest_cue_threshold' => 'required|numeric|not_in:0|regex:/^\d+$/',
            'glue_offset' => 'required|numeric|regex:/^-?\d+$/',
            'second-subtitle' => ['required', 'file', new FileNotEmptyRule, new SubtitleFileRule],
            'should_color_base_subtitle' => 'present|bool',
            'should_color_merge_subtitle' => 'present|bool',
            'base_subtitle_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'merge_subtitle_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        ];
    }

    public function loadRequest(Request $request)
    {
        $file = $request->file('second-subtitle');

        return $this->load([
            'mode' => $request->get('mode'),
            'mergeWithStoredFileId' => StoredFile::getOrCreate($file)->id,
            'nearestCueThreshold' => (int) $request->get('nearest_cue_threshold'),
            'glueOffset' => (int) $request->get('glue_offset'),
            'shouldColorBaseSubtitle' => (bool) $request->get('should_color_base_subtitle'),
            'shouldColorMergeSubtitle' => (bool) $request->get('should_color_merge_subtitle'),
            'baseSubtitleColor' => strtoupper($request->get('base_subtitle_color')),
            'mergeSubtitleColor' => strtoupper($request->get('merge_subtitle_color')),
        ]);
    }

    public function getMergeStoredFile(): StoredFile
    {
        return StoredFile::findOrFail($this->mergeWithStoredFileId);
    }

    public function simpleMode()
    {
        return $this->mode === 'simple';
    }

    public function topBottomMode()
    {
        return $this->mode === 'topBottom';
    }

    public function nearestCueThresholdMode()
    {
        return $this->mode === 'nearestCueThreshold';
    }

    public function glueEndToEndMode()
    {
        return $this->mode === 'glue';
    }
}
