<?php

use Illuminate\Database\Seeder;
use App\Overtime as Ot;

class Overtime extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('overtimes')->delete();
        Ot::create(['id' => '1', 'name' => 'Tam Gün', 'multiplier' => '1.0']);
        Ot::create(['id' => '2', 'name' => 'Yarım Gün', 'multiplier' => '0.5']);
        Ot::create(['id' => '3', 'name' => 'Fazla Mesai', 'multiplier' => '0.125']);
        Ot::create(['id' => '4', 'name' => 'Yıllık İzin', 'multiplier' => '1.0']);
        Ot::create(['id' => '5', 'name' => 'Günlük İzin', 'multiplier' => '0']);
        Ot::create(['id' => '6', 'name' => 'Haftalık İzin', 'multiplier' => '0']);
        Ot::create(['id' => '7', 'name' => 'Ücretsiz İzin', 'multiplier' => '0']);
        Ot::create(['id' => '8', 'name' => 'Raporlu', 'multiplier' => '0']);
        Ot::create(['id' => '9', 'name' => 'Bayram', 'multiplier' => '2.0']);
        Ot::create(['id' => '10', 'name' => 'Geçici Görevli', 'multiplier' => '1.0']);
        Ot::create(['id' => '11', 'name' => 'Çalışma Yok', 'multiplier' => '0']);
    }
}
