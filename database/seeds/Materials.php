<?php

use App\Material;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Materials extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(MaterialsTableSeeder::class);

        Model::reguard();
    }
}

class MaterialsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('materials')->delete();
        Material::create(array(
            'material' => 'Demir',
        ));

        Material::create(array(
            'material' => 'Çimento',
        ));

        Material::create(array(
            'material' => 'Tuğla',
        ));

        Material::create(array(
            'material' => 'Kum',
        ));

        Material::create(array(
            'material' => 'Kireç',
        ));

        Material::create(array(
            'material' => 'Alçı',
        ));

        Material::create(array(
            'material' => 'Bakır',
        ));


    }

}
