<?php

use App\Group as Gp;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class Group extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(GroupsTableSeeder::class);

        Model::reguard();
    }
}

class GroupsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('groups')->delete();
        Gp::create(array(
            'name' => 'PROJE MÜD.',
        ));
        Gp::create(array(
            'name' => 'ŞANT. ŞEFİ',
        ));
        Gp::create(array(
            'name' => 'MUHASEBE MÜD.',
        ));
        Gp::create(array(
            'name' => 'MERKEZ MUHASABE',
        ));
        Gp::create(array(
            'name' => 'ŞANTİYE MUHASEBE',
        ));
        Gp::create(array(
            'name' => 'SATINALMA',
        ));
        Gp::create(array(
            'name' => 'SAHA MÜHENDİSİ',
        ));
    }
}