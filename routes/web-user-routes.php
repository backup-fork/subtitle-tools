<?php

Route::get('/dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard.index']);
