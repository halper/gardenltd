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
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });


        $(document).ready(function () {
            $(".js-example-basic-single").select2();
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
    <?php
    $staff_options = '';
    foreach (App\Department::all() as $dept) {
        $staff_options .= "'<optgroup label=\"$dept->department\">'+\n";
        foreach ($dept->staff()->get() as $staff) {
            if (isset($staff_array) && in_array($staff->id, $staff_array))
                $staff_options .= "'<option value=\"$staff->id\" selected>" . mb_strtoupper($staff->staff, 'utf-8') . "</option>'+\n";
            else
                $staff_options .= "'<option value=\"$staff->id\">" . mb_strtoupper($staff->staff, 'utf-8') . "</option>'+\n";
        }
        $staff_options .= "'</optgroup>'+\n";
    }



    echo <<<EOT
<script>



    $(document).ready(function() {
            var wrapper         = $("#staff-insert"); //Fields wrapper
            var add_button      = $(".add-staff-row"); //Add button ID

            $(add_button).click(function(e){ //on add input button click
                e.preventDefault();

                    $(wrapper).append('<div class="row"><div class="col-sm-6"><div class="form-group">' +
                    '<select name="staffs[]" class="js-additional-staff form-control">' +
$staff_options
            '</select></div></div>' +
                '<div class="col-sm-5"><input type="number" class="form-control" name="contractor-quantity[]"/></div>'+
                '<div class="col-sm-1"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div></div>'); //add input box
                $(".js-additional-staff").select2();

            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault(); $(this).parent().closest('div.row').remove(); x--;
            })
        });
</script>
EOT;


    ?>
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$site->job_name}} Projesi Raporu
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
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-6">
                                    <span><strong>TANZİM EDEN: </strong></span>
                                </div>
                            <div class="col-sm-6">
                                    <span>{{Auth::User()->employer . " / " . Auth::User()->name}} </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
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
                    </div>
                    <div class="table-responsive">
                        <table class="table table-condensed">

                            <tbody>

                            <tr>
                                <td><strong>HAVA:</strong></td>
                                <td>{{$my_weather->getDescription()}}</td>
                                <td><strong>SICAKLIK:</strong></td>
                                <td>{!! $my_weather->getMin() ."<sup>o</sup>C / ". $my_weather->getMax() !!}
                                    <sup>o</sup>C
                                </td>
                                <td><strong>NEM:</strong></td>
                                <td>{!! $my_weather->getHumidity() ." %" !!}</td>
                                <td><strong>RÜZGAR:</strong></td>
                                <td>{{$my_weather->getWind()}} m/s</td>
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
        <div class="col-xs-12 col-md-8">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$site->main_contractor}} <small style="color: #f0f0f0;">(Ana Yüklenici)</small> Personel Tablosu</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    Falanca İnşaat için personel giriniz.
                    <br>

                    <div class="row">
                        <div class="text-center">
                            <div class="col-sm-6">
                                <span><strong>PERSONEL</strong></span>
                            </div>
                            <div class="col-sm-6">
                                <span><strong>SAYISI</strong></span>
                            </div>
                        </div>
                    </div>
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/add-staffs",
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'staffInsertForm',
                    'role' => 'form'
                    ]) !!}
                    <div id="staff-insert">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <select name="staffs[]" class="js-example-basic-single form-control">

                                        {!! $staff_options !!}
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">

                                <input type="number" class="form-control" name="contractor-quantity[]"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                        <div class="form-group pull-right">
                            <a href="#" class="btn btn-primary btn-flat add-staff-row">
                                Personel Ekle
                            </a>

                            <button type="submit" class="btn btn-success btn-flat ">
                                Kaydet
                            </button>
                        </div>
                        </div>

                    </div>
                    {!! Form::close() !!}


                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-4">
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