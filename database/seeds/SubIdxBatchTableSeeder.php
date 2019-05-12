<?php

use App\Models\SubIdxLanguage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Tests\CreatesModels;

class SubIdxBatchTableSeeder extends Seeder
{
    use CreatesModels;

    public function run()
    {
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


        $processingBatch = $this->createSubIdxBatch($user, ['started_at' => now()]);
        $processingBatch->subIdxes()->saveMany(
            [$a, $b, $c] = $this->createSubIdxes(3)
        );
        $a->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('queued')->make(),
            factory(SubIdxLanguage::class)->state('processing')->make(),
            factory(SubIdxLanguage::class)->state('queued')->make(),
        ]);
        $b->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('finished')->make(),
            factory(SubIdxLanguage::class)->state('finished')->make(),
        ]);
        $c->languages()->saveMany([
            factory(SubIdxLanguage::class)->state('queued')->make(),
        ]);
    }
}
