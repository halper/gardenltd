<?php
use App\Library\TurkishChar;use App\Library\Weather;
use App\Material;use App\Personnel;
use App\Site;
use Carbon\Carbon;
use App\Staff;
use Illuminate\Support\Facades\Session;
$my_weather = new Weather;
$weather_symbol = '';



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

$outmaterials = $report->outmaterial()->get();
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
$day_warning = (int)$total_date * 0.2;

$report_subcontractors = [];
$subcontractor_report_personnel = [];
$i = 0;
foreach ($report->subcontractor()->get() as $report_staff) {
    if (!in_array($report_staff, $report_subcontractors)) {
        array_push($report_subcontractors, $report_staff);
    }

}


$all_subcontractors = $site->subcontractor()->get();
$report_subcontractor_arr = [];
foreach ($report_subcontractors as $report_subcontractor) {
    $subcontractor_report_personnel[$i] = [];
    array_push($report_subcontractor_arr, $report_subcontractor->id);
    foreach ($report->shift()->get() as $shift) {
        if (!is_null($shift->personnel()->first()->personalize)) {
            if ($shift->personnel()->first()->personalize->id == $report_subcontractor->id) {
                array_push($subcontractor_report_personnel[$i], $shift->personnel_id);
            }
        }
    }
    $i++;
}

$subcontractor_staffs = \App\Staff::all();
$subcontractor_staff_total = 0;

$personnel_arr = [];
$report_personnel_id_arr = [];
$site_report_personnel = [];
foreach ($report->shift()->join('personnel', 'personnel_id', '=', 'personnel.id')->orderBy('personnel.personalize_type', 'ASC')->orderBy('personnel.personalize_id', 'ASC')->get() as $shift) {
    array_push($report_personnel_id_arr, $shift->personnel_id);
    if ($shift->personnel()->first()->isSitePersonnel()) {
        array_push($site_report_personnel, $shift->personnel_id);
    }
}

$personnel_options = "<option></option>";
$personnel_options_js = "";
$all_personnel = Personnel::all();
foreach ($all_personnel as $per) {
    if (!in_array($per, $personnel_arr)) {
        array_push($personnel_arr, $per);
    }

    $personnel_options_js .= "'<option value=\"$per->id\">" . TurkishChar::tr_up($per->name) . " (" . TurkishChar::tr_up($per->staff->staff) . ")</option>'+\n";
    if (isset($report_personnel_id_arr)) {
        if (!in_array($per->id, $report_personnel_id_arr)) {
            $personnel_options .= "<option value=\"$per->id\">" . TurkishChar::tr_up($per->name) . " (" . TurkishChar::tr_up($per->staff->staff) . ")</option>";
        }
    } else {
        $personnel_options .= "<option value=\"$per->id\">" . TurkishChar::tr_up($per->name) . " (" . TurkishChar::tr_up($per->staff->staff) . ")</option>";
    }
}
$personnel_left = !(sizeof($all_personnel) == sizeof($report_personnel_id_arr));

$staff_options = '';
$staff_options_js = '';
$staff_options_js_all = '';
$staff_options_all = '';
$management_depts = new \App\Department();

foreach ($management_depts->management() as $dept) {
    $staff_options .= "<optgroup label=\"$dept->department\">";
    $staff_options_js .= "'<optgroup label=\"$dept->department\">'+\n";
    foreach ($dept->staff()->get() as $staff) {
        $staff_options_js_all .= "'<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>'+\n";
        $staff_options_all .= "<option value=\"$staff->id\">" . TurkishChar::tr_up($staff->staff) . "</option>";
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

$main_personnel = Personnel::sitePersonnel()->get();
$main_per_options = '';
foreach ($main_personnel as $per) {
    $main_per_options .= "<option value=\"$per->id\">" . TurkishChar::tr_up($per->staff()->first()->staff) . ": " . TurkishChar::tr_camel($per->name) . "(" . $per->tck_no . ")</option>";
}

$subcontractor_personnel_options = '';
foreach ($all_subcontractors as $subcontractor) {
    $subcontractor_personnel_options .= "<optgroup label=\"" . $subcontractor->subdetail->name;
    foreach ($subcontractor->manufacturing()->get() as $manufacture) {
        $subcontractor_personnel_options .= " (" . TurkishChar::tr_up($manufacture->name) . ")";
    }
    $subcontractor_personnel_options .= "\">";
    foreach ($subcontractor->personnel()->get() as $per) {
        $subcontractor_personnel_options .= "<option value=\"$per->id\">" . TurkishChar::tr_up($per->staff()->first()->staff) . ": " . TurkishChar::tr_camel($per->name) . "(" . $per->tck_no . ")</option>";
    }
}


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

$inmaterial_options = "";
$inmaterial_options_js = "";


foreach (Material::all() as $inmaterial) {
    $inmaterial_options .= "<option value=\"$inmaterial->id\">" . TurkishChar::tr_up($inmaterial->material) . "</option>";
    $inmaterial_options_js .= "'<option value=\"$inmaterial->id\">" . TurkishChar::tr_up($inmaterial->material) . "</option>'+\n";
}

$outmaterial_options = "";
$outmaterial_options_js = "";


foreach (Material::all() as $outmaterial) {
    $outmaterial_options .= "<option value=\"$outmaterial->id\">" . TurkishChar::tr_up($outmaterial->material) . "</option>";
    $outmaterial_options_js .= "'<option value=\"$outmaterial->id\">" . TurkishChar::tr_up($outmaterial->material) . "</option>'+\n";
}

$subcontractor_staff_options = $staff_options_all;
$subcontractor_staff_options_js = $staff_options_js_all;




$site_reports = $site->report()->get();
$report_no = 1;
foreach ($site_reports as $site_report) {
    $report_no++;
    if ($report->id == $site_report->id) {
        break;
    }
}


if (!is_null($report->weather)) {
    if (strpos($report->weather, 'Kapalı') !== false) {
        $weather_symbol = '<i class="wi wi-day-cloudy"></i>';
    } else if (strpos($report->weather, 'Az bulutlu') !== false) {
        $weather_symbol = '<i class="wi wi-day-cloudy"></i>';
    } else if (strpos($report->weather, 'Hafif kar yağışlı') !== false) {
        $weather_symbol = '<i class="wi wi-day-snow"></i>';
    } else if (strpos($report->weather, 'Hafif yağmur') !== false) {
        $weather_symbol = '<i class="wi wi-day-sprinkle"></i>';
    } else if (strpos($report->weather, 'Şiddetli yağmur') !== false) {
        $weather_symbol = '<i class="wi wi-day-thunderstorm"></i>';
    } else if (strpos($report->weather, 'Orta şiddetli yağmur') !== false) {
        $weather_symbol = '<i class="wi wi-day-hail"></i>';
    } else if (strpos($report->weather, 'Açık') !== false) {
        $weather_symbol = '<i class="wi wi-day-sunny"></i>';
    }
} else {
    if (strpos($my_weather->getDescription(), 'Kapalı') !== false) {
        $weather_symbol = '<i class="wi wi-day-cloudy"></i>';
    } else if (strpos($my_weather->getDescription(), 'Az bulutlu') !== false) {
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
    <link href="<?= URL::to('/'); ?>/css/weather-icons-wind.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/dropzone.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>

    <?php

    if ($locked) {
        echo <<<EOT
    <script>
    $('body').addClass('sidebar-collapse');
        $('aside.right-side').addClass('strech');
        $('aside.left-side').addClass('collapse-left');
    </script>

EOT;

    }

    echo <<<EOT
<script>

    $(document).ready(function() {
            var equipment_wrapper         = $("#equipment-insert"); //Fields wrapper
            var add_equipment_button      = $(".add-equipment-row"); //Add button ID

            $(add_equipment_button).click(function(e){ //on add input button click
                e.preventDefault();

                    $(equipment_wrapper).append('<div class="row"><div class="col-sm-5"><div class="form-group">' +
                    '<div class="row"><div class="col-sm-1"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                    '<div class="col-sm-10"><select name="equipments[]" class="js-additional-equipment form-control">' +
$equipment_options_js
            '</select></div></div></div></div>' +
                '<div class="col-sm-7"><div class="row">'+
                '<div class="col-sm-4"><input type="number" step="1" class="form-control" name="equipment-present[]"/></div>'+
                '<div class="col-sm-4"><input type="number" step="1" class="form-control" name="equipment-working[]"/></div>'+
                '<div class="col-sm-4"><input type="number" step="1" class="form-control" name="equipment-broken[]"/></div></div></div></div>'); //add input box
                $(".js-additional-equipment").select2();

            });

            $(equipment_wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.row').remove();
            })
        });
</script>
EOT;



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
                '<div class="col-sm-1"><input type="text" class="form-control number" name="inmaterial-quantity[]"/></div>'+
                '<div class="col-sm-6"><input type="text" class="form-control" name="inmaterial-explanation[]"/></div></div>'); //add input box
                $(".js-additional-inmaterial").select2();
$('.number').number(true, 2, ',', '.');
            });

            $(inmaterial_wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.row').remove();
            })
        });
</script>
EOT;


    echo <<<EOT
            <script>
$(document).ready(function() {
            var outmaterial_wrapper         = $("#outmaterial-insert"); //Fields wrapper
            var add_outmaterial_button      = $(".add-outmaterial-row"); //Add button ID

            $(add_outmaterial_button).click(function(e){ //on add input button click
                e.preventDefault();

                    $(outmaterial_wrapper).append('<div class="row"><div class="col-sm-2"><div class="form-group">' +
                    '<div class="row"><div class="col-sm-2"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                    '<div class="col-sm-10"><select name="outmaterials[]" class="js-additional-outmaterial form-control">' +
$outmaterial_options_js
            '</select></div></div></div></div>' +
                '<div class="col-sm-2"><input type="text" class="form-control" name="outmaterial-from[]"/></div>'+
                '<div class="col-sm-1"><input type="text" class="form-control" name="outmaterial-unit[]"/></div>'+
                '<div class="col-sm-1"><input type="text" class="form-control number" name="outmaterial-quantity[]"/></div>'+
                '<div class="col-sm-6"><input type="text" class="form-control" name="outmaterial-explanation[]"/></div></div>'); //add input box
                $(".js-additional-outmaterial").select2();
$('.number').number(true, 2, ',', '.');
            });

            $(outmaterial_wrapper).on("click",".remove_field", function(e){ //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.row').remove();
            })
        });
</script>
EOT;


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
                '<div class="col-sm-4"><input type="number" step="1" placeholder="Personel sayısı giriniz" class="form-control" name="substaff-quantity[]"/></div>'+
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
            var data = [{id: 0, text: 'İşveren ({!! $site->employer!!})'}, {
                id: 1,
                text: 'İdare({!! $site->management_name!!})'
            },
                {id: 2, text: 'Yapı Denetim({!! $site->building_control !!})'}, {id: 3, text: 'İSG ({!! $site->isg!!})'}
            ];

            $(".js-example-data-array").select2({
                data: data
            });
            $("#dateRangePicker > input").val("{{isset($report_date) ? $report_date : App\Library\CarbonHelper::getTurkishDate($today)}}");

            $(".js-overtime-select").select2({
                placeholder: "Puantaj seçiniz",
                allowClear: true
            });


            $(".js-overtime-select").on("select2:select", function(e){
               console.log("select2:select", e);
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
                        '<div class="col-sm-1"><input type="number" step="1" class="form-control" name="staff_quantity[]"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="form-control" name="staff_unit[]"/></div>' +
                        '<div class="col-sm-6"><textarea class="form-control" name="staff_work_done[]" rows="3"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="number form-control" name="staff_planned[]"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="number form-control" name="staff_done[]"/></div>' +
                        '</div></div>'); //add input box
                $(".js-additional-staff").select2();
                $('.number').number(true, 2, ',', '.');

            });

            $(staffToWorkDoneWrapper).on("click", ".remove_field", function (e) { //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').parent().closest('div.form-group').remove();
            });

            var mainStaffWrapper = $("#main-staff-insert"); //Fields wrapper
            var addMainStaff = $(".add-main-staff-row"); //Add button ID

            $(addMainStaff).click(function (e) { //on add input button click
                $(mainStaffWrapper).append('<div class="row">' +
                        '<div class="col-sm-1"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                        '<div class="col-sm-7">' +
                        '<div class="form-group"><select name="main-staffs[]" class="js-example-data-array form-control"></select>' +
                        '</div></div>' +
                        '<div class="col-sm-offset-2 col-sm-2"><input type="number" step="1" class="form-control" name="main-staff-quantity[]"/>' +
                        '</div></div>'); //add input box
                var data = [{id: 0, text: 'İşveren ({!! $site->employer!!})'}, {
                    id: 1,
                    text: 'İdare({!! $site->management_name!!})'
                },
                    {id: 2, text: 'Yapı Denetim({!! $site->building_control !!})'}, {
                        id: 3,
                        text: 'İSG ({!! $site->isg!!})'
                    }
                ];
                $(".js-example-data-array").select2({
                    data: data
                });

            });

            $(mainStaffWrapper).on("click", ".remove_field", function (e) { //user click on remove text
                e.preventDefault();
                $(this).parent().closest('div.row').remove();
            });

            //MEALS
            var setCbHidden = function () {
                var myVal = parseInt($(this).val());
                var hiddenEl = $(this).parent().closest("label").parent().closest("div").parent().find('.meals_arr');
                var hiddenVal = parseInt($(hiddenEl).val());
                if ($(this).is(':checked')) {
                    hiddenEl.val(myVal + hiddenVal);
                }
                else {
                    hiddenEl.val(hiddenVal - myVal);
                }
            };
            $("input[name='meals[]']").on("click", setCbHidden);
            $("input.personnel-row-cb").on("click", setCbHidden);
            //END MEALS


            //PUANTAJ
            var setRdHidden = function () {
                var myVal = parseInt($(this).val());
                var hiddenEl = $(this).parent().closest("label").parent().closest("div").parent().find('.overtime-hidden');
                var overtimeIn = $(this).parent().parent().parent().find('.overtime_input');
                if ($(this).is(':checked') && myVal != 0) {
                    hiddenEl.val(myVal);
                    overtimeIn.val('');
                    overtimeIn.prop('disabled', true);
                }

                if (myVal == 0) {
                    overtimeIn.prop('disabled', false);
                }
            };
            $("input.overtime-radio").on("click", setRdHidden);
            $('.overtime_input').keyup(function () {
                $(this).parent().parent().parent().parent().find('.overtime-hidden').val($(this).val());
            });
            //END PUANTAJ

            $('#shiftsMealsForm').on("submit", function (e) {
                var overtimes = $("input[name='overtime_arr[]']");
                var personnelHelper = $('#personnel-helper-block');
                var personnel = $("select[name='personnel[]']");
                var personnelList = new Array();
                $(personnelHelper).html();
                $.each(overtimes, function (index, object) {
                    if ($(object).val().length == 0) {
                        e.preventDefault();
                        $(personnelHelper).html('<span class="text-danger">Puantajlar tüm personel için seçili olmalı veya fazla mesailer doldurulmalı!</span>');
                        return;
                    }

                });
                $.each(personnel, function (index, object) {
                    var personnelId = parseInt($(object).val());
                    if ($(object).val().length == 0) {
                        e.preventDefault();
                        $(personnelHelper).html('<span class="text-danger">Seçili olmayan personel bulunmaktadır!</span>');
                        return;
                    }
                    if ($.inArray($(personnelId), personnelList) !== -1 !== -1) {
                        personnelList.push($(personnelId));
                    }
                    else {
                        e.preventDefault();
                        $(personnelHelper).html('<span class="text-danger">Aynı personeli iki kere ekleyemezsiniz!</span>');
                        return;
                    }


                });

            });

            $('.remove-files').on("click", function (e) {
                e.preventDefault();
                var fileId = $(this).attr("data-fileId");
                var reportId = '{{$report->id}}';
                reportId = parseInt(reportId);
                $.ajax({
                    type: 'POST',
                    url: '{{"/tekil/$site->slug/delete-files"}}',
                    data: {
                        "fileid": fileId,
                        "reportid": reportId
                    }
                }).success(function () {
                    var linkID = "lb-link-" + fid;
                    $('#' + linkID).remove();
                });

            });

            function removeSubcontractor(subid, subname) {

                var subcontractorId = subid;
                var reportId = '{{$report->id}}';
                reportId = parseInt(reportId);
                $.ajax({
                    type: 'POST',
                    url: '{{"/tekil/$site->slug/delete-report-subcontractor"}}',
                    data: {
                        "subcontractorid": subcontractorId,
                        "reportid": reportId
                    }
                }).success(function () {
                    $('#div-' + subid).remove();
                    $('.js-example-responsive').append(
                            '<option value="' + subid + '">' + subname.toUpperCase() + '</option>\n'
                    );
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

            $(".staff-detach").on("click", function (e) {
                e.preventDefault();
                var id = $(this).attr("data-personnel");
                var reportId = '{{$report->id}}';
                reportId = parseInt(reportId);
                $.ajax({
                    type: 'POST',
                    url: '{{"/tekil/$site->slug/detach-staff"}}',
                    data: {
                        "staffid": id,
                        "report_id": reportId
                    }
                }).success(function () {
                    $('#div-staffid' + id).remove();
                    $('#personnel-div-' + id).remove();
                });
                return false;
            });

            $(".substaff-detach").on("click", function (e) {
                e.preventDefault();
                var id = $(this).attr("data-personnel");
                var subcontractorId = $(this).attr("data-subcontractor");
                var reportId = '{{$report->id}}';
                reportId = parseInt(reportId);
                $.ajax({
                    type: 'POST',
                    url: '{{"/tekil/$site->slug/delete-report-subcontractor"}}',
                    data: {
                        "staffid": id,
                        "subcontractorid": subcontractorId,
                        "report_id": reportId
                    }
                }).success(function () {
                    $('#div-staffid' + id).remove();
                    $('#personnel-div-' + id).remove();
                });
                return false;
            });

            function equipmentDetach(id) {
                var reportId = '{{$report->id}}';
                reportId = parseInt(reportId);
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

            function outmaterialsDelete(id) {
                $.ajax({
                    type: 'POST',
                    url: '{{"/tekil/$site->slug/delete-outmaterial"}}',
                    data: {
                        "outmaterialid": id
                    }
                }).success(function () {
                    $('#div-outmaterialid' + id).remove();
                });
            }

            function mainStaffDelete(column) {
                var reportId = '{{$report->id}}';
                reportId = parseInt(reportId);
                $.ajax({
                    type: 'POST',
                    url: '{{"/tekil/$site->slug/delete-management-staff"}}',
                    data: {
                        "reportid": reportId,
                        "column": column
                    }
                }).success(function () {
                    $('#div-' + column).remove();
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

            $(".js-main-per").select2({
                placeholder: "Çoklu seçim yapabilirsiniz",
                allowClear: true
            });

            $(document).ready(function () {
                var managementTotal = 0;
                var mainContractorTotal = 0;
                var subcontractorStaffTotal = 0;
                if ($('#man-tot').length > 0) {
                    $('#man-tot-res').text($('#man-tot').text());
                    managementTotal = parseInt($('#man-tot').text());
                }
                else {
                    $('#man-tot-res').text(0);
                }
                if ($('#main-con-tot').length > 0) {
                    $('#main-con-tot-res').text($('#main-con-tot').text());
                    mainContractorTotal = parseInt($('#main-con-tot').text());
                }
                else {
                    $('#main-con-tot-res').text(0);
                }

                if ($('.sub-staff-tot').length > 0) {
                    $('.sub-staff-tot').each(function (index, element) {
                        subcontractorStaffTotal += parseInt($(this).text());
                    });
                    $('#sub-staff-tot-res').text(subcontractorStaffTotal);
                }
                else {
                    $('#sub-staff-tot-res').text(0);
                }
                $('#gen-tot-res').text(managementTotal + mainContractorTotal + subcontractorStaffTotal);


                $("input[name='is_working']").on("click", function () {
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
                    language: 'tr',
                });

                var leftDays = '{{$left}}';
                var leftWarning = '{{$day_warning}}';
                leftDays = parseInt(leftDays);
                leftWarning = parseInt(leftWarning);
                if (leftDays < leftWarning) {
                    $('#leftDaysModal').modal("show");
                }

                $("#dateRangePicker").datepicker().on('changeDate', function (ev) {

                    if (ev.date.valueOf() > new Date()) {
                        $(this).closest("div").append('<span class="text-danger">Bugünden ileri bir tarih seçemezsiniz!</span>');
                        $(this).parent().closest("div").addClass('has-error');
                        return false;

                    }
                    $('#dateRangeForm').submit();
                });

                $(".remove_row").on("click", function (e) { //user click on remove text
                    e.preventDefault();
                    $(this).parent().closest('td').parent().closest('tr').remove();
                });


                $(".js-additional-personnel").select2({
                    placeholder: 'Personel seçiniz',
                    allowclear: true
                });
            });
        });

    </script>

@stop

@section('content')
    <?php
    $total_management = 0;
    if (!empty($report->management_staff))
        $total_management += $report->management_staff;
    if (!empty($report->building_control_staff))
        $total_management += $report->building_control_staff;
    if (!empty($report->employer_staff))
        $total_management += $report->employer_staff;
    ?>
    @if(!$locked)
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-{{$left < $day_warning ? "danger" : "primary"}} box-solid">
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
                                    <td class="text-center"><strong>KALAN SÜRE:</strong></td>
                                    <td></td>
                                    <td><strong>HAVA:</strong></td>
                                    <td>{!! $weather_symbol !!}{!! !is_null($report->weather) ? $report->weather : $my_weather->getDescription() !!}</td>
                                    <td><strong>SICAKLIK:</strong></td>
                                    <td>{!! !is_null($report->temp_min) ? $report->temp_min ."<sup>o</sup>C / ". $report->temp_max : $my_weather->getMin() ."<sup>o</sup>C / ". $my_weather->getMax() !!}

                                        <sup>o</sup>C
                                    </td>

                                </tr>
                                <tr>
                                    <td><strong>TOPLAM SÜRE:</strong></td>
                                    <td>{{$total_date}} gün</td>
                                    <td class="text-center" {{$left<$day_warning ? "style=background-color:red;color:white" : ""}}>{{$left}}
                                        gün
                                    </td>
                                    <td></td>
                                    <td><strong>RÜZGAR:</strong></td>
                                    <td>{{ !is_null($report->weather) ? $report->wind :$my_weather->getWind()}} m/s</td>
                                    <td><strong>ÇALIŞMA:</strong></td>
                                    <td>

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
            {{--Yönetim Denetim Personel tablosu--}}
            <div class="col-xs-12 col-md-7">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title">Yönetim/Denetim Personel Tablosu</h3>

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
                                        <div class="col-sm-8 text-center">
                                            <span><strong>PERSONEL </strong></span>
                                        </div>
                                        <div class="col-sm-2 col-sm-offset-2 text-center">
                                            <span><strong>SAYISI</strong></span>
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
                                {!! Form::hidden('report_id', $report->id) !!}

                                @if($report->management_staff > 0)
                                    <div class="row" id="div-management_staff">
                                        <div class="form-group">
                                            <div class="col-sm-1"><a href="#"
                                                                     onclick="mainStaffDelete('management_staff')"><i
                                                            class="fa fa-close"></i></a></div>
                                            <div class="col-sm-7">
                                                <label for="main-staff-quantity[]" class="control-label">Proje Yönetimi
                                                    ({{$site->management_name}})</label>
                                                {!! Form::hidden('main-staffs[]', '0') !!}
                                            </div>

                                            <div class="col-sm-2 col-sm-offset-2">

                                                {!! Form::number('main-staff-quantity[]', $report->management_staff, ['class' => 'form-control', 'step' => '1'])  !!}

                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($report->employer_staff>0)
                                    <div class="row" id="div-employer_staff">
                                        <div class="form-group">
                                            <div class="col-sm-1"><a href="#"
                                                                     onclick="mainStaffDelete('employer_staff')"><i
                                                            class="fa fa-close"></i></a></div>
                                            <div class="col-sm-7">
                                                <label for="main-staff-quantity" class="control-label">İşveren
                                                    ({{$site->employer}}
                                                    )</label>
                                                {!! Form::hidden('main-staffs[]', '1') !!}
                                            </div>

                                            <div class="col-sm-2 col-sm-offset-2">
                                                {!! Form::number('main-staff-quantity[]', $report->employer_staff, ['class' => 'form-control', 'step' => '1'])  !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($report->building_control_staff>0)
                                    <div class="row" id="div-building_control_staff">
                                        <div class="form-group">
                                            <div class="col-sm-1"><a href="#"
                                                                     onclick="mainStaffDelete('building_control_staff')"><i
                                                            class="fa fa-close"></i></a></div>
                                            <div class="col-sm-7">
                                                <label for="main-staff-quantity[]" class="control-label">Yapı Denetim
                                                    ({{$site->building_control}}
                                                    )</label>
                                                {!! Form::hidden('main-staffs[]', '2') !!}
                                            </div>

                                            <div class="col-sm-2 col-sm-offset-2">
                                                {!! Form::number('main-staff-quantity[]', $report->building_control_staff, ['class' => 'form-control', 'step' => '1'])  !!}

                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($report->isg_staff>0)
                                    <div class="row" id="div-isg_staff">
                                        <div class="form-group">
                                            <div class="col-sm-1"><a href="#"
                                                                     onclick="mainStaffDelete('isg_staff')"><i
                                                            class="fa fa-close"></i></a></div>
                                            <div class="col-sm-7">
                                                <label for="main-staff-quantity[]" class="control-label">İSG
                                                    ({{$site->isg}}
                                                    )</label>
                                                {!! Form::hidden('main-staffs[]', '3') !!}
                                            </div>

                                            <div class="col-sm-2 col-sm-offset-2">
                                                {!! Form::number('main-staff-quantity[]', $report->isg_staff, ['class' => 'form-control', 'step' => '1'])  !!}

                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <select name="main-staffs[]"
                                                    class="js-example-data-array form-control">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2 col-sm-offset-2">

                                        <input type="number" step="1" class="form-control"
                                               name="main-staff-quantity[]"/>
                                    </div>
                                </div>

                                <div id="main-staff-insert">

                                </div>
                                @if($total_management>0)
                                    <div class="row">
                                        <div class="col-sm-10" style="text-align:right">
                                            <span><strong>TOPLAM: </strong></span>
                                        </div>
                                        <div class="col-sm-2 text-center">
                                            {{$total_management}}
                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group pull-right">
                                            <a href="#" class="btn btn-primary btn-flat add-main-staff-row">
                                                Personel Ekle
                                            </a>
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

                <?php
                $main_contractor_total = 0;
                $number_of_col = 12;
                foreach ($report->staff()->get() as $staff) {
                    $main_contractor_total += $staff->pivot->quantity;
                }
                ?>

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
                                {{$site->main_contractor}} için personel giriniz.
                                <br>

                                <div class="row">
                                    <div class="text-center">
                                        <div class="col-sm-12">
                                            <span><strong>PERSONEL</strong></span>
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

                                    @if(sizeof($site_report_personnel)>0)
                                        <div class="row">
                                            <div class="col-sm-1"></div>
                                            <div class="col-sm-3"><span style="font-style: italic">TCK No</span></div>
                                            <div class="col-sm-5"><span style="font-style: italic">Ad Soyad</span></div>
                                            <div class="col-sm-3"><span style="font-style: italic">İş Kolu</span></div>
                                        </div>
                                    @endif
                                    @for($i = 0; $i < sizeof($site_report_personnel); $i++)
                                        <?php
                                        $per = $site_report_personnel[$i];
                                        $report_person = Personnel::find($per);
                                        ?>
                                        <div class="row" id="div-staffid{{$report_person->id}}">
                                            <div class="col-sm-1"><a href="#"><i
                                                            class="fa fa-close staff-detach"
                                                            data-personnel="{{$report_person->id}}"></i></a></div>
                                            <div class="col-sm-3">
                                                <span>
                                                    {{$report_person->tck_no}}
                                                </span>
                                            </div>
                                            <div class="col-sm-5">
                                                <span>
                                                    {{\App\Library\TurkishChar::tr_camel($report_person->name)}}
                                                </span>
                                            </div>
                                            <div class="col-sm-3">
                                                <span>
                                                    {{\App\Library\TurkishChar::tr_up($report_person->staff->staff)}}
                                                </span>
                                                {!! Form::hidden('staffs[]', $report_person->id) !!}
                                            </div>

                                        </div>
                                    @endfor

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <select name="staffs[]"
                                                        class="js-example-basic-multiple form-control" multiple>
                                                    {!! $main_per_options !!}
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

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
                                <div class="row">
                                    <div class="text-center">
                                        <div class="col-sm-12">
                                            <span><strong>PERSONEL</strong></span>
                                        </div>
                                    </div>
                                </div>
                                @if(sizeof($report_subcontractors)>0)
                                    <div class="row">
                                        <div class="col-sm-1"></div>
                                        <div class="col-sm-3"><span style="font-style: italic">TCK No</span></div>
                                        <div class="col-sm-3"><span style="font-style: italic">Ad Soyad</span></div>
                                        <div class="col-sm-2"><span style="font-style: italic">İş Kolu</span></div>
                                        <div class="col-sm-3"><span style="font-style: italic">Alt Yüklenici</span>
                                        </div>
                                    </div>
                                @endif
                                @for($i = 0; $i < sizeof($report_subcontractor_arr); $i++)
                                    @foreach($subcontractor_report_personnel[$i] as $id)
                                        <?php
                                        $report_person = Personnel::find($id);
                                        $sub = \App\Subcontractor::find($report_subcontractor_arr[$i]);
                                        ?>
                                        <div class="row" id="div-staffid{{$report_person->id}}">
                                            <div class="col-sm-1"><a href="#"><i
                                                            class="fa fa-close substaff-detach"
                                                            data-personnel="{{$report_person->id}}"
                                                            data-subcontractor="{{$report_subcontractors[$i]->id}}"></i></a>
                                            </div>
                                            <div class="col-sm-3">
                                                <span>
                                                    {{$report_person->tck_no}}
                                                </span>
                                            </div>
                                            <div class="col-sm-3">
                                                <span>
                                                    {{\App\Library\TurkishChar::tr_camel($report_person->name)}}
                                                </span>
                                            </div>
                                            <div class="col-sm-2">
                                                <span>
                                                    {{\App\Library\TurkishChar::tr_up($report_person->staff->staff)}}
                                                </span>
                                                {!! Form::hidden('substaffs[]', $report_person->id) !!}
                                            </div>
                                            <div class="col-sm-3">
                                                <span>
                                                    {{\App\Library\TurkishChar::tr_up($sub->subdetail->name)}}
                                                </span>
                                                {!! Form::hidden('staffs[]', $report_person->id) !!}
                                            </div>

                                        </div>
                                    @endforeach
                                @endfor

                                {!! Form::open([
                                 'url' => "/tekil/$site->slug/save-subcontractor-staff",
                                 'method' => 'POST',
                                 'class' => 'form',
                                 'id' => 'subcontractorStaffInsertForm',
                                 'role' => 'form form-horizontal'
                                 ]) !!}
                                {!! Form::hidden('report_id', $report->id) !!}
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <select name="substaffs[]"
                                                    class="js-example-basic-multiple form-control" multiple>
                                                {!! $subcontractor_personnel_options !!}
                                            </select>
                                        </div>
                                    </div>

                                </div>

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
                            </div>
                        </div>
                    </div>


                    {{--End of subcontractors table--}}
                </div>

                {{--End of left tables column--}}
            </div>


            <div class="col-xs-12 col-md-5">
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
                                <div class="row">
                                    <div class="col-sm-5">
                                        <span class="text-center"><strong>EKİPMAN ADI</strong></span>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <span class="text-center"><strong>ÇALIŞAN</strong></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="text-center"><strong>MEVCUT</strong></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="text-center"><strong>ARIZALI</strong></span>
                                            </div>
                                        </div>
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
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-sm-1"><a href="#"
                                                                                 onclick="equipmentDetach({{$equipment->id}})"><i
                                                                        class="fa fa-close"></i></a></div>
                                                        <div class="col-sm-10">
                                                            <span>{{$equipment->name}}</span>
                                                            {!! Form::hidden('equipments[]', $equipment->id) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-7">
                                                <div class="row">
                                                    <div class="col-sm-4">

                                                        <input type="number" step="1" class="form-control"
                                                               name="equipment-present[]"
                                                               value="{{$equipment->pivot->present}}"/>
                                                    </div>
                                                    <div class="col-sm-4">

                                                        <input type="number" step="1" class="form-control"
                                                               name="equipment-working[]"
                                                               value="{{$equipment->pivot->working}}"/>
                                                    </div>
                                                    <div class="col-sm-4">

                                                        <input type="number" step="1" class="form-control"
                                                               name="equipment-broken[]"
                                                               value="{{$equipment->pivot->broken}}"/>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach

                                    {{--<div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <select name="equipments[]"
                                                        class="js-example-basic-single form-control">

                                                    {!! $equipment_options !!}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="row">
                                                <div class="col-sm-4">

                                                    <input type="number" step="1" class="form-control"
                                                           name="equipment-present[]"/>
                                                </div>
                                                <div class="col-sm-4">

                                                    <input type="number" step="1" class="form-control"
                                                           name="equipment-working[]"/>
                                                </div>
                                                <div class="col-sm-4">

                                                    <input type="number" step="1" class="form-control"
                                                           name="equipment-broken[]"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>--}}
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
                                        {!! Form::number("staff_quantity[]", $staff->quantity, ['class' => 'form-control', 'step' => '1']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("staff_unit[]", $staff_unit_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        {!! Form::textarea("staff_work_done[]", $staff_work_done_for_work_done , ['class' => 'form-control', 'rows' => '3']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("staff_planned[]", $staff_planned_for_work_done , ['class' => 'form-control number']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("staff_done[]", $staff_done_for_work_done , ['class' => 'form-control number']) !!}
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
                                $sub = \App\Subcontractor::find($subcontractor->subcontractor_id);
                                ?>
                                <div class="row" id="div-swid{{$subcontractor->id}}">
                                    <div class="col-sm-2">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <a href="#" onclick="subcontractorToWorkDelete({{$subcontractor->id}})"><i
                                                            class="fa fa-close"></i></a>
                                            </div>
                                            <div class="col-sm-10">
                                                {{$sub->subdetail->name}}
                                                {!! Form::hidden("subcontractors[]", $subcontractor->subcontractor_id)!!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::number("subcontractor_quantity[]", $subcontractor->quantity, ['class' => 'form-control', 'step' =>'1']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("subcontractor_unit[]", $subcontractor_unit_for_work_done , ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="col-sm-6">
                                        {!! Form::textarea("subcontractor_work_done[]", $subcontractor_work_done_for_work_done , ['class' => 'form-control', 'rows' => '3']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("subcontractor_planned[]", $subcontractor_planned_for_work_done , ['class' => 'form-control number']) !!}
                                    </div>
                                    <div class="col-sm-1">
                                        {!! Form::text("subcontractor_done[]", $subcontractor_done_for_work_done , ['class' => 'form-control number']) !!}
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
                    </div>
                </div>
            </div>
        </div>



        {{--GELEN MALZEMELER TABLE--}}
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

                                        <input type="text" class="form-control number"
                                               name="inmaterial-quantity[]"
                                               value="{{str_replace('.', ',', $inmaterial->quantity)}}"/>
                                    </div>

                                    <div class="col-sm-6">

                                        <input type="text" class="form-control"
                                               name="inmaterial-explanation[]"
                                               value="{{$inmaterial->explanation}}"/>
                                    </div>

                                </div>
                            @endforeach

                            {{--<div class="row">
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

                                    <input type="text" class="number form-control"
                                           name="inmaterial-quantity[]"
                                           value=""/>
                                </div>

                                <div class="col-sm-6">

                                    <input type="text" class="form-control"
                                           name="inmaterial-explanation[]"
                                           value=""/>
                                </div>
                            </div>--}}
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

                    </div>
                </div>
            </div>
        </div>
        {{--GİDEN MALZEMELER TABLE--}}
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Giden Malzemeler Tablosu
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
                            <div class="col-sm-2"><strong>GİDEN MALZEME</strong></div>
                            <div class="col-sm-2"><strong>GÖNDERİLDİĞİ YER</strong></div>
                            <div class="col-sm-1"><strong>BİRİM</strong></div>
                            <div class="col-sm-1"><strong>MİKTAR</strong></div>
                            <div class="col-sm-6"><strong>AÇIKLAMA</strong></div>
                        </div>

                        {!! Form::open([
                                                                        'url' => "/tekil/$site->slug/save-outgoing-material",
                                                                        'method' => 'POST',
                                                                        'class' => 'form',
                                                                        'id' => 'outgoingMaterialInsertForm',
                                                                        'role' => 'form'
                                                                        ]) !!}
                        {!! Form::hidden('report_id', $report->id) !!}

                        <div id="outmaterial-insert">
                            @foreach($outmaterials as $outmaterial)
                                <div class="row" id="div-outmaterialid{{$outmaterial->id}}">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2"><a href="#"
                                                                         onclick="outmaterialsDelete({{$outmaterial->id}})"><i
                                                                class="fa fa-close"></i></a></div>
                                                <div class="col-sm-10">
                                                    <span>{{\App\Material::find($outmaterial->material_id)->material}}</span>
                                                    {!! Form::hidden('outmaterials[]', $outmaterial->material_id) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">

                                        <input type="text" class="form-control"
                                               name="outmaterial-from[]"
                                               value="{{$outmaterial->coming_from}}"/>
                                    </div>
                                    <div class="col-sm-1">

                                        <input type="text" class="form-control"
                                               name="outmaterial-unit[]"
                                               value="{{$outmaterial->unit}}"/>
                                    </div>
                                    <div class="col-sm-1">

                                        <input type="text" class="number form-control"
                                               name="outmaterial-quantity[]"
                                               value="{{str_replace('.', ',', $outmaterial->quantity)}}"/>
                                    </div>

                                    <div class="col-sm-6">

                                        <input type="text" class="form-control"
                                               name="outmaterial-explanation[]"
                                               value="{{$outmaterial->explanation}}"/>
                                    </div>

                                </div>
                            @endforeach

                            {{--<div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="outmaterials[]"
                                                class="js-example-basic-single form-control">

                                            {!! $outmaterial_options !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2">

                                    <input type="text" class="form-control"
                                           name="outmaterial-from[]"
                                           value=""/>
                                </div>
                                <div class="col-sm-1">

                                    <input type="text" class="form-control"
                                           name="outmaterial-unit[]"
                                           value=""/>
                                </div>
                                <div class="col-sm-1">

                                    <input type="text" class="number form-control"
                                           name="outmaterial-quantity[]"
                                           value=""/>
                                </div>

                                <div class="col-sm-6">

                                    <input type="text" class="form-control"
                                           name="outmaterial-explanation[]"
                                           value=""/>
                                </div>
                            </div>--}}
                        </div>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group pull-right">
                                    <a href="#" class="btn btn-primary btn-flat add-outmaterial-row">
                                        Satır Ekle
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
        </div>
        {{--END OF GİDEN MALZEMELER TABLOSU--}}

        {{--PUANTAJ AND YEMEK TABLE--}}
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Puantaj ve Yemek Tablosu
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
                        <p>İlgili personeli puantaj tablosundan çıkarmak için yukarıdaki personel tablolarını
                            kullanınız</p>
                        <div class="row">
                            <div class="col-sm-3 text-center"><strong>PERSONEL</strong></div>
                            <div class="col-sm-5 text-center"><strong>PUANTAJ</strong></div>
                            <div class="col-sm-4 text-center"><strong>YEMEK</strong></div>
                        </div>

                        {!! Form::open([
                                                                        'url' => "/tekil/$site->slug/save-shifts-meals",
                                                                        'method' => 'POST',
                                                                        'class' => 'form',
                                                                        'id' => 'shiftsMealsForm',
                                                                        'role' => 'form'
                                                                        ]) !!}
                        {!! Form::hidden('report_id', $report->id) !!}
                        <div id="personnel-insert">

                        </div>
                        <div id="personnel-helper-block"></div>
                        <?php
                        $pre_tit = 'in1t';
                        ?>
                        @for($i = 0; $i < sizeof($report_personnel_id_arr); $i++)
                            <?php
                            $per = $report_personnel_id_arr[$i];
                            $report_person = Personnel::find($per);
                            $report_shift = $report->shift()->where('personnel_id', $report_person->id)->first();
                            $report_meal = $report->meal()->where('personnel_id', $report_person->id)->first();

                            $cur_tit = $report_person->isSitePersonnel() ? 'Ana Yüklenici' : $report_person->personalize->subdetail->name;
                            $overtime_options = '<option></option>';
                            foreach (\App\Overtime::all() as $overtime) {
                                $overtime_options .= "<option value=\"$overtime->id\"";
                                $overtime_options .=    (!empty($report_shift->overtime) && ($report_shift->overtime->id == $overtime->id)) ? "selected" : ""
                                        .">" . TurkishChar::tr_up($overtime->name) . "</option>";

                            }
                            ?>
                            @if(!(strpos($cur_tit, $pre_tit) !== false))
                                <div class="row">
                                    <div class="col-sm-12">
                                        <legend>{{$cur_tit}}</legend>
                                    </div>
                                </div>
                            @endif
                            <?php
                            $pre_tit = $cur_tit;
                            ?>
                            <div class="row overtimes-meals-div" id="personnel-div-{{$per}}">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-1"></div>
                                            <div class="col-sm-3">{{$report_person->tck_no}}</div>
                                            <div class="col-sm-8">
                                                {{\App\Library\TurkishChar::tr_camel($report_person->name) . " (" . \App\Library\TurkishChar::tr_up($report_person->staff()->first()->staff) . ")"}}</div>

                                            {!! Form::hidden('personnel[]', $report_person->id) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <select name="overtimes[]"
                                                    class="js-overtime-select form-control">
                                                {!! ($overtime_options) !!}
                                            </select>
                                        </div>

                                        <div class="col-sm-4 overtime-input-div">
                                            {!! Form::text('overtime', (!empty($report_shift->overtime) && stripos($report_shift->overtime->name, "Fazla Mesai") !== false ? str_replace('.', ',', $report_shift->hour) : null), ['class' => 'number form-control overtime_input',
                                                                                'placeholder' => 'Mesai (Saat)',
                                                                                 !empty($report_shift->overtime) && stripos($report_shift->overtime->name, "Fazla Mesai") === false ? "" : "disabled"]) !!}
                                        </div>

                                        {!! Form::hidden('overtime_arr[]', $report_shift->hour, ['class' => 'overtime-hidden']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row col-sm-offset-1">
                                        <div class="col-sm-3">
                                            <label class="checkbox-inline">
                                                {!! Form::checkbox("meals-$i"."[]", '1', (!is_null($report_meal) && (int) $report_meal->meal%2 == 1) ? true : false, ['class' => 'personnel-row-cb']) !!}
                                                Kahvaltı
                                            </label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="checkbox-inline">
                                                {!! Form::checkbox("meals-$i"."[]", '2', (!is_null($report_meal) && in_array((int)$report_meal->meal, [2,6,7])) ? true : false, ['class' => 'personnel-row-cb']) !!}
                                                Öğle
                                            </label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="checkbox-inline">
                                                {!! Form::checkbox("meals-$i"."[]", '4', (!is_null($report_meal) && (int)$report_meal->meal>=4) ? true : false, ['class' => 'personnel-row-cb']) !!}
                                                Akşam
                                            </label>
                                        </div>
                                        {!! Form::hidden('meals_arr[]', (!is_null($report_meal) ? $report_meal->meal : "0"), ['class' => 'meals_arr']) !!}
                                    </div>
                                </div>
                            </div>
                        @endfor
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

                    </div>
                </div>
            </div>
        </div>
        {{--END OF GİDEN MALZEMELER TABLOSU--}}

    @else

        @include('tekil._locked')

    @endif

    <div class="row hidden-print">
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
                                {{--photos--}}
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
                                    @foreach($report->photo as $report_site_photo)
                                        <?php
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_photo->file()->first()->path);
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                        if (strpos($report_site_photo->file()->first()->name, 'pdf') !== false) {
                                            $image = URL::to('/') . "/img/pdf.jpg";
                                        } elseif (strpos($report_site_photo->file()->first()->name, 'doc') !== false) {
                                            $image = URL::to('/') . "/img/word.png";
                                        } else {
                                            $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_photo->name;
                                        }
                                        ?>

                                        <a id="lb-link-{{$report_site_photo->id}}" href="{{$image}}"
                                           data-toggle="lightbox" data-gallery="reportsitephotos"
                                           data-footer="<a data-dismiss='modal' class='remove-files' href='#' data-fileId='{{$report_site_photo->id}}'>Dosyayı Sil<a/>"
                                           class="col-sm-4">
                                            <img src="{{$image}}" class="img-responsive">
                                            {{$report_site_photo->file()->first()->name}}
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
                                    @foreach($report->receipt as $report_site_receipt)
                                        <?php
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_receipt->file()->first()->path);
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                        $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_receipt->file()->first()->name;
                                        if (strpos($report_site_receipt->file()->first()->name, 'pdf') !== false) {
                                            $image = URL::to('/') . "/img/pdf.jpg";
                                        } elseif (strpos($report_site_receipt->file()->first()->name, 'doc') !== false) {
                                            $image = URL::to('/') . "/img/word.png";
                                        }
                                        ?>

                                        <a id="lb-link-{{$report_site_receipt->id}}" href="{{$image}}"
                                           data-toggle="lightbox" data-gallery="reportsitereceipts"
                                           data-footer="<a data-dismiss='modal' class='remove-files' href='#' data-fileId='{{$report_site_receipt->id}}'>Dosyayı Sil<a/>"
                                           class="col-sm-4">
                                            <img src="{{$image}}" class="img-responsive">
                                            {{$report_site_receipt->file()->first()->name}}
                                        </a>

                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else

                        {{--photos--}}
                        <div class="row">
                            <div class="col-sm-6">

                                @foreach($report->photo as $report_site_photo)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_photo->file()->first()->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    if (strpos($report_site_photo->file()->first()->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($report_site_photo->file()->first()->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    } else {
                                        $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_photo->file()->first()->name;
                                    }
                                    ?>

                                    <a id="lb-link-{{$report_site_photo->id}}" href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitephotos"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$report_site_photo->file()->first()->name}}
                                    </a>

                                @endforeach


                            </div>
                            <div class="col-sm-6">

                                @foreach($report->receipt as $report_site_receipt)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $report_site_receipt->file()->first()->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_receipt->file()->first()->name;
                                    if (strpos($report_site_receipt->file()->first()->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($report_site_receipt->file()->first()->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    }
                                    ?>

                                    <a id="lb-link-{{$report_site_receipt->id}}" href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitereceipts"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$report_site_receipt->file()->first()->name}}
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
        <div class="row hidden-print">
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
                                @foreach($site_reports as $site_report)
                                    @foreach($site_report->photo as $site_photo)
                                        <?php
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_photo->file()->first()->path);
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                        $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_photo->file()->first()->name;
                                        if (strpos($site_photo->file()->first()->name, 'pdf') !== false) {
                                            $image = URL::to('/') . "/img/pdf.jpg";
                                        } elseif (strpos($site_photo->file()->first()->name, 'doc') !== false) {
                                            $image = URL::to('/') . "/img/word.png";
                                        }
                                        ?>

                                        <a href="{{$image}}"
                                           data-toggle="lightbox" data-gallery="reportsitephotos"
                                           class="col-sm-4">
                                            <img src="{{$image}}" class="img-responsive">
                                            {{$site_photo->file()->first()->name}}
                                        </a>

                                    @endforeach
                                @endforeach
                            </div>
                            <div class="col-sm-6">
                                @foreach($site_reports as $site_report)
                                    @foreach($site_report->receipt as $site_receipt)
                                        <?php
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_receipt->file()->first()->path);
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                        $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_receipt->file()->first()->name;
                                        if (strpos($site_receipt->file()->first()->name, 'pdf') !== false) {
                                            $image = URL::to('/') . "/img/pdf.jpg";
                                        } elseif (strpos($site_receipt->file()->first()->name, 'doc') !== false) {
                                            $image = URL::to('/') . "/img/word.png";
                                        }
                                        ?>

                                        <a href="{{$image}}"
                                           data-toggle="lightbox" data-gallery="reportsitereceipts"
                                           class="col-sm-4">
                                            <img src="{{$image}}" class="img-responsive">
                                            {{$site_receipt->file()->first()->name}}
                                        </a>

                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (!isset($report_date))
        <div class="row hidden-print">
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
@stop