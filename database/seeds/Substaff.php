<?php

use Illuminate\Database\Seeder;
use App\Substaff as St;
use Illuminate\Database\Eloquent\Model;


class Substaff extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        Model::unguard();

        $this->call(SubstaffsTableSeeder::class);

        Model::reguard();
    }
}

class SubstaffsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('substaffs')->delete();
        

//Taşeron grubu
        St::create(array(
            'name' => 'Koordi.',
            
        ));
        St::create(array(
            'name' => 'Müh.',
            
        ));
        St::create(array(
            'name' => 'Mim.',
            
        ));
        St::create(array(
            'name' => 'Tekn.',
            
        ));
        St::create(array(
            'name' => 'Formen',
            
        ));
        St::create(array(
            'name' => 'Usta',
            
        ));
        St::create(array(
            'name' => 'İşçi',
            
        ));
        St::create(array(
            'name' => 'Operat.',
            
        ));
        St::create(array(
            'name' => 'Kalıpçı',
            
        ));
        St::create(array(
            'name' => 'Demirci',
            
        ));
        St::create(array(
            'name' => 'Şöför',
            
        ));
    }
}
