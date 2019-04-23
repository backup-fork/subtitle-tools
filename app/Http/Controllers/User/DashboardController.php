<?php

namespace App\Http\Controllers\User;

class DashboardController
{
    public function index()
    {
        return view('user.dashboard.index');
    }
}
