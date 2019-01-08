<?php

namespace Tests;

use App\Models\FileGroup;
use App\Models\StoredFile;
use App\Models\User;
use App\Support\Facades\TempFile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Spatie\Snapshots\MatchesSnapshots;
use Illuminate\Contracts\Console\Kernel;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots;

    protected $snapshotDirectory = '/';

    public $testFilesStoragePath;

    public function setUp()
    {
        parent::setUp();

        $this->testFilesStoragePath = base_path('tests/Files/');
    }

    protected function getSnapshotDirectory(): string
    {
        $subDirectory = DIRECTORY_SEPARATOR.ltrim($this->snapshotDirectory, DIRECTORY_SEPARATOR);

        return $this->testFilesStoragePath.'_snapshots_'.$subDirectory;
    }

    protected function getFileSnapshotDirectory(): string
    {
        return $this->getSnapshotDirectory();
    }

    public function assertMatchesFileSnapshot($file)
    {
        if ($file instanceof StoredFile) {
            $temporaryFilePath = TempFile::makeFilePath().'.txt';

            // Git changes line endings to \n, but we save files with \r\n, so we have to change them
            $lines = preg_split("/\r\n|\n|\r/",
                file_get_contents($file->file_path)
            );

            file_put_contents($temporaryFilePath, implode("\n", $lines));

            $file = $temporaryFilePath;
        }

        $this->doFileSnapshotAssertion($file);
    }

    public function assertMatchesStoredFileSnapshot($storedFileId)
    {
        $storedFile = StoredFile::findOrFail($storedFileId);

        $this->assertMatchesFileSnapshot($storedFile);
    }

    /**
     * Assert that the file job controller redirected to the file group result page.
     *
     * @param TestResponse $response
     * @param FileGroup $fileGroup
     */
    protected function assertSuccessfulFileJobRedirect(TestResponse $response, FileGroup $fileGroup)
    {
        $response->assertStatus(302)->assertRedirect($fileGroup->result_route);
    }

    /**
     * @param null $user
     *
     * @return $this
     */
    protected function adminLogin($user = null)
    {
        $user = $user ?: factory(User::class)->create();

        return $this->actingAs($user);
    }

    protected function progressTimeInDays($days)
    {
        return Carbon::setTestNow(
            Carbon::now()->addDays($days)
        );
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
