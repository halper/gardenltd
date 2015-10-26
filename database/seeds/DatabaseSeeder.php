<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         $this->call(UserTableSeeder::class);

        Model::reguard();
    }
}

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('users')->delete();
        User::create(array(
            'name'     => 'Tolga Cenbek',
            'email'    => 'tocenbek@gmail.com',
            'password' => Hash::make('garden'),
        ));
        User::create(array(
            'name'     => 'Alper Döm',
            'email'    => 'halperdom@gmail.com',
            'password' => Hash::make('garden'),
        ));
    }

}
