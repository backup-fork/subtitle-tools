<?php

namespace Tests;

use App\Models\SubIdxBatch\SubIdxBatch;

trait CreatesSubIdxBatches
{
    public function createSubIdxBatch($user = null, $attributes = []): SubIdxBatch
    {
        if (is_array($user)) {
            [$user, $attributes] = [$attributes, $user];
        }

        $user = $user ?: $this->createUser();

        return factory(SubIdxBatch::class)->create($attributes + [
            'user_id' => $user->id,
        ]);
    }
}
