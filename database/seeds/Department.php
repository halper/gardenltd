<?php

use App\Department as Dept;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Department extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(DepartmentsTableSeeder::class);

        Model::reguard();
    }
}

class DepartmentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('departments')->delete();
        Dept::create(array(
            'department' => 'Yönetim / İmalat',
        ));
        Dept::create(array(
            'department' => 'Personel',
        ));
    }
}