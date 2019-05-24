<?php

namespace App\Http\Controllers\User;

use App\Http\Rules\AreUploadedFilesRule;
use App\Http\Rules\SubMimeRule;
use App\Models\SubIdxBatch\SubIdxBatch;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubIdxBatchUploadController
{
    private $linkedNames = [];

    private $unlinkedNames = [];

    public function index(SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            return redirect()->route('user.subIdxBatch.show', $subIdxBatch);
        }

        $subIdxBatch->load('files', 'unlinkedFiles');

        return view('user.sub-idx-batch.show-uploads', [
            'subIdxBatch' => $subIdxBatch,
        ]);
    }

    public function post(Request $request, SubIdxBatch $subIdxBatch)
    {
        if ($subIdxBatch->started_at) {
            abort(422, 'This batch has already started');
        }

        $files = $request->validate([
            'files' => ['required', 'array', 'max:100', new AreUploadedFilesRule],
        ])['files'];

        [$subFiles, $idxFiles, $invalidFiles] = $this->sort($files);

        if (count($files) === 2 && count($subFiles) === 1 && count($idxFiles) === 1) {
            $this->storeSubIdx($subIdxBatch, $subFiles[0], $idxFiles[0]);
        } else {
            $this->store($subIdxBatch, $subFiles, $idxFiles);
        }

        $invalidFileNames = array_map(function (UploadedFile $file) {
            return $file->getClientOriginalName();
        }, $invalidFiles);

        return view('user.sub-idx-batch.show-uploads', [
            'subIdxBatch' => $subIdxBatch,
            'linkedNames' => $this->linkedNames,
            'unlinkedNames' => $this->unlinkedNames,
            'invalidNames' => $invalidFileNames,
        ]);
    }

    private function sort($files)
    {
        $subFiles = [];
        $idxFiles = [];
        $invalidFiles = [];

        /** @var UploadedFile $file */
        foreach ($files as $file) {
            if ((new SubMimeRule)->passes('', $file)) {
                $subFiles[] = $file;
            } elseif (is_text_file($file) && $file->getSize() > 0) {
                $idxFiles[] = $file;
            } else {
                $invalidFiles[] = $file;
            }
        }

        return [$subFiles, $idxFiles, $invalidFiles];
    }

    private function store(SubIdxBatch $subIdxBatch, $subFiles, $idxFiles)
    {
        /** @var UploadedFile $sub */
        foreach ($subFiles as $subKey => $sub) {
            $subName = name_without_extension($sub);

            foreach ($idxFiles as $idxKey => $idx) {
                $idxName = name_without_extension($idx);

                if (strtolower($subName) === strtolower($idxName)) {
                    unset($idxFiles[$idxKey], $subFiles[$subKey]);

                    $this->storeSubIdx($subIdxBatch, $sub, $idx);
                }
            }
        }

        foreach ($subFiles as $unmatchedSubFile) {
            $this->storeUnlinked($subIdxBatch, $unmatchedSubFile, true);
        }

        foreach ($idxFiles as $unmatchedIdxFile) {
            $this->storeUnlinked($subIdxBatch, $unmatchedIdxFile, false);
        }
    }

    private function storeUnlinked(SubIdxBatch $subIdxBatch, UploadedFile $file, bool $isSub)
    {
        $subIdxBatch->unlinkedFiles()->create([
            'id' => $uuid = Str::uuid(),
            'original_name' => $name = name_without_extension($file),
            'hash' => file_hash($file),
            'is_sub' => $isSub,
            'storage_file_path' => Storage::putFileAs("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id", $file, $uuid.($isSub ? '.sub' : '.idx')),
        ]);

        $this->unlinkedNames[] = $name;
    }

    private function storeSubIdx(SubIdxBatch $subIdxBatch, UploadedFile $sub, UploadedFile $idx)
    {
        $subIdxBatch->files()->create([
            'id' => $uuid = Str::uuid(),
            'sub_original_name' => $name = name_without_extension($sub),
            'idx_original_name' => name_without_extension($idx),
            'sub_hash' => file_hash($sub),
            'idx_hash' => file_hash($idx),
            'sub_storage_file_path' => Storage::putFileAs("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid", $sub, 'a.sub'),
            'idx_storage_file_path' => Storage::putFileAs("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid", $idx, 'a.idx'),
        ]);

        $this->linkedNames[] = $name;
    }
}
