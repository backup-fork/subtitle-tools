<?php

namespace App\Http\Controllers\User;

class BuyBatchTokensController
{
    public function index()
    {
        return view('user.buy-tokens.index', [
            'user' => user(),
        ]);
    }
}
