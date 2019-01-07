<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'fadlimuharram@hotmail.com',
            'password' => bcrypt('fadli'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'customer',
            'email' => 'fadlimuharram2@hotmail.com',
            'password' => bcrypt('fadli2'),
            'role' => 'customer'
        ]);
    }
}
