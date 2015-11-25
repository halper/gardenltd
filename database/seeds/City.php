<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\City as Ct;

class City extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Model::unguard();

        $this->call(CitiesTableSeeder::class);

        Model::reguard();
    }
}

class CitiesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('cities')->delete();
        Ct::create(array('name' => 'İstanbul', 'country_id' => '1',));
        Ct::create(array('name' => 'Ankara', 'country_id' => '1',));
        Ct::create(array('name' => 'İzmir', 'country_id' => '1',));
        Ct::create(array('name' => 'Adana', 'country_id' => '1',));
        Ct::create(array('name' => 'Adıyaman', 'country_id' => '1',));
        Ct::create(array('name' => 'Afyonkarahisar', 'country_id' => '1',));
        Ct::create(array('name' => 'Ağrı', 'country_id' => '1',));
        Ct::create(array('name' => 'Aksaray', 'country_id' => '1',));
        Ct::create(array('name' => 'Amasya', 'country_id' => '1',));
        Ct::create(array('name' => 'Antalya', 'country_id' => '1',));
        Ct::create(array('name' => 'Ardahan', 'country_id' => '1',));
        Ct::create(array('name' => 'Artvin', 'country_id' => '1',));
        Ct::create(array('name' => 'Aydın', 'country_id' => '1',));
        Ct::create(array('name' => 'Balıkesir', 'country_id' => '1',));
        Ct::create(array('name' => 'Bartın', 'country_id' => '1',));
        Ct::create(array('name' => 'Batman', 'country_id' => '1',));
        Ct::create(array('name' => 'Bayburt', 'country_id' => '1',));
        Ct::create(array('name' => 'Bilecik', 'country_id' => '1',));
        Ct::create(array('name' => 'Bingöl', 'country_id' => '1',));
        Ct::create(array('name' => 'Bitlis', 'country_id' => '1',));
        Ct::create(array('name' => 'Bolu', 'country_id' => '1',));
        Ct::create(array('name' => 'Burdur', 'country_id' => '1',));
        Ct::create(array('name' => 'Bursa', 'country_id' => '1',));
        Ct::create(array('name' => 'Çanakkale', 'country_id' => '1',));
        Ct::create(array('name' => 'Çankırı', 'country_id' => '1',));
        Ct::create(array('name' => 'Çorum', 'country_id' => '1',));
        Ct::create(array('name' => 'Denizli', 'country_id' => '1',));
        Ct::create(array('name' => 'Diyarbakır', 'country_id' => '1',));
        Ct::create(array('name' => 'Düzce', 'country_id' => '1',));
        Ct::create(array('name' => 'Edirne', 'country_id' => '1',));
        Ct::create(array('name' => 'Elazığ', 'country_id' => '1',));
        Ct::create(array('name' => 'Erzincan', 'country_id' => '1',));
        Ct::create(array('name' => 'Erzurum', 'country_id' => '1',));
        Ct::create(array('name' => 'Eskişehir', 'country_id' => '1',));
        Ct::create(array('name' => 'Gaziantep', 'country_id' => '1',));
        Ct::create(array('name' => 'Giresun', 'country_id' => '1',));
        Ct::create(array('name' => 'Gümüşhane', 'country_id' => '1',));
        Ct::create(array('name' => 'Hakkari', 'country_id' => '1',));
        Ct::create(array('name' => 'Hatay', 'country_id' => '1',));
        Ct::create(array('name' => 'Iğdır', 'country_id' => '1',));
        Ct::create(array('name' => 'Isparta', 'country_id' => '1',));
        Ct::create(array('name' => 'Kahramanmaraş', 'country_id' => '1',));
        Ct::create(array('name' => 'Karabük', 'country_id' => '1',));
        Ct::create(array('name' => 'Karaman', 'country_id' => '1',));
        Ct::create(array('name' => 'Kars', 'country_id' => '1',));
        Ct::create(array('name' => 'Kastamonu', 'country_id' => '1',));
        Ct::create(array('name' => 'Kayseri', 'country_id' => '1',));
        Ct::create(array('name' => 'Kırıkkale', 'country_id' => '1',));
        Ct::create(array('name' => 'Kırklareli', 'country_id' => '1',));
        Ct::create(array('name' => 'Kırşehir', 'country_id' => '1',));
        Ct::create(array('name' => 'Kilis', 'country_id' => '1',));
        Ct::create(array('name' => 'Kocaeli', 'country_id' => '1',));
        Ct::create(array('name' => 'Konya', 'country_id' => '1',));
        Ct::create(array('name' => 'Kütahya', 'country_id' => '1',));
        Ct::create(array('name' => 'Malatya', 'country_id' => '1',));
        Ct::create(array('name' => 'Manisa', 'country_id' => '1',));
        Ct::create(array('name' => 'Mardin', 'country_id' => '1',));
        Ct::create(array('name' => 'Mersin', 'country_id' => '1',));
        Ct::create(array('name' => 'Muğla', 'country_id' => '1',));
        Ct::create(array('name' => 'Muş', 'country_id' => '1',));
        Ct::create(array('name' => 'Nevşehir', 'country_id' => '1',));
        Ct::create(array('name' => 'Niğde', 'country_id' => '1',));
        Ct::create(array('name' => 'Ordu', 'country_id' => '1',));
        Ct::create(array('name' => 'Osmaniye', 'country_id' => '1',));
        Ct::create(array('name' => 'Rize', 'country_id' => '1',));
        Ct::create(array('name' => 'Sakarya', 'country_id' => '1',));
        Ct::create(array('name' => 'Samsun', 'country_id' => '1',));
        Ct::create(array('name' => 'Siirt', 'country_id' => '1',));
        Ct::create(array('name' => 'Sinop', 'country_id' => '1',));
        Ct::create(array('name' => 'Sivas', 'country_id' => '1',));
        Ct::create(array('name' => 'Şırnak', 'country_id' => '1',));
        Ct::create(array('name' => 'Tekirdağ', 'country_id' => '1',));
        Ct::create(array('name' => 'Tokat', 'country_id' => '1',));
        Ct::create(array('name' => 'Trabzon', 'country_id' => '1',));
        Ct::create(array('name' => 'Tunceli', 'country_id' => '1',));
        Ct::create(array('name' => 'Şanlıurfa', 'country_id' => '1',));
        Ct::create(array('name' => 'Uşak', 'country_id' => '1',));
        Ct::create(array('name' => 'Van', 'country_id' => '1',));
        Ct::create(array('name' => 'Yalova', 'country_id' => '1',));
        Ct::create(array('name' => 'Yozgat', 'country_id' => '1',));
        Ct::create(array('name' => 'Zonguldak', 'country_id' => '1',));
    }
}


