<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use App\MobileCode as Cd;

class MobileCode extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mobile_codes')->delete();
        Cd::create(array('code' => '505', 'country_id' => '1',));
        Cd::create(array('code' => '506', 'country_id' => '1',));
        Cd::create(array('code' => '507', 'country_id' => '1',));
        Cd::create(array('code' => '552', 'country_id' => '1',));
        Cd::create(array('code' => '553', 'country_id' => '1',));
        Cd::create(array('code' => '554', 'country_id' => '1',));
        Cd::create(array('code' => '555', 'country_id' => '1',));
        Cd::create(array('code' => '559', 'country_id' => '1',));
        Cd::create(array('code' => '530', 'country_id' => '1',));
        Cd::create(array('code' => '531', 'country_id' => '1',));
        Cd::create(array('code' => '532', 'country_id' => '1',));
        Cd::create(array('code' => '533', 'country_id' => '1',));
        Cd::create(array('code' => '534', 'country_id' => '1',));
        Cd::create(array('code' => '535', 'country_id' => '1',));
        Cd::create(array('code' => '536', 'country_id' => '1',));
        Cd::create(array('code' => '537', 'country_id' => '1',));
        Cd::create(array('code' => '538', 'country_id' => '1',));
        Cd::create(array('code' => '539', 'country_id' => '1',));
        Cd::create(array('code' => '540', 'country_id' => '1',));
        Cd::create(array('code' => '541', 'country_id' => '1',));
        Cd::create(array('code' => '542', 'country_id' => '1',));
        Cd::create(array('code' => '543', 'country_id' => '1',));
        Cd::create(array('code' => '544', 'country_id' => '1',));
        Cd::create(array('code' => '545', 'country_id' => '1',));
        Cd::create(array('code' => '546', 'country_id' => '1',));
        Cd::create(array('code' => '547', 'country_id' => '1',));
        Cd::create(array('code' => '548', 'country_id' => '1',));
        Cd::create(array('code' => '549', 'country_id' => '1',));
    }
}
