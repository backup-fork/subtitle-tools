<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        factory(User::class)->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);


        factory(User::class)->create([
            'email' => 'user@example.com',
            'is_admin' => false,
        ]);
    }
}
