<?php

use Illuminate\Database\Seeder;
use App\Site;
use Illuminate\Database\Eloquent\Model;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SitesTableSeeder::class);

        Model::reguard();
    }
}

class SitesTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('sites')->delete();

        Site::create(array(
            'job_name'     => 'TOKİ Mahal',
            'management_name'    => 'Sinpaş Toki Konsorsiyum',
            'start_date' => '2015-08-31',
            'contract_date' => '2015-08-01',
            'end_date' => '2015-12-31',
            'address' => 'Eskişehir Yolu 9. KM Ankara',
            'site_chief' => 'Tolga Alper',
        ));

        Site::create(array(
            'job_name'     => 'Sinpaş OrAn',
            'management_name'    => 'Sinpaş',
            'start_date' => '2015-08-31',
            'contract_date' => '2015-08-01',
            'end_date' => '2015-12-01',
            'address' => 'Oran Ankara',
            'site_chief' => 'Tolga Alper',
        ));
        /*Site::create(array(
            'id' => '99998',
            'job_name'     => 'Mevcut santiyeler icin izin',
            'management_name'    => 'Mevcut santiyeler icin izin',
            'start_date' => '2015-08-31',
            'contract_date' => '2015-08-01',
            'end_date' => '2015-12-31',
            'address' => 'Eskişehir Yolu 9. KM Ankara',
            'site_chief' => 'Tolga Alper',
        ));*/
        Site::create(array(
            'id' => '1',
            'job_name'     => 'Mevcut ve sonraki santiyeler icin izin',
            'management_name'    => 'Mevcut ve sonraki santiyeler icin izin',
            'start_date' => '2015-08-31',
            'contract_date' => '2015-08-01',
            'end_date' => '2015-12-31',
            'address' => 'Eskişehir Yolu 9. KM Ankara',
            'site_chief' => 'Tolga Alper',
        ));
    }

}