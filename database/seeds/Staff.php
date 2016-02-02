<?php

use App\Staff as St;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Staff extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(StaffsTableSeeder::class);

        Model::reguard();
    }
}

class StaffsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('staffs')->delete();

        St::create(array(
            'staff' => 'Garden Personel',
            'department_id' => '1',
            'id' => '1',
        ));
        St::create(array(
            'staff' => 'Proje Müdürü',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Şantiye Şefi',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'İnşaat Mühendisi',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'mimar',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Harita Müh.',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Elektrik Müh.',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Makine Müh.',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'İnşaat Teknikeri',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Elek. Teknikeri',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Makine Teknikeri',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Harita Teknikeri',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Şenör',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Alet Opr.',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Sağlık Memuru',
            'department_id' => '1',
        ));
        St::create(array(
            'staff' => 'Muhasabe',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Tercüman & Personel',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'İdari İşler',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Satın Alma',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Kamp Amiri',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Depocu',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Ambarcı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Puantör',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Şantiye Elek. Usta',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Kule Vinç Operatörü',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Tesisatçı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'İskeleci',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Boyacı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Seramikçi',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Sıvacı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Duvarcı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Kaynakçı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Türk Düz İşçi',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Yerel Düz İşçi',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Çaycı',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Yemekhane Sor.',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Temizlikçi',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Bekçi',
            'department_id' => '2',
        ));
        St::create(array(
            'staff' => 'Şöför',
            'department_id' => '2',
        ));
    }
}
