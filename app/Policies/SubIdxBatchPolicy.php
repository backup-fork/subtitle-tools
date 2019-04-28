<?php

namespace App\Policies;

use App\Models\SubIdxBatch\SubIdxBatch;
use App\Models\User;

class SubIdxBatchPolicy
{
    public function access(User $user, SubIdxBatch $subIdxBatch)
    {
        return $user->id === $subIdxBatch->user_id;
    }
}
