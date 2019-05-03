<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Tests\CreatesSubIdxBatches;

class SubIdxBatchTableSeeder extends Seeder
{
    use CreatesSubIdxBatches;

    public function run()
    {
        /** @var User $user */
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $emptyBatch = $this->createSubIdxBatch($user);

        $onlyUnlinked = $this->createSubIdxBatch($user);
        $this->createUnlinkedBatchFile_sub($onlyUnlinked);
        $this->createUnlinkedBatchFile_sub($onlyUnlinked);
        $this->createUnlinkedBatchFile_sub($onlyUnlinked);

        $onlyLinked = $this->createSubIdxBatch($user);
        $this->createSubIdxBatchFiles(3, $onlyLinked);

        $linkedAndUnlinked = $this->createSubIdxBatch($user);
        $this->createUnlinkedBatchFiles(6, $linkedAndUnlinked);
        $this->createSubIdxBatchFiles(3, $linkedAndUnlinked);
    }
}
