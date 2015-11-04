@extends('tekil/layout')

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Şantiye Günlük Raporu</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <tbody>

                            <tr>
                                <td></td>

                                <td><strong>PROJE ADI:</strong></td>
                                <td>{{$site->job_name}}</td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                            <tr>
                                <td></td>
                                <td><strong>TARİH:</strong></td>
                                <td>{{Carbon\Carbon::today('Europe/istanbul')->format('d.m.Y')}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <?php
                                use Cmfcmf\OpenWeatherMap;

                                $time = strtotime($site->end_date);
                                $myFormatForView = date("d.m.Y", $time);

                                $start_date = date_create($site->start_date);
                                $now = date_create();
                                $end_date = date_create($site->end_date);
                                $left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));
                                $owm = new OpenWeatherMap();

                                $weather = $owm->getWeather('Ankara', 'metric', 'tr');

                                {{--try {
                                } catch(OWMException $e) {
                                    echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
                                    echo "<br />\n";
                                } catch(\Exception $e) {
                                    echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
                                    echo "<br />\n";
                                }--}}

                                ?>
                                <td><strong>İŞ BİTİM TARİHİ:</strong></td>
                                <td>{{$myFormatForView}}</td>
                                <td></td>
                                <td></td>
                                <td><strong>KALAN SÜRE:</strong></td>
                                <td>{{$left}} gün</td>
                            </tr>
                            <tr>

                            </tr>
                            <tr>
                                <td><strong>HAVA DURUMU:</strong></td>
                                <td>Güneşli</td>
                                <td></td>
                                <td></td>
                                <td><strong>SICAKLIK:</strong></td>
                                <td>{{ $weather->temperature}}</td>
                            </tr>
                            <tr>
                                <td><strong>ŞANTİYE ŞEFİ:</strong></td>
                                <td>{{$site->site_chief}}</td>
                                <td></td>
                                <td></td>
                                <td><strong>ÇALIŞMA:</strong></td>
                                <td>Var</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-4">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>Görevi</th>
                        <th>Sayısı</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(App\Department::all() as $dept)
                        <tr>
                            <td><strong>{{mb_strtoupper($dept->department, 'utf-8')}} GRUBU</strong></td>
                            <td></td>
                        </tr>
                        @foreach($dept->staff()->get() as $staff)
                            <tr>
                                <td>{{mb_strtoupper($staff->staff, 'utf-8')}}</td>
                                <td>{{random_int(1,8)}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td>TOPLAM ŞANTİYE PERSONELİ</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>ŞANTİYE GENEL TOPLAMI</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xs-12 col-md-8">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>ŞANTİYE İŞ MAKİNELERİ</th>
                                <th>ÇALIŞ. SAATİ</th>
                                <th>SAYISI</th>
                            </tr>
                            </thead>
                            <tbody>
                            @for($i = 1; $i<8; $i++)

                                <tr>
                                    <td style="text-align: center">{{$i}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                </tr>

                            @endfor
                            <tr>
                                <td></td>
                                <td>ÇALIŞAN TOPLAM MAKİNA</td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <thead>
                            <tr>
                                <th>TAŞERON GRUBU</th>
                                <th>TAŞERON FİRMA</th>
                                <th>SAYISI</th>
                            </tr>
                            </thead>
                            <tbody>
                            @for($i = 1; $i<24; $i++)

                                <tr>
                                    @if($i%4==0)
                                        <td style="color: darkred">TOPLAM:</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td></td>
                                    <td></td>

                                </tr>

                            @endfor
                            <tr>
                                <td></td>
                                <td>ÇALIŞAN TOPLAM MAKİNA</td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ÇALIŞAN BİRİM</th>
                        <th>YAPILAN İŞLER</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i = 1; $i<6; $i++)

                        <tr>
                            <td>{{$i}}</td>
                            <td></td>
                            <td></td>
                        </tr>

                    @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>S.N</th>
                        <th>GELEN MALZEME</th>
                        <th>BİRİM</th>
                        <th>MİK.</th>
                        <th>AÇIKLAMASI</th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i = 1; $i<6; $i++)

                        <tr>
                            <td>{{$i}}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    @endfor
                    <tr>
                        <td></td>
                        <td>TOPLAM:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--<div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Personel Raporu</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Firma</th>
                                <th>Puantaj</th>
                                <th>Yapılan İş ve Mahali</th>
                                <th>Ödemeler</th>
                                <th>Malzeme Talep</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>Garden İnşaat</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Elektrik</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Tesisat</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Sıva</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Güvenlik</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Toplam</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Gelen Malzeme</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>Kimden</th>
                                <th>Malzeme Cinsi</th>

                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>Garden İnşaat</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Elektrik</td>
                                <td></td>

                            </tr>


                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

    </div>

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">İş Makinesi ve Ekipman</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">

                            <tbody>

                            <tr>
                                <td>Kule Vinç</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Kamyon</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Eskavatör</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Beko Loader</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Vinç</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>High Up</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Gırgır Vinç</td>
                                <td></td>

                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Şantiye Notları</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">

                            <tbody>

                            <tr>
                                <td></td>

                            </tr>

                            <tr>
                                <td></td>

                            </tr>

                            <tr>
                                <td></td>

                            </tr>

                            <tr>
                                <td></td>

                            </tr>


                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>--}}

@stop