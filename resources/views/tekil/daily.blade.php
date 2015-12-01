<?php
use App\Library\TurkishChar;use App\Library\Weather;
use App\Material;use Carbon\Carbon;
use App\Staff;
use Illuminate\Support\Facades\Session;
$my_weather = new Weather;
$weather_symbol = '';

if (strpos($my_weather->getDescription(), 'Kapalı') !== false) {
    $weather_symbol = '<i class="wi wi-day-cloudy"></i>';
} else if (strpos($my_weather->getDescription(), 'Hafif kar yağışlı') !== false) {
    $weather_symbol = '<i class="wi wi-day-snow"></i>';
} else if (strpos($my_weather->getDescription(), 'Hafif yağmur') !== false) {
    $weather_symbol = '<i class="wi wi-day-sprinkle"></i>';
} else if (strpos($my_weather->getDescription(), 'Şiddetli yağmur') !== false) {
    $weather_symbol = '<i class="wi wi-day-thunderstorm"></i>';
} else if (strpos($my_weather->getDescription(), 'Orta şiddetli yağmur') !== false) {
    $weather_symbol = '<i class="wi wi-day-hail"></i>';
} else if (strpos($my_weather->getDescription(), 'Açık') !== false) {
    $weather_symbol = '<i class="wi wi-day-sunny"></i>';
}

if (session()->has("data")) {
    $report_date = session('data')["date"];
}



$today = Carbon::now()->toDateString();

$locked = true;
if ($report->admin_lock == 0) {
    $locked = false;
} else if ($report->created_at < $today) {
    $locked = true;
} else if (!$report->locked()) {
    $locked = false;
}

$staffs = Staff::all();

$report_staff_arr = [];
foreach ($report->staff()->get() as $report_staff) {
    array_push($report_staff_arr, $report_staff->id);
}

$report_equipment_arr = [];
foreach ($report->equipment()->get() as $report_equipment) {
    array_push($report_equipment_arr, $report_equipment->id);
}

$inmaterials = $report->inmaterial()->get();

$time = strtotime($site->end_date);
$myFormatForView = date("d.m.Y", $time);

$start_date = date_create($site->start_date);
$now = date_create();
if (isset($report_date)) {
    $now = date_create($report_date);
}
$end_date = date_create($site->end_date);
$left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));
$total_date = str_replace("+", "", date_diff($start_date, $end_date)->format("%R%a"));

$subcontractors = $report->subcontractor()->get();
$all_subcontractors = $site->subcontractor()->get();
$report_subcontractor_arr = [];
foreach ($subcontractors as $report_subcontractor) {
    array_push($report_subcontractor_arr, $report_subcontractor->id);
}
$subcontractor_options = "<option></option>";
$subcontractor_options_js = "";
foreach ($all_subcontractors as $subcontractor) {
    $subcontractor_options_js .= "'<option value=\"$subcontractor->id\">" . TurkishChar::tr_up($subcontractor->name) . "</option>'+\n";
    if (isset($report_subcontractor_arr)) {
        if (!in_array($subcontractor->id, $report_subcontractor_arr)) {
            $subcontractor_options .= "<option value=\"$subcontractor->id\">" . TurkishChar::tr_up($subcontractor->name) . "</option>";
        }
    } else {
        $subcontractor_options .= "<option value=\"$subcontractor->id\">" . TurkishChar::tr_up($subcontractor->name) . "</option>";
    }
}

$subcontractor_staffs = \App\Substaff::all();
$subcontractor_staff_total = 0;

$report_site_photo_files = $report->rfile()->join('files', 'files.id', '=', 'rfiles.file_id')->
select("files.id", "name", "path")->where("type", "=", 0)->get();
$report_site_receipt_files = $report->rfile()->join('files', 'files.id', '=', 'rfiles.file_id')->
select("files.id", "name", "path")->where("type", "=", 1)->get();

$site_photo_files = $site->rfile()->join('files', 'files.id', '=', 'rfiles.file_id')->
select("files.id", "name", "path")->where("type", "=", 0)->get();
$site_receipt_files = $site->rfile()->join('files', 'files.id', '=', 'rfiles.file_id')->
select("files.id", "name", "path")->where("type", "=", 1)->get();

$site_reports = $site->report()->get();
$report_no = 1;
foreach ($site_reports as $site_report) {
    $report_no++;
    if ($report->id == $site_report->id) {
        break;
    }
}

?>
@extends('tekil/layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/dropzone.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/weather-icons.min.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/dropzone.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>
    <script>
        function removeFiles(fid, rid) {

            var fileId = fid;
            var reportId = rid;
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-files"}}',
                data: {
                    "fileid": fileId,
                    "reportid": reportId
                }
            });

            var linkID = "lb-link-" + fid;
            $('#' + linkID).remove();

        }
        function removeSubcontractor(subid) {

            var subcontractorId = subid;
            var reportId = {{$report->id}};
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-report-subcontractor"}}',
                data: {
                    "subcontractorid": subcontractorId,
                    "reportid": reportId
                }
            }).success(function () {
                $('#div-' + subid).remove();
            });
        }

        function subcontractorToWorkDelete(id) {
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-swunit"}}',
                data: {
                    "swid": id
                }
            }).success(function () {
                $('#div-swid' + id).remove();
            });
        }

        function staffToWorkDelete(id) {
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-pwunit"}}',
                data: {
                    "pwid": id
                }
            }).success(function () {
                $('#div-pwid' + id).remove();
            });
        }

        function staffDetach(id) {
            var reportId = {{$report->id}};
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/detach-staff"}}',
                data: {
                    "staffid": id,
                    "report_id": reportId
                }
            }).success(function () {
                $('#div-staffid' + id).remove();
            });
        }

        function equipmentDetach(id) {
            var reportId = {{$report->id}};
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/detach-equipment"}}',
                data: {
                    "equipmentid": id,
                    "report_id": reportId
                }
            }).success(function () {
                $('#div-equipmentid' + id).remove();
            });
        }

        function inmaterialsDelete(id) {
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-inmaterial"}}',
                data: {
                    "inmaterialid": id
                }
            }).success(function () {
                $('#div-inmaterialid' + id).remove();
            });
        }


        Dropzone.options.fileInsertForm = {
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, response) {
                    file.serverId = response.id;
                    file.reportId = response.rid;

                });
                this.on("removedfile", function (file) {
                    var name = file.name;

                    $.ajax({
                        type: 'POST',
                        url: '{{"/tekil/$site->slug/delete-files"}}',
                        data: {
                            "fileid": file.serverId,
                            "reportid": file.reportId
                        }
                    });
                });
            }
        };
        Dropzone.options.receiptInsertForm = {
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, response) {
                    file.serverId = response.id;
                    file.reportId = response.rid;

                });
                this.on("removedfile", function (file) {
                    var name = file.name;

                    $.ajax({
                        type: 'POST',
                        url: '{{"/tekil/$site->slug/delete-files"}}',
                        data: {
                            "fileid": file.serverId,
                            "reportid": file.reportId
                        }
                    });
                });
            }
        };

        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

        $(".js-example-basic-multiple").select2({
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });

        $(document).ready(function () {

            $(".radio-inline").on("click", function () {
                $("#selectIsWorkingForm").submit();
            });
            $(".js-example-basic-single").select2();
            $(".js-example-responsive").select2({
                placeholder: "Alt yüklenici seçiniz",
                allowClear: true
            });
            $(".js-example-responsive").on("select2:select", function () {
                $(".add-subcontractor_staff-row").show();
            });
            $(".js-example-responsive").on("select2:unselect", function () {
                $(".add-subcontractor_staff-row").hide();
            });


            $('#dateRangePicker').datepicker({
                autoclose: true,
                firstDay: 1,
                format: 'dd.mm.yyyy',
                startDate: '01.01.2015',
                endDate: '30.12.2100'
            });

            var leftDays = '{{$left}}';
            leftDays = parseInt(leftDays);
            if (leftDays < 10) {
                $('#leftDaysModal').modal("show");
            }

            $("#dateRangePicker").datepicker().on('changeDate', function (ev) {

                if (ev.date.valueOf() > new Date()) {
                    $(this).closest("div").next("span").text('Bugünden ileri bir tarih seçemezsiniz!');
                    $(this).closest("div").next("span").addClass('text-danger');
                    $(this).parent().closest("div").addClass('has-error');
                    return false;

                }
                $('#dateRangeForm').submit();
            });

            $("#dateRangePicker > input").val("{{isset($report_date) ? $report_date : App\Library\CarbonHelper::getTurkishDate($today)}}")

            $(".remove_row").on("click", function (e) { //user click on remove text
                e.preventDefault();
                $(this).parent().closest('td').parent().closest('tr').remove();
            })
        });


    </script>
    <?php

    $staff_options = '';
    $staff_options_js = '';
    $staff_options_js_all = '';
    $management_depts = new \App\Department();

    foreach ($management_depts->management() as $dept) {
        $staff_options .= "<optgroup label=\"$dept->department\">";
        $staff_options_js .= "'<optgroup label=\"$dept->department\">'+\n";
        foreach ($dept->staff()->get() as $staff) {
            $staff_options_js_all .= "'<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>'+\n";
            if (isset($report_staff_arr)) {
                if (!in_array($staff->id, $report_staff_arr)) {
                    $staff_options .= "<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>";
                    $staff_options_js .= "'<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>'+\n";
                }
            } else {
                $staff_options .= "<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>";
                $staff_options_js .= "'<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>'+\n";
            }
        }

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
$staff_options_js
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




    $equipment_options = '';
    $equipment_options_js = '';


    foreach ($site->equipment()->get() as $equipment) {
        if (isset($report_equipment_arr)) {
            if (!in_array($equipment->id, $report_equipment_arr)) {
                $equipment_options .= "<option value=\"$equipment->id\">" . TurkishChar::tr_up($equipment->name) . "</option>";
                $equipment_options_js .= "'<option value=\"$equipment->id\">" . TurkishChar::tr_up($equipment->name) . "</option>'+\n";
            }
        } else {
            $equipment_options .= "<option value=\"$equipment->id\">" . TurkishChar::tr_up($equipment->name) . "</option>";
            $equipment_options_js .= "'<option value=\"$equipment->id\">" . TurkishChar::tr_up($equipment->name) . "</option>'+\n";
        }
    }

    echo <<<EOT
<script>



    $(document).ready(function() {
            var equipment_wrapper         = $("#equipment-insert"); //Fields wrapper
            var add_equipment_button      = $(".add-equipment-row"); //Add button ID

            $(add_equipment_button).click(function(e){ //on add input button click
                e.preventDefault();

                    $(equipment_wrapper).append('<div class="row"><div class="col-sm-6"><div class="form-group">' +
                    '<div class="row"><div class="col-sm-2"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                    '<div class="col-sm-10"><select name="equipments[]" class="js-additional-equipment form-control">' +
$equipment_options_js
            '</select></div></div></div></div>' +
                '<div class="col-sm-2"><input type="number" class="form-control" name="equipment-present[]"/></div>'+
                '<div class="col-sm-2"><input type="number" class="form-control" name="equipment-working[]"/></div>'+
                '<div class="col-sm-2"><input type="number" class="form-control" name="equipment-broken[]"/></div></div>'); //add input box
                $(".js-additional-equipment").select2();

            });

            $(equipment_wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.row').remove();
            })
        });
</script>
EOT;

    $inmaterial_options = "";
    $inmaterial_options_js = "";


    foreach (Material::all() as $inmaterial) {
        $inmaterial_options .= "<option value=\"$inmaterial->id\">" . TurkishChar::tr_up($inmaterial->material) . "</option>";
        $inmaterial_options_js .= "'<option value=\"$inmaterial->id\">" . TurkishChar::tr_up($inmaterial->material) . "</option>'+\n";
    }


    echo <<<EOT
            <script>
$(document).ready(function() {
            var inmaterial_wrapper         = $("#inmaterial-insert"); //Fields wrapper
            var add_inmaterial_button      = $(".add-inmaterial-row"); //Add button ID

            $(add_inmaterial_button).click(function(e){ //on add input button click
                e.preventDefault();

                    $(inmaterial_wrapper).append('<div class="row"><div class="col-sm-2"><div class="form-group">' +
                    '<div class="row"><div class="col-sm-2"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                    '<div class="col-sm-10"><select name="inmaterials[]" class="js-additional-inmaterial form-control">' +
$inmaterial_options_js
            '</select></div></div></div></div>' +
                '<div class="col-sm-2"><input type="text" class="form-control" name="inmaterial-from[]"/></div>'+
                '<div class="col-sm-1"><input type="text" class="form-control" name="inmaterial-unit[]"/></div>'+
                '<div class="col-sm-1"><input type="number" class="form-control" name="inmaterial-quantity[]"/></div>'+
                '<div class="col-sm-6"><input type="text" class="form-control" name="inmaterial-explanation[]"/></div></div>'); //add input box
                $(".js-additional-inmaterial").select2();

            });

            $(inmaterial_wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.row').remove();
            })
        });
</script>
EOT;

    $subcontractor_staff_options = "";
    $subcontractor_staff_options_js = "";
    foreach ($subcontractor_staffs as $subcontractor_staff) {
        $subcontractor_staff_options .= "<option value=\"$subcontractor_staff->id\">" . TurkishChar::tr_up($subcontractor_staff->name) . "</option>";
        $subcontractor_staff_options_js .= "'<option value=\"$subcontractor_staff->id\">" . TurkishChar::tr_up($subcontractor_staff->name) . "</option>'+\n";
    }

    echo <<<EOT
            <script>
$(document).ready(function() {
            var subcontractor_staff_wrapper         = $("#subcontractor_staff-insert"); //Fields wrapper
            var add_subcontractor_staff_button      = $(".add-subcontractor_staff-row"); //Add button ID

            $(add_subcontractor_staff_button).click(function(e){ //on add input button click
                $(subcontractor_staff_wrapper).append('<div class="form-group"><div class="row">' +
                '<div class="col-sm-1"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>'+
                '<div class="col-sm-7">' +
                    '<select name="subcontractor_staffs[]" class="js-additional-subcontractor_staff form-control">' +
$subcontractor_staff_options_js
            '</select></div>' +
                '<div class="col-sm-4"><input type="number" placeholder="Personel sayısı giriniz" class="form-control" name="substaff-quantity[]"/></div>'+
                '</div></div>'); //add input box
                $(".js-additional-subcontractor_staff").select2();

            });

            $(subcontractor_staff_wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.form-group').remove();
            })
        });
</script>
EOT;
    ?>

    <script>
        $(document).ready(function () {

            var subcontractorStaffWrapper = $("#subcontractor-to-work-insert"); //Fields wrapper
            var addSubcontractorStaffButton = $(".add-subcontractor-to-work-done-row"); //Add button ID

            $(addSubcontractorStaffButton).click(function (e) { //on add input button click
                $(subcontractorStaffWrapper).append('<div class="form-group"><div class="row"><div class="col-sm-2">' +
                        '<div class="row"><div class="col-sm-2"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                        '<div class="col-sm-10">' +
                        '<select name="subcontractors[]" class="js-additional-subcontractor_staff form-control">' +
                        {!! $subcontractor_options_js !!}
                '</select></div></div></div>' +
                        '<div class="col-sm-1"><input type="number" class="form-control" name="subcontractor_quantity[]"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="form-control" name="subcontractor_unit[]"/></div>' +
                        '<div class="col-sm-6"><textarea class="form-control" name="subcontractor_work_done[]" rows="3"/></div>' +
                        '<div class="col-sm-1"><input type="number" class="form-control" name="subcontractor_planned[]"/></div>' +
                        '<div class="col-sm-1"><input type="number" class="form-control" name="subcontractor_done[]"/></div>' +
                        '</div></div>'); //add input box
                $(".js-additional-subcontractor_staff").select2();

            });

            $(subcontractorStaffWrapper).on("click", ".remove_field", function (e) { //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.form-group').remove();
            });
        });


        var staffToWorkDoneWrapper = $("#staff-to-work-insert"); //Fields wrapper
        var addStaffToWorkDone = $(".add-staff-to-work-done-row"); //Add button ID

        $(addStaffToWorkDone).click(function (e) { //on add input button click
            $(staffToWorkDoneWrapper).append('<div class="form-group"><div class="row"><div class="col-sm-2">' +
                    '<div class="row"><div class="col-sm-2"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                    '<div class="col-sm-10">' +
                    '<select name="staffs[]" class="js-additional-staff form-control">' +
                    {!! $staff_options_js_all !!}
            '</select></div></div></div>' +
                    '<div class="col-sm-1"><input type="number" class="form-control" name="staff_quantity[]"/></div>' +
                    '<div class="col-sm-1"><input type="text" class="form-control" name="staff_unit[]"/></div>' +
                    '<div class="col-sm-6"><textarea class="form-control" name="staff_work_done[]" rows="3"/></div>' +
                    '<div class="col-sm-1"><input type="number" class="form-control" name="staff_planned[]"/></div>' +
                    '<div class="col-sm-1"><input type="number" class="form-control" name="staff_done[]"/></div>' +
                    '</div></div>'); //add input box
            $(".js-additional-staff").select2();

        });

        $(staffToWorkDoneWrapper).on("click", ".remove_field", function (e) { //user click on remove text
            e.preventDefault();
            $(this).parent().closest('div.row').parent().closest('div.form-group').remove();
        });
    </script>

    }
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-{{$left < 10 ? "danger" : "primary"}} box-solid">
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
                        <div class="col-sm-4">
                            <div class="row">
                                {!! Form::open([
                                'url' => "/tekil/$site->slug/select-date",
                                'method' => 'POST',
                                'class' => 'form form-horizontal',
                                'id' => 'dateRangeForm',
                                'role' => 'form'
                                ]) !!}

                                <div class="form-group pull-right">

                                    <label class="col-xs-4 control-label">TARİH: </label>

                                    <div class="col-xs-8 date">
                                        <div class="input-group input-append date" id="dateRangePicker">
                                            <input type="text" class="form-control" name="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-sm-2 col-sm-offset-2">
                            <div class="row">
                                <div class="col-sm-6"><strong>RAPOR NO:</strong></div>
                                <div class="col-sm-6">{{$report_no}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-condensed">

                            <tbody>

                            <tr>
                                <td><strong>İŞ BİTİM TARİHİ:</strong></td>
                                <td>{{$myFormatForView}}</td>
                                <td><strong>TOPLAM SÜRE:</strong></td>
                                <td>{{$total_date}} gün</td>
                                <td><strong>HAVA:</strong></td>
                                <td>{!! $weather_symbol . " " . $my_weather->getDescription()!!}</td>
                                <td><strong>SICAKLIK:</strong></td>
                                <td>{!! $my_weather->getMin() ."<sup>o</sup>C / ". $my_weather->getMax() !!}
                                    <sup>o</sup>C
                                </td>

                            </tr>
                            <tr>
                                <td><strong>KALAN SÜRE:</strong></td>
                                <td>{{$left}} gün</td>
                                <td></td>
                                <td></td>
                                <td><strong>RÜZGAR:</strong></td>
                                <td>{{$my_weather->getWind()}} m/s</td>
                                <td><strong>ÇALIŞMA:</strong></td>
                                <td>
                                    @if(!$locked)
                                        {!! Form::open([
                            'url' => "/tekil/$site->slug/select-is-working",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'selectIsWorkingForm',
                            'role' => 'form'
                            ]) !!}
                                        {!! Form::hidden('report_id', $report->id) !!}
                                        <label class="radio-inline"><input type="radio" name="is_working"
                                                                           value="1" {{$report->is_working == 1 ? "checked" : ""}}>Var</label>
                                        <label class="radio-inline"><input type="radio" name="is_working"
                                                                           value="0" {{$report->is_working == 0 ? "checked" : ""}}>Yok</label>
                                        {!! Form::close() !!}
                                    @else
                                        {{$report->is_working==0 ? "Yok" : "Var"}}
                                    @endif
                                </td>
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


    {{--lock check buradan başlayacak--}}

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
                                        <label for="employer_staff" class="control-label">İşveren
                                            ({{$site->employer}}
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

                            <div class="row {{($locked && empty($report->building_control_staff)) ? "hidden" : ""}}">
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
                            <div class="row {{$locked ? "hidden" : ""}}">
                                <div class="col-sm-12">
                                    <div class="form-group pull-right">
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
                                {{$site->main_contractor}} için personel giriniz.
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
                                        <div class="row" id="div-staffid{{$staff->id}}">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <span>{{$staff->staff}}</span>
                                                    {!! Form::hidden('staffs[]', $staff->id) !!}
                                                </div>
                                            </div>

                                            <div class="col-sm-3">

                                                <input type="number" class="form-control"
                                                       name="contractor-quantity[]"
                                                       value="{{$staff->pivot->quantity}}"/>
                                            </div>
                                            <div class="col-sm-1"><a href="#" onclick="staffDetach({{$staff->id}})"><i
                                                            class="fa fa-close"></i></a></div>
                                        </div>
                                    @endforeach

                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <select name="staffs[]"
                                                        class="js-example-basic-single form-control">

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

                                    $main_contractor_total += $staff->pivot->quantity;

                                }
                                ?>
                                @if($main_contractor_total>0)
                                    <div class="row">
                                        <div class="col-sm-11 text-center">


                                            @foreach($report->staff()->get() as $staff)
                                                <?php
                                                $vowels = ["a", "e", "ı", "i", "o", "ö", "u", "ü"];
                                                $pos = strpos($staff->staff, " ");
                                                $staff_name = "";
                                                $br_string = "";



                                                if ($pos === false) {
                                                    // string needle NOT found in haystack
                                                    $staff_name = $staff->staff;
                                                    for ($k = 2; $k < $max_explosion; $k++) {
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
                                                    for ($k = 1; $k < $max_explosion - sizeof($staff_words); $k++) {
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
                            @if(!$locked)
                                @foreach($subcontractors as $sub)
                                    <?php
                                    $sub_row_total = 0;
                                    ?>
                                    <div class="row" id="div-{{$sub->id}}">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <legend>{{$sub->name}}
                                                        @foreach($sub->manufacturing()->where('site_id', $site->id)->get() as $manufacture)
                                                            <small>({{TurkishChar::tr_up($manufacture->name) }})</small>
                                                        @endforeach
                                                    </legend>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-1 text-center">
                                                    <a class='remove-subcontractor' href='#'
                                                       onclick='removeSubcontractor({{$sub->id}})'><i
                                                                class="fa fa-close"></i></a>
                                                </div>
                                                <div class="col-sm-10">
                                                    <div class="row">
                                                        @foreach($report->substaff()->where('subcontractor_id', $sub->id)->get() as $substaff)
                                                            <div class="col-sm-1 text-center">
                                                                <strong>{{$substaff->name}}</strong>
                                                                <br>
                                                                {{$substaff->pivot->quantity}}</div>
                                                            <?php
                                                            $sub_row_total += (int)$substaff->pivot->quantity;
                                                            ?>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="col-sm-1"><strong>TOPLAM</strong>
                                                    <br>
                                                    <strong>{{$sub_row_total}}</strong>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group pull-right">
                                            <a href="#substaff-modal" data-toggle="modal"
                                               class="btn btn-primary btn-flat"
                                               id="substaff-modal-opener">
                                                Alt Yüklenici ve Personel Ekle
                                            </a>


                                        </div>
                                    </div>

                                </div>
                            @else
                                @foreach($subcontractors as $sub)
                                    <?php
                                    $sub_row_total = 0;
                                    ?>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <legend>{{$sub->name}}
                                                @foreach($sub->manufacturing()->get() as $manufacture)
                                                    <small>({{TurkishChar::tr_up($manufacture->name) }})</small>
                                                @endforeach
                                            </legend>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col-sm-11">
                                            <div class="row">
                                                @foreach($report->substaff()->where('subcontractor_id', $sub->id)->get() as $substaff)
                                                    <div class="col-sm-1 text-center">
                                                        <strong>{{$substaff->name}}</strong>
                                                        <br>
                                                        {{$substaff->pivot->quantity}}</div>
                                                    <?php
                                                    $sub_row_total += (int)$substaff->pivot->quantity;
                                                    ?>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-sm-1 text-left"><strong>TOPLAM</strong>
                                            <br>
                                            {{$sub_row_total}}
                                        </div>
                                    </div>
                                    <?php
                                    $subcontractor_staff_total += $sub_row_total;
                                    ?>

                                @endforeach

                                @if($main_contractor_total + $subcontractor_staff_total + $total_management>0)
                                    @if($subcontractor_staff_total>0)
                                        <div class="row">
                                            <div class="col-sm-11">
                                                <p class="text-right"><strong>ALT YÜKLENİCİ
                                                        TOPLAMI</strong></p>
                                            </div>
                                            <div class="col-sm-1">
                                                <p class="text-left">{{$subcontractor_staff_total}}</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-sm-11">
                                            <p class="text-right" style="font-size: large"><strong>GENEL
                                                    TOPLAM</strong>
                                            </p>
                                        </div>
                                        <div class="col-sm-1">
                                            <p class="text-left"
                                               style="font-size: large">{{$main_contractor_total + $subcontractor_staff_total + $total_management}}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{--End of subcontractors table--}}
            </div>

            {{--End of left tables column--}}
        </div>

        <div class="col-xs-12 col-md-4">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ekipman Tablosu
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
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span class="text-center"><strong>EKİPMAN ADI</strong></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <span class="text-center"><strong>ÇALIŞAN</strong></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <span class="text-center"><strong>MEVCUT</strong></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <span class="text-center"><strong>ARIZALI</strong></span>
                                    </div>

                                </div>
                                {!! Form::open([
                                                                'url' => "/tekil/$site->slug/save-equipment",
                                                                'method' => 'POST',
                                                                'class' => 'form',
                                                                'id' => 'equipmentInsertForm',
                                                                'role' => 'form'
                                                                ]) !!}
                                {!! Form::hidden('report_id', $report->id) !!}
                                <div id="equipment-insert">
                                    @foreach($report->equipment()->get() as $equipment)
                                        <div class="row" id="div-equipmentid{{$equipment->id}}">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-2"><a href="#"
                                                                                 onclick="equipmentDetach({{$equipment->id}})"><i
                                                                        class="fa fa-close"></i></a></div>
                                                        <div class="col-sm-10">
                                                            <span>{{$equipment->name}}</span>
                                                            {!! Form::hidden('equipments[]', $equipment->id) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-2">

                                                <input type="number" class="form-control"
                                                       name="equipment-present[]"
                                                       value="{{$equipment->pivot->present}}"/>
                                            </div>
                                            <div class="col-sm-2">

                                                <input type="number" class="form-control"
                                                       name="equipment-working[]"
                                                       value="{{$equipment->pivot->working}}"/>
                                            </div>
                                            <div class="col-sm-2">

                                                <input type="number" class="form-control"
                                                       name="equipment-broken[]"
                                                       value="{{$equipment->pivot->broken}}"/>
                                            </div>

                                        </div>
                                    @endforeach

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select name="equipments[]"
                                                        class="js-example-basic-single form-control">

                                                    {!! $equipment_options !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">

                                            <input type="number" class="form-control"
                                                   name="equipment-present[]"/>
                                        </div>
                                        <div class="col-sm-2">

                                            <input type="number" class="form-control"
                                                   name="equipment-working[]"/>
                                        </div>
                                        <div class="col-sm-2">

                                            <input type="number" class="form-control"
                                                   name="equipment-broken[]"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group pull-right">
                                            <a href="#" class="btn btn-primary btn-flat add-equipment-row">
                                                Ekipman Ekle
                                            </a>

                                            <button type="submit" class="btn btn-success btn-flat ">
                                                Kaydet
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                {!! Form::close() !!}
                            @else
                                <?php
                                $equipment_total = 0;

                                foreach ($report->equipment()->get() as $eq) {
                                    $equipment_total += $eq->pivot->present + $eq->pivot->working + $eq->pivot->broken;
                                }
                                ?>
                                @if($equipment_total>0)
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
                                            <?php
                                            $equipment_total = 0;
                                            ?>
                                            @foreach ($report->equipment()->get() as $eq)
                                                <tr>
                                                    <td>{{$eq->name}}</td>
                                                    <td>{{$eq->pivot->present}}</td>
                                                    <td>{{$eq->pivot->working}}</td>
                                                    <td>{{$eq->pivot->broken}}</td>
                                                    <td>{{$eq->pivot->present + $eq->pivot->working + $eq->pivot->broken}}</td>
                                                </tr>
                                                <?php
                                                $equipment_total += (int)$eq->pivot->present + (int)$eq->pivot->working + (int)$eq->pivot->broken;
                                                ?>
                                            @endforeach
                                            <tr>
                                                <td><strong>TOPLAM</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>{{$equipment_total}}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--END of right side--}}

    </div>
    {{--end of row--}}


    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Yapılan İşler Tablosu
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
                        <p>Yapılan işler tablosuna 'Alt Yüklenici Ekle' ve 'Personel Ekle' butonlarıyla çalışan birim
                            ekleyebilirsiniz.</p>

                        <div class="row">
                            <div class="col-sm-2 text-center">
                                <span><strong>ÇALIŞAN BİRİM</strong></span>
                            </div>
                            <div class="col-sm-1">
                                <span><strong>KİŞİ SAYISI</strong></span>
                            </div>
                            <div class="col-sm-1">
                                <span><strong>ÖLÇÜ BİRİMİ</strong></span>
                            </div>
                            <div class="col-sm-6">
                                <span><strong>YAPILAN İŞLER</strong></span>
                            </div>
                            <div class="col-sm-1">
                                <span><strong>PLANLANAN</strong></span>
                            </div>
                            <div class="col-sm-1">
                                <span><strong>YAPILAN</strong></span>
                            </div>
                        </div>

                        {!! Form::open([
                                                                    'url' => "/tekil/$site->slug/save-work-done",
                                                                    'method' => 'POST',
                                                                    'class' => 'form',
                                                                    'id' => 'workDoneInsertForm',
                                                                    'role' => 'form'
                                                                    ]) !!}
                        {!! Form::hidden('report_id', $report->id) !!}
                        <div id="staff-to-work-insert">
                            @foreach ($report->pwunit()->get() as $staff)
                                <?php
                                $staff_unit_for_work_done = empty($report->pwunit()->where("staff_id", $staff->staff_id)->first()->unit) ? null : $report->pwunit()->where("staff_id", $staff->staff_id)->first()->unit;
                                $staff_work_done_for_work_done = empty($report->pwunit()->where("staff_id", $staff->staff_id)->first()->works_done) ? null : $report->pwunit()->where("staff_id", $staff->staff_id)->first()->works_done;
                                $staff_planned_for_work_done = empty($report->pwunit()->where("staff_id", $staff->staff_id)->first()->planned) ? null : $report->pwunit()->where("staff_id", $staff->staff_id)->first()->planned;
                                $staff_done_for_work_done = empty($report->pwunit()->where("staff_id", $staff->staff_id)->first()->done) ? null : $report->pwunit()->where("staff_id", $staff->staff_id)->first()->done;
                                ?>
                                <div class="row" id="div-pwid{{$staff->id}}">
                                    <div class="col-sm-2">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <a href="#" onclick="staffToWorkDelete({{$staff->id}})"><i
                                                            class="fa fa-close"></i></a>
                                            </div>
                                            <div class="col-sm-10">
                                                {{$staffs->find($staff->staff_id)->staff}}
                                                {!! Form::hidden("staffs[]", $staff->staff_id)!!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("staff_quantity[]", $staff->quantity, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("staff_unit[]", $staff_unit_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        {!! Form::textarea("staff_work_done[]", $staff_work_done_for_work_done , ['class' => 'form-control', 'rows' => '3']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("staff_planned[]", $staff_planned_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("staff_done[]", $staff_done_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="subcontractor-to-work-insert">
                            @foreach ($report->swunit()->get() as $subcontractor)
                                <?php
                                $subcontractor_unit_for_work_done = empty($report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->unit) ? null : $report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->unit;
                                $subcontractor_work_done_for_work_done = empty($report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->works_done) ? null : $report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->works_done;
                                $subcontractor_planned_for_work_done = empty($report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->planned) ? null : $report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->planned;
                                $subcontractor_done_for_work_done = empty($report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->done) ? null : $report->swunit()->where("subcontractor_id", $subcontractor->subcontractor_id)->first()->done;
                                ?>
                                <div class="row" id="div-swid{{$subcontractor->id}}">
                                    <div class="col-sm-2">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <a href="#" onclick="subcontractorToWorkDelete({{$subcontractor->id}})"><i
                                                            class="fa fa-close"></i></a>
                                            </div>
                                            <div class="col-sm-10">
                                                {{\App\Subcontractor::all()->find($subcontractor->subcontractor_id)->name}}
                                                {!! Form::hidden("subcontractors[]", $subcontractor->subcontractor_id)!!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("subcontractor_quantity[]", $subcontractor->quantity, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("subcontractor_unit[]", $subcontractor_unit_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        {!! Form::textarea("subcontractor_work_done[]", $subcontractor_work_done_for_work_done , ['class' => 'form-control', 'rows' => '3']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("subcontractor_planned[]", $subcontractor_planned_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("subcontractor_done[]", $subcontractor_done_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>


                        <div class="row">
                            <div class="col-sm-10">
                                <div class="form-group pull-left">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <a class="btn btn-warning btn-flat add-subcontractor-to-work-done-row">
                                                Alt Yüklenici Ekle
                                            </a>
                                        </div>

                                        <div class="col-sm-6">
                                            <a class="btn btn-primary btn-flat add-staff-to-work-done-row">
                                                Personel Ekle
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success btn-flat pull-right">
                                    Kaydet
                                </button>
                            </div>

                        </div>

                        {!! Form::close() !!}
                        {{--Locked if--}}
                    @else
                        @if(sizeof($report->pwunit()->get()) + sizeof($report->swunit()->get()) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>ÇALIŞAN BİRİM</th>
                                        <th>KİŞİ SAYISI</th>
                                        <th>ÖLÇÜ BİRİMİ</th>
                                        <th>YAPILAN İŞLER</th>
                                        <th>PLANLANAN</th>
                                        <th>YAPILAN</th>
                                        <th>YÜZDE</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 1;
                                    ?>
                                    @foreach($report->pwunit()->get() as $pw)
                                        <?php
                                        $pw_work_done_in_percent = ((int)$pw->planned == 0 || is_null($pw->planned)) ? 0 : 100 * (int)$pw->done / (int)$pw->planned;
                                        ?>

                                        @if($pw_work_done_in_percent>100)
                                            <tr class="bg-success">
                                        @elseif($pw_work_done_in_percent<100)
                                            <tr class="bg-danger">
                                        @elseif($pw_work_done_in_percent == 100)
                                            <tr class="bg-warning">
                                                @endif
                                                <td>{{$i++}}</td>
                                                <td>{{$staffs->find($pw->staff_id)->staff}}</td>
                                                <td>{{$pw->quantity}}</td>
                                                <td>{{$pw->unit}}</td>
                                                <td>{{$pw->works_done}}</td>
                                                <td>{{$pw->planned}}</td>
                                                <td>{{$pw->done}}</td>
                                                <td>%{{$pw_work_done_in_percent}}</td>
                                            </tr>

                                            @endforeach

                                            @foreach($report->swunit()->get() as $sw)
                                                <?php

                                                $sw_work_done_in_percent = ((int)$sw->planned == 0 || is_null($sw->planned)) ? 0 : 100 * (int)$sw->done / (int)$sw->planned;

                                                ?>

                                                @if($sw_work_done_in_percent>100)
                                                    <tr class="bg-success">
                                                @elseif($sw_work_done_in_percent<100)
                                                    <tr class="bg-danger">
                                                @elseif($sw_work_done_in_percent == 100)
                                                    <tr class="bg-warning">
                                                        @endif
                                                        <td>{{$i++}}</td>
                                                        <td>{{\App\Subcontractor::find($sw->subcontractor_id)->name}}</td>
                                                        <td>{{$sw->quantity}}</td>
                                                        <td>{{$sw->unit}}</td>
                                                        <td>{{$sw->works_done}}</td>
                                                        <td>{{$sw->planned}}</td>
                                                        <td>{{$sw->done}}</td>
                                                        <td>%{{$sw_work_done_in_percent}}</td>
                                                    </tr>

                                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Gelen Malzemeler Tablosu
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
                        <div class="row">
                            <div class="col-sm-2"><strong>GELEN MALZEME</strong></div>
                            <div class="col-sm-2"><strong>GELDİĞİ YER</strong></div>
                            <div class="col-sm-1"><strong>BİRİM</strong></div>
                            <div class="col-sm-1"><strong>MİKTAR</strong></div>
                            <div class="col-sm-6"><strong>AÇIKLAMA</strong></div>
                        </div>

                        {!! Form::open([
                                                                        'url' => "/tekil/$site->slug/save-incoming-material",
                                                                        'method' => 'POST',
                                                                        'class' => 'form',
                                                                        'id' => 'incomingMaterialInsertForm',
                                                                        'role' => 'form'
                                                                        ]) !!}
                        {!! Form::hidden('report_id', $report->id) !!}

                        <div id="inmaterial-insert">
                            @foreach($inmaterials as $inmaterial)
                                <div class="row" id="div-inmaterialid{{$inmaterial->id}}">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2"><a href="#"
                                                                         onclick="inmaterialsDelete({{$inmaterial->id}})"><i
                                                                class="fa fa-close"></i></a></div>
                                                <div class="col-sm-10">
                                                    <span>{{\App\Material::find($inmaterial->material_id)->material}}</span>
                                                    {!! Form::hidden('inmaterials[]', $inmaterial->material_id) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">

                                        <input type="text" class="form-control"
                                               name="inmaterial-from[]"
                                               value="{{$inmaterial->coming_from}}"/>
                                    </div>
                                    <div class="col-sm-1">

                                        <input type="text" class="form-control"
                                               name="inmaterial-unit[]"
                                               value="{{$inmaterial->unit}}"/>
                                    </div>
                                    <div class="col-sm-1">

                                        <input type="number" class="form-control"
                                               name="inmaterial-quantity[]"
                                               value="{{$inmaterial->quantity}}"/>
                                    </div>

                                    <div class="col-sm-6">

                                        <input type="text" class="form-control"
                                               name="inmaterial-explanation[]"
                                               value="{{$inmaterial->explanation}}"/>
                                    </div>

                                </div>
                            @endforeach

                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="inmaterials[]"
                                                class="js-example-basic-single form-control">

                                            {!! $inmaterial_options !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2">

                                    <input type="text" class="form-control"
                                           name="inmaterial-from[]"
                                           value=""/>
                                </div>
                                <div class="col-sm-1">

                                    <input type="text" class="form-control"
                                           name="inmaterial-unit[]"
                                           value=""/>
                                </div>
                                <div class="col-sm-1">

                                    <input type="number" class="form-control"
                                           name="inmaterial-quantity[]"
                                           value=""/>
                                </div>

                                <div class="col-sm-6">

                                    <input type="text" class="form-control"
                                           name="inmaterial-explanation[]"
                                           value=""/>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group pull-right">
                                    <a href="#" class="btn btn-primary btn-flat add-inmaterial-row">
                                        Satır Ekle
                                    </a>

                                    <button type="submit" class="btn btn-success btn-flat ">
                                        Kaydet
                                    </button>
                                </div>
                            </div>

                        </div>
                        {!! Form::close() !!}
                    @else
                        @if(sizeof($inmaterials)>0)
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

                                    @for($i = 1; $i<=sizeof($inmaterials); $i++)

                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{TurkishChar::tr_up(\App\Material::find($inmaterials[$i-1]->material_id)->material)}}</td>
                                            <td>{{TurkishChar::tr_up($inmaterials[$i-1]->unit)}}</td>
                                            <td>{{$inmaterials[$i-1]->quantity}}</td>
                                            <td>{{$inmaterials[$i-1]->explanation}}</td>
                                        </tr>

                                    @endfor

                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Rapor Ekleri
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
                        <div class="col-sm-6">
                            <span><strong>ŞANTİYE FOTOĞRAFLARI</strong></span>
                        </div>
                        <div class="col-sm-6">
                            <span><strong>FATURALAR</strong></span>
                        </div>
                    </div>
                    @if(!$locked)
                        <div class="row">
                            <div class="col-sm-6">
                                {!! Form::open([
                                                                                            'url' => "/tekil/$site->slug/save-files",
                                                                                            'method' => 'POST',
                                                                                            'class' => 'dropzone',
                                                                                            'id' => 'file-insert-form',
                                                                                            'role' => 'form',
                                                                                            'files'=>true
                                                                                            ]) !!}
                                {!! Form::hidden('report_id', $report->id) !!}
                                {!! Form::hidden('type', "0") !!}

                                <div class="fallback">
                                    <input name="file" type="file" multiple/>
                                </div>
                                <div class="dropzone-previews"></div>
                                <h4 style="text-align: center;color:#428bca;">Şantiye fotoğraflarını bu alana
                                    sürükleyin
                                    <br>Ya da tıklayın<span
                                            class="glyphicon glyphicon-hand-down"></span></h4>


                                {!! Form::close() !!}
                                <div class="row">
                                    @foreach($report_site_photo_files as $report_site_photo)
                                        <?php
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_photo->path);
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                        if (strpos($report_site_photo->name, 'pdf') !== false) {
                                            $image = URL::to('/') . "/img/pdf.jpg";
                                        } elseif (strpos($report_site_photo->name, 'doc') !== false) {
                                            $image = URL::to('/') . "/img/word.png";
                                        } else {
                                            $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_photo->name;
                                        }
                                        ?>

                                        <a id="lb-link-{{$report_site_photo->id}}" href="{{$image}}"
                                           data-toggle="lightbox" data-gallery="reportsitephotos"
                                           data-footer="<a data-dismiss='modal' class='remove-files' href='#' onclick='removeFiles({{$report_site_photo->id}}, {{$report->id}})'>Dosyayı Sil<a/>"
                                           class="col-sm-4">
                                            <img src="{{$image}}" class="img-responsive">
                                            {{$report_site_photo->name}}
                                        </a>

                                    @endforeach
                                </div>
                            </div>

                            <div class="col-sm-6">
                                {!! Form::open([
                                'url' => "/tekil/$site->slug/save-files",
                                'method' => 'POST',
                                'class' => 'dropzone',
                                'id' => 'receipt-insert-form',
                                'role' => 'form',
                                'files'=>true]) !!}
                                {!! Form::hidden('report_id', $report->id) !!}
                                {!! Form::hidden('type', "1") !!}

                                <div class="fallback">
                                    <input name="file" type="file" multiple/>
                                </div>
                                <div class="dropzone-previews"></div>
                                <h4 style="text-align: center;color:#428bca;">Şantiye faturalarını bu alana
                                    sürükleyin
                                    <br>Ya da tıklayın<span
                                            class="glyphicon glyphicon-hand-down"></span></h4>


                                {!! Form::close() !!}
                                <div class="row">
                                    @foreach($report_site_receipt_files as $report_site_receipt)
                                        <?php
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_receipt->path);
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                        $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_receipt->name;
                                        if (strpos($report_site_receipt->name, 'pdf') !== false) {
                                            $image = URL::to('/') . "/img/pdf.jpg";
                                        } elseif (strpos($report_site_receipt->name, 'doc') !== false) {
                                            $image = URL::to('/') . "/img/word.png";
                                        }
                                        ?>

                                        <a id="lb-link-{{$report_site_receipt->id}}" href="{{$image}}"
                                           data-toggle="lightbox" data-gallery="reportsitereceipts"
                                           data-footer="<a data-dismiss='modal' class='remove-files' href='#' onclick='removeFiles({{$report_site_receipt->id}}, {{$report->id}})'>Dosyayı Sil<a/>"
                                           class="col-sm-4">
                                            <img src="{{$image}}" class="img-responsive">
                                            {{$report_site_receipt->name}}
                                        </a>

                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else

                        <div class="row">
                            <div class="col-sm-6">
                                @foreach($report_site_photo_files as $report_site_photo)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_photo->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    if (strpos($report_site_photo->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($report_site_photo->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    } else {
                                        $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_photo->name;
                                    }
                                    ?>

                                    <a id="lb-link-{{$report_site_photo->id}}" href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitephotos"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$report_site_photo->name}}
                                    </a>

                                @endforeach
                            </div>
                            <div class="col-sm-6">
                                @foreach($report_site_receipt_files as $report_site_receipt)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_receipt->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_receipt->name;
                                    if (strpos($report_site_receipt->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($report_site_receipt->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    }
                                    ?>

                                    <a id="lb-link-{{$report_site_receipt->id}}" href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitereceipts"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$report_site_receipt->name}}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($locked)
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Şantiye Ekleri
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
                            <div class="col-sm-6">
                                <span><strong>ŞANTİYE FOTOĞRAFLARI</strong></span>
                            </div>
                            <div class="col-sm-6">
                                <span><strong>FATURALAR</strong></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                @foreach($site_photo_files as $site_photo)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_photo->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_photo->name;
                                    if (strpos($site_photo->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($site_photo->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    }
                                    ?>

                                    <a href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitephotos"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$site_photo->name}}
                                    </a>

                                @endforeach
                            </div>
                            <div class="col-sm-6">
                                @foreach($site_receipt_files as $site_receipt)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_receipt->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_receipt->name;
                                    if (strpos($site_receipt->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($site_receipt->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    }
                                    ?>

                                    <a href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitereceipts"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$site_receipt->name}}
                                    </a>

                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (!isset($report_date))
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                {!! Form::open([
                                    'url' => "/tekil/$site->slug/lock-report",
                                    'method' => 'PATCH',
                                    'class' => 'form',
                                    'id' => 'lock-report-form',
                                    'role' => 'form']) !!}
                {!! Form::hidden('report_id', $report->id) !!}
                {!! Form::hidden('lock', !$locked) !!}


                <button type="submit"
                        class="btn btn-flat btn-lg btn-block btn-{{ $locked == 1 ? "primary" : "warning" }}">
                    {{ $locked == 1 ? "Form" : "Rapor" }} Görünümü
                </button>

                {!! Form::close() !!}
            </div>
        </div>
    @endif

    <div class="modal modal-danger" role="dialog" id="leftDaysModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Kalan Gün Uyarısı</h4>
                </div>
                <div class="modal-body">
                    <p>Şantiyenin tamamlanması için {{$left}} gün kalmıştır.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">Kapat
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div id="substaff-modal" class="modal fade" role="dialog" tabindex="-1"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Alt Yüklenici ve Personel Ekle</h4>
                </div>
                {!! Form::open([
                                 'url' => "/tekil/$site->slug/save-subcontractor-staff",
                                 'method' => 'POST',
                                 'class' => 'form',
                                 'id' => 'subcontractorStaffInsertForm',
                                 'role' => 'form form-horizontal'
                                 ]) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>Alt yükleniciyi seçtikten sonra personel ekleyebilirsiniz.</p>
                        </div>
                    </div>

                    {!! Form::hidden('report_id', $report->id) !!}


                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                {!! Form::label('subcontractor', 'Alt Yüklenici: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-9">
                                <select name="subcontractor" class="js-example-responsive form-control"
                                        style="width: 100%">
                                    {!! $subcontractor_options !!}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="subcontractor_staff-insert">

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <a href="#" class="btn btn-primary btn-block btn-flat add-subcontractor_staff-row"
                               style="display: none">
                                Personel Ekle
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-warning btn-flat pull-left"
                                    data-dismiss="modal">Kapat
                            </button>
                        </div>
                        <div class="col-sm-9">
                            <div class="form-group pull-right">

                                <button type="submit" class="btn btn-success btn-flat ">
                                    Kaydet
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    @if($locked)
        @include('tekil._locked')
    @endif

@stop