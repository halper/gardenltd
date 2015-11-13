<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use \App\Equipment as Eq;

class Equipment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(EquipmentsTableSeeder::class);

        Model::reguard();
    }
}

class EquipmentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('equipments')->delete();
        Eq::create(array(
            'name' => 'EKSKAVATÖR',
        ));
        Eq::create(array('name' => 'LOADER 955',));
        Eq::create(array('name' => 'TELEHANDLER',));
        Eq::create(array('name' => 'JCB',));
        Eq::create(array('name' => 'KAMYON',));
        Eq::create(array('name' => 'MİNİ KAZIK MAKİNASI',));
        Eq::create(array('name' => 'SEYYAR AYDINLATMA',));
        Eq::create(array('name' => 'JENERATÖR',));
        Eq::create(array('name' => 'DALGIÇ POMPA',));
        Eq::create(array('name' => 'KOMPRESÖR',));
        Eq::create(array('name' => 'SİLİNDİR',));
        Eq::create(array('name' => 'KAYNAK MAKİNASI',));
        Eq::create(array('name' => 'PICK-UP',));
        Eq::create(array('name' => 'MİKSER',));
        Eq::create(array('name' => 'HİYAP',));
        Eq::create(array('name' => 'KULE VİNÇ',));
        Eq::create(array('name' => 'TRAKTÖR',));
        Eq::create(array('name' => 'VİBRATÖR',));
        Eq::create(array('name' => 'MOBİL SU TANKI',));
        Eq::create(array('name' => 'KOMPAKTÖR',));
        Eq::create(array('name' => 'DİESEL POMPA',));
        Eq::create(array('name' => 'HİLTİ',));
        Eq::create(array('name' => 'ARAZÖZ',));
        Eq::create(array('name' => 'BOBCAT',));
    }
}