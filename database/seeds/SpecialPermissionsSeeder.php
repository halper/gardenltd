<?php

use App\SpecialPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SpecialPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(SpecialPermissionsTableSeeder::class);

        Model::reguard();
    }
}

class SpecialPermissionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('special_permissions')->delete();
        SpecialPermission::create(['name' => 'ŞANTİYE EKLE', 'group' => 'ŞANTİYE']);
        SpecialPermission::create(['name' => 'ŞANTİYE DÜZENLE', 'group' => 'ŞANTİYE']);
        SpecialPermission::create(['name' => 'YÖNETİM DENETİM PERSONEL TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'GARDEN YÖNETİM PERSONEL TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'ALT YÜKLENİCİLER PERSONEL TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'EKİPMAN TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'YAPILAN İŞLER TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'GELEN MALZEMELER TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'GİDEN MALZEMELER TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'PUANTAJ VE YEMEK TABLOSU', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'ERTESİ GÜN NOTLARI', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'RAPOR EKLERİ', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'RAPOR FORM GÖRÜNÜMÜ', 'group' => 'GÜNLÜK RAPOR']);
        SpecialPermission::create(['name' => 'PERSONEL', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'PERSONEL İŞE GİRİŞ', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'ALT YÜKLENİCİ', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'İŞ KOLU', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'FAALİYET ALANI', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'DEPARTMAN', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'DEMİRBAŞ', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'MALZEME (MALZEME TALEP)', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'BAĞLANTILI MALZEME', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'İŞ MAKİNESİ', 'group' => 'EKLE']);
        SpecialPermission::create(['name' => 'PUANTAJ FİLTRELE', 'group' => 'PUANTAJ']);
        SpecialPermission::create(['name' => 'YEMEK FİLTRELE', 'group' => 'YEMEK']);
        SpecialPermission::create(['name' => 'İŞ İLERLEME FİLTRELE', 'group' => 'İŞ İLERLEME']);
        SpecialPermission::create(['name' => 'MALZEME TALEP FORMU BİRİM FİYAT', 'group' => 'MALZEME TALEP']);
        SpecialPermission::create(['name' => 'MALZEME TALEP FORMU ÖDEME ŞEKLİ', 'group' => 'MALZEME TALEP']);
        SpecialPermission::create(['name' => 'MALZEME TALEP GÖRÜNTÜLE SEVKET', 'group' => 'MALZEME TALEP']);
        SpecialPermission::create(['name' => 'MALZEME TALEP GÖRÜNTÜLE SİL', 'group' => 'MALZEME TALEP']);
        SpecialPermission::create(['name' => 'DEMİRBAŞ SİL', 'group' => 'DEMİRBAŞ']);
        SpecialPermission::create(['name' => 'HARCAMA KAYDI OLUŞTUR', 'group' => 'KASA']);
        SpecialPermission::create(['name' => 'HARCAMA SİL', 'group' => 'KASA']);
        SpecialPermission::create(['name' => 'ALT YÜKLENİCİ SİL', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'ALT YÜKLENİCİ DÜZENLE', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'SÖZLEŞME BİLGİLERİ', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'ÜCRET ORANLARI', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'EK BELGELER', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'PERSONEL EKLE', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'İŞE GİRİŞ BELGESİ EKLE', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'PERSONEL DÜZENLE', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'PERSONEL SİL', 'group' => 'ALT YÜKLENİCİ CARİ HESAP']);
        SpecialPermission::create(['name' => 'BAĞLANTILI MALZEME TALEP OLUŞTUR', 'group' => 'BAĞLANTILI MALZEME']);
        SpecialPermission::create(['name' => 'BAĞLANTILI MALZEME TALEP GÖRÜNTÜLE', 'group' => 'BAĞLANTILI MALZEME']);
        SpecialPermission::create(['name' => 'BAĞLANTILI MALZEME TALEP GÜNCELLE', 'group' => 'BAĞLANTILI MALZEME']);
    }
}
