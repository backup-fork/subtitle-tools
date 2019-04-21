<?php

namespace Tests\Unit\Support;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    function file_mime_returns_the_file_mime()
    {
        $this->assertSame(
            'text/plain',
            file_mime($this->testFilesStoragePath.'sub-idx/error-and-nl.idx')
        );
    }

    /** @test */
    function file_mime_handles_broken_files()
    {
        $this->assertSame(
            'application/octet-stream',
            file_mime($this->testFilesStoragePath.'other/file-with-broken-mime.dat')
        );
    }

    /** @test */
    function storage_disk_file_path_returns_the_correct_path()
    {
        $this->assertStringEndsWith(
            '/storage/testing/dirname',
            storage_disk_file_path('dirname')
        );

        $this->assertStringEndsWith(
            '/storage/testing/dirname/file.jpg',
            storage_disk_file_path('/dirname/file.jpg')
        );
    }

    /** @test */
    function file_hash_hashes_files()
    {
        $fileA = $this->testFilesStoragePath.'sub-idx/error-and-nl.sub';
        $fileB = $this->testFilesStoragePath.'sub-idx/error-and-nl.idx';

        $this->assertSame($hashA = 'ca9b27eec6c23c8961604afeb08ecfb96901df5d', file_hash($fileA));
        $this->assertSame($hashB = 'da96b233385f273882f4e7ff60d5008b345dbecb', file_hash($fileB));

        [$a, $b] = file_hash($fileA, $fileB);

        $this->assertSame($hashA, $a);
        $this->assertSame($hashB, $b);
    }
}
