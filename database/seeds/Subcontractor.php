<?php

use App\Subcontractor as Sub;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Subcontractor extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SubcontractorTableSeeder::class);

        Model::reguard();
    }
}

class SubcontractorTableSeeder extends Seeder
{

    public function run()
    {
//        main contractor must be no 1
        DB::table('subcontractors')->delete();
        Sub::create(array(
            'id' => 1,
            'name' => 'Main contractor',
            'contract_start_date' => '0000-00-00',
            'contract_end_date' => '0000-00-00',
            'contract_date' => '0000-00-00',
            'site_id' => 1
        ));

Sub::create(array(
            'name' => 'Taşeron no 1',
    'site_id' => 2
        ));

Sub::create(array(
            'name' => 'Taşeron no 2',
    'site_id' => 2
        ));


    }

}
