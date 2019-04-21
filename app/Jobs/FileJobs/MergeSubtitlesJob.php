<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\ChangesColor;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TextFile;
use App\Subtitles\Tools\Options\MergeSubtitlesOptions;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Support\Facades\TextFileFormat;
use RuntimeException;

class MergeSubtitlesJob extends FileJob
{
    protected $newExtension = '';

    public function process(TextFile $subtitle, $options)
    {
        /** @var MergeSubtitlesOptions $options */

        $baseSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile);

        $mergeSubtitle = TextFileFormat::getMatchingFormat($options->getMergeStoredFile());

        if ($baseSubtitle instanceof Srt || ! $mergeSubtitle instanceof ContainsGenericCues) {
            $mergeSubtitle = $this->convertToSrt($mergeSubtitle);
        }

        $this->newExtension = $baseSubtitle->getExtension();

        if (! $baseSubtitle || ! $mergeSubtitle || ! $baseSubtitle instanceof ContainsGenericCues) {
            $this->abort('messages.cant_merge_these_subtitles');
        }

        if ($options->shouldColorBaseSubtitle && $baseSubtitle instanceof ChangesColor) {
            $baseSubtitle->changeColor($options->baseSubtitleColor);
        }

        if ($options->shouldColorMergeSubtitle && $mergeSubtitle instanceof ChangesColor) {
            $mergeSubtitle->changeColor($options->mergeSubtitleColor);
        }

        if ($options->simpleMode() || $options->topBottomMode()) {
            return $this->simpleMerge($baseSubtitle, $mergeSubtitle, $options->topBottomMode());
        }

        if ($options->nearestCueThresholdMode()) {
            return $this->nearestCueThresholdMerge($baseSubtitle, $mergeSubtitle, $options->nearestCueThreshold);
        }

        if ($options->glueEndToEndMode()) {
            return $this->glueEndToEndMerge($baseSubtitle, $mergeSubtitle, $options->glueOffset);
        }

        throw new RuntimeException('Invalid mode');
    }

    private function convertToSrt($subtitle)
    {
        if (! $subtitle instanceof TransformsToGenericSubtitle && ! $subtitle instanceof Srt) {
            return null;
        }

        $srt = $subtitle instanceof Srt
            ? $subtitle
            : new Srt($subtitle);

        $srt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if (! $srt->hasCues()) {
            return null;
        }

        return $srt;
    }

    /**
     * @param $baseSubtitle ContainsGenericCues|TextFile
     * @param $mergeSubtitle ContainsGenericCues|TextFile
     * @param $styleOnTop bool
     *
     * @return ContainsGenericCues|TextFile
     */
    private function simpleMerge($baseSubtitle, $mergeSubtitle, bool $styleOnTop)
    {
        foreach ($mergeSubtitle->getCues() as $mergeCue) {
            $addedCue = $baseSubtitle->addCue($mergeCue);

            if ($styleOnTop) {
                $addedCue->stylePositionTop();
            }
        }

        return $baseSubtitle
            ->removeEmptyCues()
            ->removeDuplicateCues();
    }

    /**
     * @param $baseSubtitle ContainsGenericCues|TextFile
     * @param $mergeSubtitle ContainsGenericCues|TextFile
     * @param $glueOffset
     *
     * @return ContainsGenericCues|TextFile
     */
    private function glueEndToEndMerge($baseSubtitle, $mergeSubtitle, $glueOffset)
    {
        $baseCues = $baseSubtitle->getCues();

        $glueOffset = ($baseCues ? end($baseCues)->getStartMs() : 0) + $glueOffset;

        foreach ($mergeSubtitle->getCues() as $mergeCue) {
            $baseSubtitle->addCue($mergeCue)->shift($glueOffset);
        }

        return $baseSubtitle
            ->removeEmptyCues()
            ->removeDuplicateCues();
    }

    /**
     * @param $baseSubtitle ContainsGenericCues|TextFile
     * @param $mergeSubtitle ContainsGenericCues|TextFile
     * @param $threshold
     *
     * @return ContainsGenericCues|TextFile
     */
    private function nearestCueThresholdMerge($baseSubtitle, $mergeSubtitle, $threshold)
    {
        $baseCues = $baseSubtitle->getCues();

        foreach ($mergeSubtitle->getCues() as $cue) {
            $nearestCue = $this->findNearestCue($baseCues, $cue, $threshold);

            // If there is no nearby cue, merge the whole cue.
            is_null($nearestCue)
                ? $baseSubtitle->addCue($cue)
                : $nearestCue->addLines($cue->getLines());
        }

        return $baseSubtitle;
    }

    /**
     * @param $baseCues GenericSubtitleCue[]
     * @param $cue GenericSubtitleCue
     * @param $threshold
     *
     * @return GenericSubtitleCue|null
     */
    private function findNearestCue($baseCues, $cue, $threshold)
    {
        $cueStartMs = $cue->getStartMs();

        $nearestCue = null;

        $smallestDifference = 99999;

        foreach ($baseCues as $cue) {
            $difference = abs($cue->getStartMs() - $cueStartMs);

            // Cues should be within the threshold to be a nearby cue.
            if ($difference > $threshold) {
                continue;
            }

            if ($difference > $smallestDifference) {
                continue;
            }

            $smallestDifference = $difference;

            $nearestCue = $cue;
        }

        return $nearestCue;
    }
}
