<?php

namespace App\Http\Controllers\User;

use App\Http\Rules\AreUploadedFilesRule;
use App\Http\Rules\SubMimeRule;
use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\SubIdxBatch\SubIdxBatchFile;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubIdxBatchUploadController
{
    private $linkedNames = [];

    private $unlinkedNames = [];

    private $duplicateUnlinkedNames = [];

    private $duplicateLinkedNames = [];

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
            'duplicateUnlinkedNames' => $this->duplicateUnlinkedNames,
            'duplicateLinkedNames' => $this->duplicateLinkedNames,
            'automaticallyUpload' => $request->get('automatically_upload') ?? false,
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
        $extension = $isSub ? '.sub' : '.idx';

        $existingHashes = null;

        if ($existingHashes === null) {
            $existingHashes = $subIdxBatch->unlinkedFiles()->pluck('hash')->all();
        }

        $newHash = file_hash($file);

        $name = name_without_extension($file);

        if (in_array($newHash, $existingHashes)) {
            $this->duplicateUnlinkedNames[] = $name.$extension;

            return;
        }

        $subIdxBatch->unlinkedFiles()->create([
            'id' => $uuid = Str::uuid(),
            'original_name' => $name,
            'hash' => $newHash,
            'is_sub' => $isSub,
            'storage_file_path' => Storage::putFileAs("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id", $file, $uuid.$extension),
        ]);

        $existingHashes[] = $newHash;

        $this->unlinkedNames[] = $name;
    }

    private function storeSubIdx(SubIdxBatch $subIdxBatch, UploadedFile $sub, UploadedFile $idx)
    {
        $name = name_without_extension($sub);

        $subHash = file_hash($sub);
        $idxHash = file_hash($idx);

        $existingHashes = null;

        if ($existingHashes === null) {
            $existingHashes = $subIdxBatch->files()
                ->select('sub_hash', 'idx_hash')
                ->get()
                ->map(function (SubIdxBatchFile $batchFile) {
                    return $batchFile->sub_hash.$batchFile->idx_hash;
                })
                ->all();
        }

        if (in_array($subHash.$idxHash, $existingHashes)) {
            $this->duplicateLinkedNames[] = $name;

            return;
        }

        $subIdxBatch->files()->create([
            'id' => $uuid = Str::uuid(),
            'sub_original_name' => $name,
            'idx_original_name' => name_without_extension($idx),
            'sub_hash' => $subHash,
            'idx_hash' => $idxHash,
            'sub_storage_file_path' => Storage::putFileAs("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid", $sub, 'a.sub'),
            'idx_storage_file_path' => Storage::putFileAs("sub-idx-batches/$subIdxBatch->user_id/$subIdxBatch->id/$uuid", $idx, 'a.idx'),
        ]);

        $this->linkedNames[] = $name;
    }
}
