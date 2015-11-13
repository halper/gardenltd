<?php

use App\Manufacture as Man;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Manufacture extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ManufacturingsTableSeeder::class);

        Model::reguard();
    }
}

class ManufacturingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('manufacturings')->delete();
        Man::create(array(
            'name' => 'Elektrik',
        ));
        Man::create(array(
            'name' => 'Kaba Yapı',
        ));
        Man::create(array(
            'name' => 'İksa',
        ));
        Man::create(array(
            'name' => 'İzolasyon',
        ));
        Man::create(array(
            'name' => 'Tuğla Duvar',
        ));
        Man::create(array(
            'name' => 'Koruma Betonu',
        ));
        Man::create(array(
            'name' => 'Altyapı',
        ));
    }
}