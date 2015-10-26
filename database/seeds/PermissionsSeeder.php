<?php

use App\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(PermissionsTableSeeder::class);

        Model::reguard();
    }
}

class PermissionsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('permissions')->delete();
        Permission::create(array(
            'permission'     => '1',
            'definition'    => 'Görüntüleme',
        ));
        Permission::create(array(
            'permission'     => '2',
            'definition'    => 'Düzenleme',
        ));
    Permission::create(array(
            'permission'     => '999',
            'definition'    => 'Admin',
        ));
    }

}
