<?php
use App\Library\Weather;
$my_weather = new Weather;
if (session()->has("data")) {
    $report_date = session('data')["date"];
}

if (session()->has("staff_array")) {
    $staff_array = session('staff_array');
}
if (session()->has("quantity_array")) {
    $quantity_array = session('quantity_array');
}

?>
@extends('tekil/layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(".js-example-basic-multiple").select2({
            placeholder: "Eklemek istediğiniz personeli seçiniz",
            allowClear: true
        });

        $(document).ready(function () {
            $('#dateRangePicker').datepicker({
                autoclose: true,
                firstDay: 1,
                format: 'dd.mm.yyyy',
                startDate: '01.01.2015',
                endDate: '30.12.2100'
            });


            $("#dateRangePicker").datepicker().on('changeDate', function (ev) {

                if (ev.date.valueOf() > new Date()) {
                    $(this).closest("div").next("span").text('Bugünden ileri bir tarih seçemezsiniz!');
                    $(this).closest("div").next("span").addClass('text-danger');
                    $(this).parent().closest("div").addClass('has-error');
                    return false;

                }
                $('#dateRangeForm').submit();
            });
        });
    </script>
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Şantiye Günlük Raporu
                        @if(isset($report_date))
                            <small style="color: #f9f9f9">{{$report_date}}</small>
                        @endif
                    </h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        {!! Form::open([
                        'url' => "/tekil/$site->slug/select-date",
                        'method' => 'POST',
                        'class' => 'form form-horizontal',
                        'id' => 'dateRangeForm',
                        'role' => 'form'
                        ]) !!}

                        <div class="form-group">

                            <label class="col-xs-3 control-label">TARİH: </label>

                            <div class="col-xs-3 date">
                                <div class="input-group input-append date" id="dateRangePicker">
                                    <input type="text" class="form-control" name="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                                <span class="help-block"></span>
                            </div>
                        </div>


                        {!! Form::close() !!}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-condensed">

                            <tbody>

                            <tr>
                                <td class="col-xs-2"><strong>PROJE ADI:</strong></td>
                                <td class="col-xs-4">{{$site->job_name}}</td>
                                <td></td>
                                <td></td>
                                {{--<td>{{Carbon\Carbon::today('Europe/istanbul')->format('d.m.Y')}}</td>--}}


                            </tr>
                            <tr>
                                <td><strong>HAVA:</strong></td>
                                <td>{{$my_weather->getDescription()}}</td>
                                <td><strong>SICAKLIK:</strong></td>
                                <td>{!! $my_weather->getMin() ."<sup>o</sup>C / ". $my_weather->getMax() !!}
                                    <sup>o</sup>C
                                </td>
                            </tr>
                            <tr>
                                <?php


                                $time = strtotime($site->end_date);
                                $myFormatForView = date("d.m.Y", $time);

                                $start_date = date_create($site->start_date);
                                $now = date_create();
                                if (isset($report_date)) {
                                    $now = date_create($report_date);
                                }
                                $end_date = date_create($site->end_date);
                                $left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));


                                ?>
                                <td><strong>İŞ BİTİM TARİHİ:</strong></td>
                                <td>{{$myFormatForView}}</td>
                                <td><strong>KALAN SÜRE:</strong></td>
                                <td>{{$left}} gün</td>
                            </tr>
                            <tr>

                            </tr>
                            <tr>
                                <td><strong>NEM:</strong></td>
                                <td>{!! $my_weather->getHumidity() ." %" !!}</td>
                                <td><strong>RÜZGAR:</strong></td>
                                <td>{{$my_weather->getWind()}} m/s</td>

                            </tr>
                            <tr>
                                <td><strong>ŞANTİYE ŞEFİ:</strong></td>
                                <td>{{$site->site_chief}}</td>
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
    <?php
    $staffs = \App\Staff::all()
    ?>

    <div class="row">
        <div class="col-xs-12 col-md-4">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Personel</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    @if(isset($quantity_array))

                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>Görevi</th>
                                    <th>Sayısı</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $site_general_total = 0;
                                ?>
                                @for($i = 0; $i< sizeof($staff_array); $i++)
                                    <tr>
                                        <td>{{mb_strtoupper($staffs->get($staff_array[$i])->staff, 'utf-8')}}</td>
                                        <td>{{$quantity_array[$i]}}</td>
                                    </tr>
                                    <?php
                                    $site_general_total += $quantity_array[$i];
                                    ?>
                                @endfor

                                <tr>
                                    <td style="text-align: right"><strong>TOPLAM ŞANTİYE PERSONELİ:</strong></td>
                                    <td>{{sizeof($staff_array)}}</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right"><strong>ŞANTİYE GENEL TOPLAMI:</strong></td>
                                    <td>{{$site_general_total}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @else


                        @if(!isset($report_date))
                            <p>Aşağıdaki kutucuğu kullanarak personel ekleyebilirsiniz</p>

                            {!! Form::open([
                            'url' => "/tekil/$site->slug/add-staffs",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'staffInsertForm',
                            'role' => 'form'
                            ]) !!}

                            <div class="form-group">
                                <label for="staff">Personel seçimi: </label>
                                <select id="staff" name="staffs[]" class="js-example-basic-multiple form-control"
                                        multiple="multiple">

                                    @foreach(App\Department::all() as $dept)
                                        <optgroup label="{{$dept->department}}">
                                            @foreach($dept->staff()->get() as $staff)
                                                @if(isset($staff_array) && in_array($staff->id,$staff_array))
                                                    <option value="{{$staff->id}}"
                                                            selected>{{mb_strtoupper($staff->staff, 'utf-8')}}</option>
                                                @else
                                                    <option value="{{$staff->id}}">{{mb_strtoupper($staff->staff, 'utf-8')}}</option>
                                                @endif


                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-flat">
                                    Personel Ekle
                                </button>
                            </div>
                            {!! Form::close() !!}
                            @if(isset($staff_array))
                                <br/>
                                <div class="table-responsive">
                                    {!! Form::open([
                                    'url' => "/tekil/$site->slug/save-staff",
                                    'method' => 'POST',
                                    'class' => 'form',
                                    'id' => 'materialDemandForm',
                                    'role' => 'form'
                                    ]) !!}
                                    <table class="table table-bordered table-condensed" id="staffInsert">
                                        <thead>
                                        <tr>
                                            <th>Görevi</th>
                                            <th>Sayısı</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @for($i = 0; $i<sizeof($staff_array); $i++)
                                            <tr>
                                                <td>
                                                    {{mb_strtoupper($staffs->find($staff_array[$i])->staff, 'utf-8')}}
                                                    <input type="hidden" name="staffs[]"
                                                           value="{{$staffs->find($staff_array[$i])->id}}">
                                                </td>

                                                <td>
                                                    <br/>

                                                    <div class="form-group">
                                                        {!! Form::input('number', 'quantity[]', null, ['class' =>
                                                        'form-control']) !!}
                                                        <span></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endfor
                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
                                    </div>
                                    {!! Form::close() !!}
                                </div>

                            @endif

                        @else

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
                                            <td><strong>{{mb_strtoupper($dept->department, 'utf-8')}} GRUBU</strong>
                                            </td>
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
                        @endif
                    @endif
                </div>
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