<?php

use App\Module;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ModulesTableSeeder::class);

        Model::reguard();
    }
}

class ModulesTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('modules')->delete();
        Module::create(array(
            'name' => 'Günlük Rapor',
            'icon' => 'fa-bar-chart',
        ));


        Module::create(array(
            'name' => 'Puantaj',
            'icon' => 'ion-ios-timer-outline',
        ));

        
        Module::create(array(
            'name' => 'Yemek',
            'icon' => 'ion-android-restaurant',
        ));

        
        Module::create(array(
            'name' => 'İş İlerleme',
            'icon' => 'ion-arrow-graph-up-right',
        ));

        
        Module::create(array(
            'name' => 'Malzeme Talep',
            'icon' => 'fa-cart-plus',
        ));

        
        Module::create(array(
            'name' => 'Kiralık Araç',
            'icon' => 'fa-truck',
        ));

        
        Module::create(array(
            'name' => 'Demirbaş',
            'icon' => 'ion-clipboard',
        ));

        
        Module::create(array(
            'name' => 'Gelen Malzeme',
            'icon' => 'fa-cart-arrow-down',
        ));

        
        Module::create(array(
            'name' => 'Kasa',
            'icon' => 'ion-lock-combination',
        ));

        
        Module::create(array(
            'name' => 'Taşeron Cari Hesap',
            'icon' => 'ion-social-usd',
        ));

        
        Module::create(array(
            'name' => 'Bağlantı Malzeme Takip',
            'icon' => 'ion-share',
        ));

        Module::create(array(
            'id' => '1',
            'name' => 'All modules',
        ));

    }

}
