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
            'id' => '1',
            'name' => 'All modules',
        ));

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
            'name' => 'İş Makineleri',
            'icon' => 'fa-truck',
        ));


        Module::create(array(
            'name' => 'Demirbaş',
            'icon' => 'ion-clipboard',
        ));


        Module::create(array(
            'name' => 'Gelen Malzeme',
            'icon' => 'fa-caret-square-o-down',
        ));

        Module::create(array(
            'name' => 'Giden Malzeme',
            'icon' => 'fa-caret-square-o-up',
        ));


        Module::create(array(
            'name' => 'Kasa',
            'icon' => 'ion-lock-combination',
        ));


        Module::create(array(
            'name' => 'Alt Yüklenici Cari Hesap',
            'icon' => 'ion-social-usd',
        ));


        Module::create(array(
            'name' => 'Bağlantı Malzeme Takip',
            'icon' => 'ion-share',
        ));

        Module::create(array(
            'name' => 'Ertesi Gün Notları',
            'icon' => 'ion-ios-list-outline',
        ));

        Module::create(array(
            'name' => 'Hakedişler',
            'icon' => 'ion-ios-calculator',
        ));
        Module::create(array(
            'name' => 'Şantiye Ekleri',
            'icon' => 'ion-images',
        ));

        Module::create([
            'name' => 'İcmal',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'Genel Giderler',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'Sözleşme Giderleri',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'Muhtelif Sarf Malzeme',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'İnşaat Malzeme',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'Taşeron Hakedişleri',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'Şantiye Personel Maaş',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
        Module::create([
            'name' => 'Şantiye İşçilik',
            'icon' => 'fa-tachometer',
            'expandable' => 'Maliyet'
        ]);
    }

}
