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
        Ot::create(['name' => 'Tam Gün', 'multiplier' => '1.0']);
        Ot::create(['name' => 'Yarım Gün', 'multiplier' => '0.5']);
        Ot::create(['name' => 'Fazla Mesai', 'multiplier' => '0.125']);
        Ot::create(['name' => 'Yıllık İzin', 'multiplier' => '1.0']);
        Ot::create(['name' => 'Günlük İzin', 'multiplier' => '0']);
        Ot::create(['name' => 'Haftalık İzin', 'multiplier' => '0']);
        Ot::create(['name' => 'Ücretsiz İzin', 'multiplier' => '0']);
        Ot::create(['name' => 'Raporlu', 'multiplier' => '0']);
        Ot::create(['name' => 'Bayram', 'multiplier' => '2.0']);
        Ot::create(['name' => 'Geçici Görevli', 'multiplier' => '1.0']);
        Ot::create(['name' => 'Çalışma Yok', 'multiplier' => '0']);
    }
}
