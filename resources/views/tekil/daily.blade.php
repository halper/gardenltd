<?php
use App\Library\Weather;
use Carbon\Carbon;
use App\Staff;
use Illuminate\Support\Facades\Session;
$my_weather = new Weather;
if (session()->has("data")) {
    $report_date = session('data')["date"];
}

$today = Carbon::now()->toDateString();

if (session()->has("staff_array")) {
    $staff_array = session('staff_array');
}
if (session()->has("quantity_array")) {
    $quantity_array = session('quantity_array');
}
$locked = true;
if ($report->admin_lock == 0) {
    $locked = false;
} else if ($report->created_at < $today) {
    $locked = true;
} else if (!$report->locked()) {
    $locked = false;
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

            $(".remove_row").on("click", function (e) { //user click on remove text
                e.preventDefault();
                $(this).parent().closest('td').parent().closest('tr').remove();
            })
        });


    </script>
    <?php
    $staff_options = '';
    $management_depts = new \App\Department();

    foreach ($management_depts->management() as $dept) {
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

                    $(wrapper).append('<div class="row"><div class="col-sm-8"><div class="form-group">' +
                    '<select name="staffs[]" class="js-additional-staff form-control">' +
$staff_options
            '</select></div></div>' +
                '<div class="col-sm-3"><input type="number" class="form-control" name="contractor-quantity[]"/></div>'+
                '<div class="col-sm-1"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div></div>'); //add input box
                $(".js-additional-staff").select2();

            });

            $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').remove();
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
    $staffs = Staff::all()
    ?>

    <div class="row">
        {{--Personel icmal tablosu--}}
        <div class="col-xs-12 col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Personel İcmal Tablosu</h3>

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
                                <div class="text-center">
                                    <div class="col-sm-10">
                                        <span><strong>PERSONEL İCMALİ</strong></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <span><strong>TOPLAM</strong></span>
                                    </div>
                                </div>
                            </div>

                            {!! Form::model($report, [
                            'url' => "/tekil/$site->slug/add-management-staffs",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'managementStaffInsertForm',
                            'role' => 'form'
                            ]) !!}

                            <div class="row {{($locked && empty($report->employer_staff)) ? "hidden" : ""}}">
                                <div class="form-group">

                                    <div class="col-sm-10">
                                        <label for="employer_staff" class="control-label">İşveren ({{$site->employer}}
                                            )</label>
                                    </div>

                                    <div class="col-sm-2 text-center">
                                        @if(!$locked)
                                            {!! Form::number('employer_staff', null, ['class' => 'form-control'])  !!}
                                        @else
                                            <span class="input">{{$report->employer_staff}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row {{($locked && empty($report->management_staff)) ? "hidden" : ""}}">
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <label for="management_staff" class="control-label">Proje Yönetimi
                                            ({{$site->management_name}})</label>
                                    </div>

                                    <div class="col-sm-2 text-center">
                                        @if(!$locked)
                                            {!! Form::number('management_staff', null, ['class' => 'form-control'])  !!}
                                        @else
                                            <span class="input">{{$report->management_staff}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row {{($locked && empty($report->management_name)) ? "hidden" : ""}}">
                                <div class="form-group">

                                    <div class="col-sm-10">
                                        <label for="building_control_staff" class="control-label">Yapı Denetim
                                            ({{$site->building_control}}
                                            )</label>
                                    </div>

                                    <div class="col-sm-2 text-center">
                                        @if(!$locked)
                                            {!! Form::number('building_control_staff', null, ['class' => 'form-control'])  !!}
                                        @else
                                            <span class="input">{{$report->building_control_staff}}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <?php
                            $total_management = 0;
                            if (!empty($report->management_staff))
                                $total_management += $report->management_staff;
                            if (!empty($report->building_control_staff))
                                $total_management += $report->building_control_staff;
                            if (!empty($report->employer_staff))
                                $total_management += $report->employer_staff;

                            if ($total_management > 0) {
                                echo <<<EOT
<div class="row">
<div class="col-sm-10" style="text-align:right">
<span><strong>TOPLAM: </strong></span>
</div>
<div class="col-sm-2 text-center">
$total_management
</div>
</div>
EOT;
                            }

                            ?>
                            <div class="row {{$report->locked() ? "hidden" : ""}}">
                                <div class="col-sm-12">
                                    <div class="pull-right">
                                        <button type="submit" class="btn btn-success btn-flat">
                                            Kaydet
                                        </button>
                                    </div>
                                </div>

                            </div>
                            {!! Form::close() !!}


                        </div>
                    </div>
                </div>
            </div>
            {{--End of Personel icmal table--}}


            {{--Main contractor table--}}
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{$site->main_contractor}}
                                <small style="color: #f0f0f0;">(Ana Yüklenici)</small>
                                Personel Tablosu
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
                            @if(!$locked)
                                Falanca İnşaat için personel giriniz.
                                <br>

                                <div class="row">
                                    <div class="text-center">
                                        <div class="col-sm-8">
                                            <span><strong>PERSONEL</strong></span>
                                        </div>
                                        <div class="col-sm-3">
                                            <span><strong>SAYISI</strong></span>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::open([
                                'url' => "/tekil/$site->slug/save-staff",
                                'method' => 'POST',
                                'class' => 'form',
                                'id' => 'staffInsertForm',
                                'role' => 'form'
                                ]) !!}
                                {!! Form::hidden('report_id', $report->id) !!}
                                <div id="staff-insert">
                                    @foreach($report->staff()->get() as $staff)
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <span>{{$staff->staff}}</span>
                                                    {!! Form::hidden('staffs[]', $staff->id) !!}
                                                </div>
                                            </div>

                                            <div class="col-sm-3">

                                                <input type="number" class="form-control" name="contractor-quantity[]"
                                                       value="{{$staff->pivot->quantity}}"/>
                                            </div>
                                            <div class="col-sm-1"><a href="#" class="remove_field"><i
                                                            class="fa fa-close"></i></a></div>
                                        </div>
                                    @endforeach

                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <select name="staffs[]" class="js-example-basic-single form-control">

                                                    {!! $staff_options !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">

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
                            @else
                                <div class="row">
                                    <div class="col-sm-11 text-center">

                                        <?php
                                        $main_contractor_total = 0;
                                        $j = 0;
                                        $max_explosion = 0;
                                        foreach ($report->staff()->get() as $staff) {
                                            $pos = strpos($staff->staff, " ");


                                            if ($pos === false) {
                                                // string needle NOT found in haystack

                                            } else {
                                                // string needle found in haystack
                                                $staff_words = explode(" ", $staff->staff);
                                                if (sizeof($staff_words) > $max_explosion) {
                                                    $max_explosion = sizeof($staff_words);
                                                }
                                            }
                                        }
                                        ?>
                                        @foreach($report->staff()->get() as $staff)
                                            <?php
                                            $vowels = ["a", "e", "ı", "i", "o", "ö", "u", "ü"];
                                            $pos = strpos($staff->staff, " ");
                                            $staff_name = "";
                                            $br_string = "";



                                            if ($pos === false) {
                                                // string needle NOT found in haystack
                                                $staff_name = $staff->staff;
                                                for ($k = 1; $k < $max_explosion; $k++) {
                                                    $br_string .= "<br>";
                                                }
                                            } else {
                                                // string needle found in haystack
                                                $staff_words = explode(" ", $staff->staff);
                                                $i = 1;
                                                foreach ($staff_words as $word) {
                                                    if (strlen($word) > 5) {
                                                        $cut = in_array(mb_substr($word, 3, 1), $vowels) ? 3 : 4;
                                                        $staff_name .= mb_substr($word, 0, $cut, 'utf-8') . ".";
                                                    } else {
                                                        $staff_name .= $word;
                                                    }
                                                    if ($i < sizeof($staff_words)) {
                                                        $staff_name .= " ";
                                                        $i++;
                                                    }
                                                }
                                                for ($k = 0; $k < $max_explosion - sizeof($staff_words); $k++) {
                                                    $br_string .= "<br>";
                                                }
                                            }
                                            $x = sizeof($report->staff()->get()) - ((int)floor($j / 12)) * 12;
                                            if ($x >= 12) {
                                                $class_size = 1;
                                            } else {
                                                $class_size = $x % 12;
                                                switch ($class_size) {
                                                    case(0):
                                                    case(7):
                                                    case(8):
                                                    case(9):
                                                    case(10):
                                                    case(11):
                                                        $class_size = 1;
                                                        break;
                                                    case(1):
                                                    case(2):
                                                    case(3):
                                                    case(4):
                                                    case(6):
                                                        $class_size = 12 / $x;
                                                        break;
                                                    case(5):
                                                        $class_size = 2;
                                                        break;

                                                }
                                            }
                                            ?>
                                            {!! $j % 12 == 0 ? "<div class='row'>" : "" !!}

                                            <div class="col-sm-{{$class_size}}">
                                                <span><strong>{{$staff_name}}</strong></span>
                                                <br>
                                                {!! $br_string !!}
                                                <span>{{$staff->pivot->quantity}}</span>
                                            </div>
                                            {!! ($j % 12 == 11 || $j+1==sizeof($report->staff()->get())) ? "</div>" : "" !!}
                                            <?php
                                            $main_contractor_total += $staff->pivot->quantity;
                                            $j++;
                                            ?>


                                        @endforeach
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="row text-center">
                                            <span><strong>TOPLAM</strong></span>
                                            <br>
                                            <span>{{$main_contractor_total}}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            {{--End of Main contractor table--}}

            {{--Subcontractors table--}}
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Alt Yükleniciler Personel Tablosu
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
                            <?php
                            $subcontractor_staffs = \App\Staff::where('department_id', '3')->get();
                            $subcontractors = \App\Subcontractor::where('contract_start_date', '<=', $today)->where('contract_end_date', '>', $today)->get();
                            $subcontractor_staff_total = 0;
                            ?>
                            @if(!$locked)

                                <table class="table table-responsive table-condensed">
                                    {!! Form::open([
                                'url' => "/tekil/$site->slug/save-subcontractor-staff",
                                'method' => 'POST',
                                'class' => 'form',
                                'id' => 'subcontractorStaffInsertForm',
                                'role' => 'form'
                                ]) !!}
                                    {!! Form::hidden('report_id', $report->id) !!}
                                    <thead>
                                    <tr>
                                        <th>ALT YÜKLENİCİ</th>

                                        @foreach($subcontractor_staffs as $sub_staff)
                                            <th>{{$sub_staff->staff}}</th>
                                        @endforeach
                                        <th>TOPLAM</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($subcontractors as $subcontractor)
                                        <tr>
                                            <td><a href="#" class="remove_row"><i
                                                            class="fa fa-close"></i></a>{{$subcontractor->name}}

                                                @foreach($subcontractor->manufacturing()->get() as $manufacture)
                                                    <?php
                                                    $sub_row_total = 0;
                                                    ?>
                                                    <br> ({{mb_strtoupper($manufacture->name, 'utf8')}})
                                                @endforeach
                                            </td>
                                            {!! Form::hidden("subcontractors[]", $subcontractor->id)!!}
                                            @for($i = 0; $i<sizeof($subcontractor_staffs); $i++)

                                                <td style="vertical-align: middle">

                                                    {!! Form::text($subcontractor_staffs[$i]->id . "[]", empty($report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity) ? null : $report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity, ['class' => 'form-control']) !!}</td>
                                                <?php
                                                $sub_row_total += empty($report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity) ? 0 : $report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity;
                                                $subcontractor_staff_total += $sub_row_total;
                                                ?>
                                            @endfor
                                            <td class="text-center"
                                                style="vertical-align: middle">{{$sub_row_total}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group pull-right">

                                            <button type="submit" class="btn btn-success btn-flat ">
                                                Kaydet
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                {!! Form::close() !!}
                            @else

                                <table class="table table-responsive table-condensed">

                                    <thead>
                                    <tr>
                                        <th>ALT YÜKLENİCİ</th>
                                        @foreach($subcontractor_staffs as $sub_staff)
                                            <th>{{$sub_staff->staff}}</th>
                                        @endforeach
                                        <th>TOPLAM</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($subcontractors as $subcontractor)
                                        <?php
                                        $sub_row_total = 0;
                                        ?>
                                        <tr>
                                            <td>
                                                {{$subcontractor->name}}
                                                @foreach($subcontractor->manufacturing()->get() as $manufacture)
                                                    <br> ({{mb_strtoupper($manufacture->name, 'utf8')}})
                                                @endforeach
                                            </td>
                                            @for($i = 0; $i<sizeof($subcontractor_staffs); $i++)

                                                <td style="vertical-align: middle">

                                                    {{empty($report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity) ? "" : $report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity }}</td>
                                                <?php
                                                $sub_row_total += empty($report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity) ? 0 : $report->staff()->where('subcontractor_id', $subcontractor->id)->find($subcontractor_staffs[$i]->id)->pivot->quantity;
                                                $subcontractor_staff_total += $sub_row_total;
                                                ?>
                                            @endfor
                                            <td class="text-center"
                                                style="vertical-align: middle">{{$sub_row_total}}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-11">
                                        <p class="text-right"><strong>ALT YÜKLENİCİ TOPLAMI</strong></p>
                                    </div>
                                    <div class="col-sm-1">
                                        <p class="text-left">{{$subcontractor_staff_total}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-11">
                                        <p class="text-right"><strong>ANA YÜKLENİCİ & ALT YÜKLENİCİ TOPLAMI</strong></p>
                                    </div>
                                    <div class="col-sm-1">
                                        <p class="text-center">{{$main_contractor_total + $subcontractor_staff_total}}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-11">
                                        <p class="text-right"><strong>GENEL TOPLAM</strong></p>
                                    </div>
                                    <div class="col-sm-1">
                                        <p class="text-center">{{$main_contractor_total + $subcontractor_staff_total + $total_management}}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{--End of subcontractors table--}}

        </div>
        {{--End of left tables column--}}


        <div class="col-xs-12 col-md-4">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <thead>
                            <tr>
                                <th>EKİPMAN ADI</th>
                                <th>ÇALIŞAN</th>
                                <th>MEVCUT</th>
                                <th>ARIZALI</th>
                                <th>TOPLAM</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($site->equipment()->get() as $eq)
                                <tr>
                                    <td>{{$eq->name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                            @endforeach
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

@stop