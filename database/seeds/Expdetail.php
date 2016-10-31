<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use \App\Expdetail as Ex;

class Expdetail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ExpdetailSeeder::class);

        Model::reguard();
    }
}

class ExpdetailSeeder extends Seeder
{
    public function run()
    {
        DB::table('expdetails')->delete();
        Ex::create(array('name' => 'TELEFON FAKS', 'group' => '1'));
        Ex::create(array('name' => 'İSKİ SU ', 'group' => '1'));
        Ex::create(array('name' => 'REKLAM TANITIM', 'group' => '1'));
        Ex::create(array('name' => 'KARGO KURYE', 'group' => '1'));
        Ex::create(array('name' => 'TEMİZLİK', 'group' => '1'));
        Ex::create(array('name' => 'TEKNİK SERVİS', 'group' => '1'));
        Ex::create(array('name' => 'SMM YMM AVUKAT', 'group' => '1'));
        Ex::create(array('name' => 'BURS GİDERİ', 'group' => '1'));
        Ex::create(array('name' => 'İNTERNET SARF MALZ.', 'group' => '1'));
        Ex::create(array('name' => 'NOTER ', 'group' => '1'));
        Ex::create(array('name' => 'BİLG.SARF MALZEMLERİ', 'group' => '1'));
        Ex::create(array('name' => 'VERGİ RESİM VE HARÇLAR', 'group' => '1'));
        Ex::create(array('name' => 'DİĞER ÇEŞİTLİ ', 'group' => '1'));
        Ex::create(array('name' => 'PERSONEL SAĞLIK', 'group' => '1'));
        Ex::create(array('name' => 'ŞEHİR İÇİ ULAŞIM', 'group' => '1'));
        Ex::create(array('name' => 'TEMSİL VE AĞIRLAMA', 'group' => '1'));
        Ex::create(array('name' => 'ALL-RİSK İŞYERİ SİGORTALAMA', 'group' => '1'));
        Ex::create(array('name' => 'BARINMA KİRALAR', 'group' => '1'));
        Ex::create(array('name' => 'DOĞALGAZ GİDERİ', 'group' => '1'));
        Ex::create(array('name' => 'ARAÇ SİGORTALAMA', 'group' => '1'));
        Ex::create(array('name' => 'KÜÇÜK DEMİRBAŞLAR', 'group' => '1'));
        Ex::create(array('name' => 'BÜRO BAKIM ONARIM', 'group' => '1'));
        Ex::create(array('name' => 'ŞANTİYE ARAÇ YAKIT', 'group' => '1'));
        Ex::create(array('name' => 'KURYE', 'group' => '1'));
        Ex::create(array('name' => 'GAZETE VE DERGİ', 'group' => '1'));
        Ex::create(array('name' => 'ARAÇ OTOPARK', 'group' => '1'));
        Ex::create(array('name' => 'ARAÇ BAKIM VE ONARIM', 'group' => '1'));
        Ex::create(array('name' => 'MATBAA ', 'group' => '1'));
        Ex::create(array('name' => 'ŞANTİYE PERSONEL MAAŞ ', 'group' => '1'));
        Ex::create(array('name' => 'BANKA KOMİSYON', 'group' => '1'));
        Ex::create(array('name' => 'KREDİ KARTI KOMİSYONLARI', 'group' => '1'));
        Ex::create(array('name' => 'SGK PRİM (ŞANTİYE PERSONEL)', 'group' => '1'));
        Ex::create(array('name' => 'YURT İÇİ/DIŞI ULAŞIM', 'group' => '1'));
        Ex::create(array('name' => 'ARAÇ VERGİ', 'group' => '1'));
        Ex::create(array('name' => 'SGK STOPAJ', 'group' => '1'));
        Ex::create(array('name' => 'OGS', 'group' => '1'));
        Ex::create(array('name' => 'KANUNEN KABUL EDİLMEYEN', 'group' => '1'));
        Ex::create(array('name' => 'YURTDIŞI ULAŞIM & KONAKLMA', 'group' => '1'));
        Ex::create(array('name' => 'ARAÇ KİRALAMA', 'group' => '1'));
        Ex::create(array('name' => 'KIDEM İHBAR', 'group' => '1'));
        Ex::create(array('name' => 'DİĞER HABERLEŞME GİD.', 'group' => '1'));

        Ex::create(array('name' => 'KİK PAYI ', 'group' => '2'));
        Ex::create(array('name' => 'All Risk Sigorta', 'group' => '2'));
        Ex::create(array('name' => 'Elektrik Odası Belge Gideri', 'group' => '2'));
        Ex::create(array('name' => 'Makina Odası Belge Gideri', 'group' => '2'));
        Ex::create(array('name' => 'İnşaat Müh Odası Belge Gideri', 'group' => '2'));
        Ex::create(array('name' => 'Ato Belge Gideri', 'group' => '2'));
        Ex::create(array('name' => 'Ticaret Sicil Belge Gideri', 'group' => '2'));
        Ex::create(array('name' => 'TEKNİK PERSONEL NOTER TAAHHÜTNAMESİ', 'group' => '2'));
        Ex::create(array('name' => 'BAYINDIRLIK YAPI MÜT.KAYIT NUMARA BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'ATO RUHSAT İÇİN EVRAK', 'group' => '2'));
        Ex::create(array('name' => 'İŞ GÜVENLİĞİ BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'OSGB HİZMETİ (AĞUSTOS-EYLÜL-….)', 'group' => '2'));
        Ex::create(array('name' => 'İADE OSGB', 'group' => '2'));
        Ex::create(array('name' => 'SÖZLEŞME VERGİSİ', 'group' => '2'));
        Ex::create(array('name' => 'DAMGA VERGİSİ', 'group' => '2'));
        Ex::create(array('name' => 'NOTER MASRAFI', 'group' => '2'));
        Ex::create(array('name' => 'KANAL KATILIM BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'YOL KATILIM BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'HAFRİYAT DÖKÜM BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'SU İDARESİ KEŞİF BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'AYKOME ALTYAPI HARÇLARI', 'group' => '2'));
        Ex::create(array('name' => 'ŞANTİYE ELEKTRİK ABONE BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'ŞANTİYE SU ABONE BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'ŞANTİYE DOĞALGAZ ABONE BEDELİ', 'group' => '2'));
        Ex::create(array('name' => 'YAPI KULLANIM İZİN BELGESİ GİDERLERİ', 'group' => '2'));
        Ex::create(array('name' => 'DİĞER', 'group' => '2'));

        Ex::create(array('name' => 'VİBRATÖR VE SARF MALZEMELERİ', 'group' => '3'));
        Ex::create(array('name' => 'MATKAP-KIRICI-DELİCİ VE SARF MALZEMELERİ', 'group' => '3'));
        Ex::create(array('name' => 'KÜREK', 'group' => '3'));
        Ex::create(array('name' => 'KAZMA', 'group' => '3'));
        Ex::create(array('name' => 'ÇEKİÇ', 'group' => '3'));
        Ex::create(array('name' => 'KESER', 'group' => '3'));
        Ex::create(array('name' => 'EL ARABASI', 'group' => '3'));
        Ex::create(array('name' => 'BALYOZ', 'group' => '3'));
        Ex::create(array('name' => 'MANİVELA', 'group' => '3'));
        Ex::create(array('name' => 'ÇİZME', 'group' => '3'));
        Ex::create(array('name' => 'İŞ AYAKKABISI', 'group' => '3'));
        Ex::create(array('name' => 'İŞ YELEĞİ', 'group' => '3'));
        Ex::create(array('name' => 'YAĞMURLUK', 'group' => '3'));
        Ex::create(array('name' => 'BARET', 'group' => '3'));
        Ex::create(array('name' => 'GÖZLÜK', 'group' => '3'));
        Ex::create(array('name' => 'MASKE', 'group' => '3'));
        Ex::create(array('name' => 'MALA', 'group' => '3'));
        Ex::create(array('name' => 'KALIP TAKVİYE (KELEBEK-ÇİROZ-TİJ TAKIMI)', 'group' => '3'));
        Ex::create(array('name' => 'DİĞER', 'group' => '3'));

        Ex::create(array('name' => 'PROFİL MUHTELİF DEMİR', 'group' => '4'));
        Ex::create(array('name' => 'BETON', 'group' => '4'));
        Ex::create(array('name' => 'ÇİMENTO', 'group' => '4'));
        Ex::create(array('name' => 'KİREÇ', 'group' => '4'));
        Ex::create(array('name' => 'ALÇI', 'group' => '4'));
        Ex::create(array('name' => 'KUM', 'group' => '4'));
        Ex::create(array('name' => 'ÇAKIL', 'group' => '4'));
        Ex::create(array('name' => 'MİL KUM', 'group' => '4'));
        Ex::create(array('name' => 'TUĞLA', 'group' => '4'));
        Ex::create(array('name' => 'BİMS BLOK', 'group' => '4'));
        Ex::create(array('name' => 'GAZ BETON', 'group' => '4'));
        Ex::create(array('name' => 'KERESTE', 'group' => '4'));
        Ex::create(array('name' => 'KİREMİT', 'group' => '4'));
        Ex::create(array('name' => 'BOYA', 'group' => '4'));
        Ex::create(array('name' => 'KARO SERAMİK', 'group' => '4'));
        Ex::create(array('name' => 'DİĞER', 'group' => '4'));
    }
}