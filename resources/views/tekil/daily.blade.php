<?php
use App\Library\TurkishChar;use App\Library\Weather;
use App\Material;use App\Personnel;
use App\Site;
use Carbon\Carbon;
use App\Staff;
use Illuminate\Support\Facades\Session;
$my_weather = new Weather(0, $site->city->name);
$weather_symbol = '';



if (session()->has("data")) {
    $report_date = session('data')["date"];
}

$anchor = session()->has('anchor') ? session()->get('anchor') : null;

$today = Carbon::now()->toDateString();

$locked = true;
if (!is_null(Auth::user()->report()->where('reports.created_at', '=', $report->created_at)->first())) {
    $locked = false;
} else if ($report->created_at < $today) {
    $locked = true;
} else if (!$report->locked()) {
    $locked = false;
}

$staffs = Staff::allStaff();

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
$now = $report->created_at;
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
        if (!($shift->personnel()->get()->isEmpty())) {

            if (!empty($shift->personnel()->first()->personalize->id) && $shift->personnel()->first()->personalize->id == $report_subcontractor->id) {
                array_push($subcontractor_report_personnel[$i], $shift->personnel_id);
            }
        }
    }
    $i++;
}

$subcontractor_staffs = \App\Staff::allStaff();
$subcontractor_staff_total = 0;

$personnel_arr = [];
$report_personnel_id_arr = [];
$site_report_personnel = [];
foreach ($report->shift()->join('personnel', 'personnel_id', '=', 'personnel.id')->orderBy('personnel.personalize_type', 'ASC')->orderBy('personnel.personalize_id', 'ASC')->get() as $shift) {
    array_push($report_personnel_id_arr, $shift->personnel_id);
    if ($shift->personnel()->withTrashed()->first()->isSitePersonnel()) {
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
    foreach ($dept->staff()->notGarden()->get() as $staff) {
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
$subcontractor_options = "'<option></option>'+\n";
foreach ($all_subcontractors as $subcontractor) {
    if (count($subcontractor->subdetail)) {
        $subcontractor_personnel_options .= "<optgroup label=\"" . $subcontractor->subdetail->name;
        $subcontractor_options .= "'<option value=\"$subcontractor->id\">" . TurkishChar::tr_camel($subcontractor->subdetail->name) . "</option>'+\n";
        foreach ($subcontractor->manufacturing()->get() as $manufacture) {
            $subcontractor_personnel_options .= " (" . TurkishChar::tr_up($manufacture->name) . ")";
        }
        $subcontractor_personnel_options .= "\">";
        foreach ($subcontractor->personnel()->get() as $per) {
            $subcontractor_personnel_options .= "<option value=\"$per->id\">" . TurkishChar::tr_up($per->staff()->first()->staff) . ": " . TurkishChar::tr_camel($per->name) . "(" . $per->tck_no . ")</option>";
        }
    }
}


$equipment_options = '';
$equipment_options_js = '';
foreach ($site->equipment()->get() as $equipment) {
    if (isset($report_equipment_arr)) {
        if (!in_array($equipment->id, $report_equipment_arr)) {
            $equipment_options .= "<option value=\"$equipment->id\">" . TurkishChar::tr_up($equipment->name) . "</option>";
            $equipment_options_js .= "'<option value=\"$equipment->id\">" . str_replace("'", "`", TurkishChar::tr_up($equipment->name)) . "</option>'+\n";
        }
    } else {
        $equipment_options .= "<option value=\"$equipment->id\">" . TurkishChar::tr_up($equipment->name) . "</option>";
        $equipment_options_js .= "'<option value=\"$equipment->id\">" . str_replace("'", "`", TurkishChar::tr_up($equipment->name)) . "</option>'+\n";
    }
}

$inmaterial_options = "";
$inmaterial_options_js = "";


foreach (Material::all() as $inmaterial) {
    $inmaterial_options .= "<option value=\"$inmaterial->id\">" . TurkishChar::tr_up($inmaterial->material) . "</option>";
    $inmaterial_options_js .= "'<option value=\"$inmaterial->id\">" . str_replace("'", "`", TurkishChar::tr_up($inmaterial->material)) . "</option>'+\n";
}

$outmaterial_options = "";
$outmaterial_options_js = "";


foreach (Material::all() as $outmaterial) {
    $outmaterial_options .= "<option value=\"$outmaterial->id\">" . TurkishChar::tr_up($outmaterial->material) . "</option>";
    $outmaterial_options_js .= "'<option value=\"$outmaterial->id\">" . str_replace("'", "`", TurkishChar::tr_up($outmaterial->material)) . "</option>'+\n";
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

$yesterdays_report = $site->report()->where('created_at', Carbon::yesterday()->toDateString())->first();

if ($viewCount == 1 && !is_null($yesterdays_report) && !is_null($yesterdays_report->notes)) {
    $notes = $yesterdays_report->notes;
}

?>
@extends('tekil/layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link href="<?= URL::to('/'); ?>/css/dropzone.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/weather-icons.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/weather-icons-wind.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/bootstrap-editable.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/dropzone.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-editable.min.js" type="text/javascript"></script>

    <script>
        $('#attachTagBtn').on('click', function (e) {
            e.preventDefault();
            $.post("/tekil/{{$site->slug}}/attach-tag", $('#attach-tag').serialize())
                    .done(function () {
                        $('.success-message').html('<p class="alert-success">Kayıt başarılı!</p>');
                        $('p.alert-success').not('.alert-important').delay(7500).slideUp(300);
                    });
        });
                @if(!empty($anchor))
                var anchor = "{{$anchor}}";
        $('html,body').animate({scrollTop: $(anchor).offset().top}, 'slow');
                @endif
var puantajApp = angular.module('dailyApp', [], function ($interpolateProvider) {
                    $interpolateProvider.startSymbol('<%');
                    $interpolateProvider.endSymbol('%>');
                }).controller('swController', function ($scope, $http) {
                    $scope.sws = [];
                    $scope.message = '';
                    $scope.swUnit = '';
                    $scope.swQuantity = '';
                    $scope.swPlanned = '';
                    $scope.swDone = '';
                    $scope.swWorkDone = '';
                    $scope.swSelected = '';
                    $scope.subcontractors = [];

                    $scope.getSubcontractors = function () {
                        $http.post("{{url("/tekil/$site->slug/retrieve-subcontractors")}}", {
                            sid: "{{$site->id}}"
                        }).then(function (response) {
                            $scope.subcontractors = response.data;
                        });
                    };

                    $scope.pwUnit = '';
                    $scope.pwQuantity = '';
                    $scope.pwPlanned = '';
                    $scope.pwDone = '';
                    $scope.pwWorkDone = '';
                    $scope.pw = [];


                    $scope.getPw = function () {
                        $http.post("{{url("/tekil/$site->slug/retrieve-pw")}}", {
                            rid: "{{$report->id}}"
                        }).then(function (response) {
                            $scope.pw = response.data;
                            $scope.pwUnit = $scope.pw[0].unit;
                            $scope.pwQuantity = parseInt($scope.pw[0].quantity);
                            $scope.pwPlanned = $scope.pw[0].planned;
                            $scope.pwDone = $scope.pw[0].done;
                            $scope.pwWorkDone = $scope.pw[0].work_done;
                        });
                    };

                    $scope.getPw();

                    $scope.getSubcontractors();

                    $scope.getSw = function () {
                        $http.post("{{url("/tekil/$site->slug/retrieve-sw")}}", {
                            rid: "{{$report->id}}"
                        }).then(function (response) {
                            $scope.sws = response.data;
                        });
                    };
                    $scope.getSw();

                    $scope.addSwunit = function () {
                        $scope.message = '';
                        $http.post("{{url("/tekil/$site->slug/save-work-done")}}", {
                            rid: "{{$report->id}}",
                            subid: $scope.swSelected.id,
                            quantity: $scope.swQuantity,
                            unit: $scope.swUnit,
                            planned: $scope.swPlanned,
                            done: $scope.swDone,
                            works_done: $scope.swWorkDone
                        }).
                        then(function (response) {
                            $scope.message = 'Kayıt başarılı';
                            $scope.getSw();
                            $scope.swUnit = '';
                            $scope.swQuantity = '';
                            $scope.swPlanned = '';
                            $scope.swDone = '';
                            $scope.swWorkDone = '';
                            $scope.swSelected = '';
                        });
                    };
                    $scope.addPwunit = function () {
                        $scope.message = '';
                        $http.post("{{url("/tekil/$site->slug/save-pw")}}", {
                            rid: "{{$report->id}}",
                            quantity: $scope.pwQuantity,
                            unit: $scope.pwUnit,
                            planned: $scope.pwPlanned,
                            done: $scope.pwDone,
                            works_done: $scope.pwWorkDone
                        }).
                        then(function (response) {
                            $scope.message = 'Kayıt başarılı!';
                            $scope.getPw();
                        });
                    };


                    $scope.remove_field = function (item) {
                        $scope.message = '';
                        $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-swunit", {
                            id: item.id
                        }).then(function () {
                            $scope.message = 'Silme işlemi başarılı';
                            $scope.getSw();
                            angular.forEach($scope.subcontractors, function (value, key) {
                                if (parseInt(item.subid) == value.id) {
                                    $scope.swSelected = value;
                                }
                                $scope.swUnit = item.unit;
                                $scope.swQuantity = item.quantity;
                                $scope.swPlanned = item.planned;
                                $scope.swDone = item.done;
                                $scope.swWorkDone = item.work_done;
                            });

                        });

                    }
                }).filter('numberFormatter', function () {
                    return function (data) {
                        return $.number(data, 2, ',', '.');
                    }
                }).filter('searchFor', function () {
                    return function (arr, searchStr) {
                        if (!searchStr) {
                            return arr;
                        }
                        var result = [];
                        searchStr = searchStr.turkishToLower();
                        angular.forEach(arr, function (item) {
                            if ((item.date + ' ' + item.bill + ' ' + item.subname).turkishToLower().indexOf(searchStr) !== -1) {
                                result.push(item);
                            }
                        });
                        return result;
                    };
                });
        $.fn.editable.defaults.mode = 'inline';
        $(document).ready(function () {
            $('.inline-edit').editable({
                validate: true
            });
        });
    </script>

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
                '<div class="col-sm-1">-</div><div class="col-sm-2"><input type="text" class="form-control" name="inmaterial-from[]"/></div>'+
                '<div class="col-sm-1"><input type="text" class="form-control" name="inmaterial-unit[]"/></div>'+
                '<div class="col-sm-1"><input type="text" class="form-control number" name="inmaterial-quantity[]"/></div>'+
                '<div class="col-sm-4"><input type="text" class="form-control" name="inmaterial-explanation[]"/></div>'+
                '<div class="col-sm-1"><input type="text" class="form-control" name="inmaterial-irsaliye[]"/></div></div>'); //add input box
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
    @if(isset($notes))
        <script>
            $('#notesModal').modal('show');
        </script>
    @endif
    <script>


        var setRdHidden = function (pid) {
            var myOpt = $('#select-' + pid + ' :selected');
            var hiddenEl = $(myOpt).parent().parent().parent().find('.overtime-hidden');
            var overtimeIn = $(myOpt).parent().parent().parent().find('.overtime_input');
            if ($(myOpt).text().match("FAZLA MESAİ")) {
                overtimeIn.prop('disabled', false);
            }
            else {
                hiddenEl.val('999');
                overtimeIn.val('');
                overtimeIn.prop('disabled', true);
            }
        };

        function checkForOvertime(pid) {
            $('#select-' + pid).on("select2:select", setRdHidden(pid));
        }
        $(document).ready(function () {
            var data = [{id: 1, text: 'İşveren ({!! $site->employer!!})'}, {
                id: 0,
                text: 'İdare({!! $site->management_name!!})'
            },
                {id: 2, text: 'Yapı Denetim({!! $site->building_control !!})'}, {id: 3, text: 'İSG ({!! $site->isg!!})'}
            ];

            $(".js-example-data-array").select2({
                data: data
            });
            $("#dateRangePicker > input").val("{{App\Library\CarbonHelper::getTurkishDate($report->created_at)}}");


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

//            subcontractor gelecek
            var subcontractorToWorkDoneWrapper = $("#subcontractor-to-work-insert"); //Fields wrapper
            var addsubcontractorToWorkDone = $(".add-subcontractor-to-work-done-row"); //Add button ID

            $(addsubcontractorToWorkDone).click(function (e) { //on add input button click
                $(subcontractorToWorkDoneWrapper).append('<div class="form-group"><div class="row"><div class="col-sm-2">' +
                        '<div class="row"><div class="col-sm-2"><a href="#" class="remove_field"><i class="fa fa-close"></i></a></div>' +
                        '<div class="col-sm-10">' +
                        '<select name="subcontractors[]" class="js-additional-subcontractor form-control">' +
                        {!! $subcontractor_options !!}
                                '</select></div></div></div>' +
                        '<div class="col-sm-1"><input type="number" step="1" class="form-control" name="subcontractor_quantity[]"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="form-control" name="subcontractor_unit[]"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="number form-control" name="subcontractor_planned[]"/></div>' +
                        '<div class="col-sm-1"><input type="text" class="number form-control" name="subcontractor_done[]"/></div>' +
                        '<div class="col-sm-6"><textarea class="form-control" name="subcontractor_work_done[]" rows="3"/></div>' +
                        '</div></div>'); //add input box
                $(".js-additional-subcontractor").select2();
                $('.number').number(true, 2, ',', '.');

            });

            $(subcontractorToWorkDoneWrapper).on("click", ".remove_field", function (e) { //user click on remove text
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
                var data = [{id: 1, text: 'İşveren ({!! $site->employer!!})'}, {
                    id: 0,
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
            var overtimeSelect = $(".js-overtime-select");

            overtimeSelect.select2({
                placeholder: "Puantaj seçiniz",
                allowClear: true
            });


//            $("input.overtime-radio").on("click", setRdHidden);
            $('.overtime_input').keyup(function () {
                $(this).parent().parent().parent().parent().find('.overtime-hidden').val($(this).val());
            });


            //END PUANTAJ
        });
    </script>
    <script>

        $('#shiftsMealsForm').on("submit", function (e) {
            var overtimes = $("input[name='overtime_arr[]']");
            var personnelHelper = $('#personnel-helper-block');
            var personnel = $("select[name='personnel[]']");
            var personnelList = [];
            $(personnelHelper).html();
            $.each(overtimes, function (index, object) {
                if ($(object).val().length == 0) {
                    e.preventDefault();
                    $(personnelHelper).html('<span class="text-danger">Puantajlar tüm personel için seçili olmalı veya fazla mesailer doldurulmalı!</span>');

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


        $('.staffToWorkDelete').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-pwunit"}}',
                data: {
                    "pwid": id
                }
            }).success(function () {
                $('#div-pwid' + id).remove();
            });
        });

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

        $('.equipmentDetach').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
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
        });


        $('.inmaterialsDelete').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-inmaterial"}}',
                data: {
                    "inmaterialid": id
                }
            }).success(function () {
                $('#div-inmaterialid' + id).remove();
            });
        });


        $('.outmaterialsDelete').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-outmaterial"}}',
                data: {
                    "outmaterialid": id
                }
            }).success(function () {
                $('#div-outmaterialid' + id).remove();
            });
        });


        $('.mainStaffDelete').on('click', function (e) {
            e.preventDefault();
            var column = $(this).data('column');
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
        });


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

            function getReportDays(e) {
                console.log(moment(e.date).format('YYYY-MM-DD'));
                $.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-report-days", {
                    'date': moment(e.date).format('YYYY-MM-DD')
                }, function (data) {

                    var tdArr = $('div.datepicker-days > table > tbody').find($('td.day').not('.new, .old, .active'));
                    $.each(tdArr, function (index, value) {
                        if ($.inArray(parseInt(tdArr.eq(index).text()), data) >= 0) {
                            tdArr.eq(index).css({'background-color': 'rgba(50, 118, 177, 0.35)'});
                        }
                        else {
                            tdArr.eq(index).css({'background-color': 'white'});
                        }

                    });
                });
            }

            var firstLoad = false;

            $('#dateRangePicker').datepicker().on('show', function (e) {
                if (!firstLoad) {
                    firstLoad = true;
                    getReportDays(e);
                }
            });
            $('#dateRangePicker').datepicker().on('changeMonth', function (e) {
                getReportDays(e);
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
    if (!empty($report->isg_staff))
        $total_management += $report->isg_staff;
    ?>
    <div ng-app="dailyApp">
        @if(!$locked)
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="box box-{{$left < $day_warning ? "danger" : "primary"}} box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{$site->job_name}} Projesi Raporu

                                <small style="color: #f9f9f9">{{\App\Library\CarbonHelper::getTurkishDate($report->created_at)}}</small>

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
                                        <td>{{ !is_null($report->weather) ? $report->wind :$my_weather->getWind()}}
                                            m/s
                                        </td>
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
                                                                         class="mainStaffDelete"
                                                                         data-column="management_staff"><i
                                                                class="fa fa-close"></i></a></div>
                                                <div class="col-sm-7">
                                                    <label for="main-staff-quantity[]" class="control-label">İdare
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
                                                                         class="mainStaffDelete"
                                                                         data-column="employer_staff"><i
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
                                                                         class="mainStaffDelete"
                                                                         data-column="building_control_staff"><i
                                                                class="fa fa-close"></i></a></div>
                                                <div class="col-sm-7">
                                                    <label for="main-staff-quantity[]" class="control-label">Yapı
                                                        Denetim
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
                                                                         class="mainStaffDelete"
                                                                         data-column="isg_staff"><i
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
                                                <div class="col-sm-3"><span style="font-style: italic">TCK No</span>
                                                </div>
                                                <div class="col-sm-5"><span style="font-style: italic">Ad Soyad</span>
                                                </div>
                                                <div class="col-sm-3"><span style="font-style: italic">İş Kolu</span>
                                                </div>
                                            </div>
                                        @endif
                                        @for($i = 0; $i < sizeof($site_report_personnel); $i++)
                                            <?php
                                            $per = $site_report_personnel[$i];
                                            $report_person = Personnel::withTrashed()->find($per);
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

                    <div class="row" id="subcontractor_staff">
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
                                                                                     class="equipmentDetach"
                                                                                     data-id="{{$equipment->id}}"><i
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
                        <div class="box-body" ng-controller="swController">

                            <p class="text-success alert-success" ng-hide="!message"><%message%></p>

                            <h4>Alt Yüklenici Ekle</h4>
                            <p>Alt yüklenicileri sırayla ekleyebilirsiniz.</p>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <select class="form-control"
                                                ng-options="subcontractor as subcontractor.name for subcontractor in subcontractors track by subcontractor.id"
                                                ng-model="swSelected">
                                            <option value="" selected disabled>Alt Yüklenici</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <input name="swQuantity" class="form-control" type="text" ng-model="swQuantity"
                                               placeholder="Kişi sayısı">
                                    </div>
                                    <div class="col-sm-1">
                                        <input class="form-control" type="text" ng-model="swUnit" name="swunit"
                                               placeholder="Ölçü Birimi">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="text" class="number form-control" ng-model="swPlanned"
                                               name="planned" placeholder="Planlanan">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="text" class="number form-control" ng-model="swDone" name="done"
                                               placeholder="Yapılan">

                                    </div>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" ng-model="swWorkDone" rows="3"
                                                  name="work_done"></textarea>
                                    </div>
                                    <div class="col-sm-2">
                                        <a class="btn btn-warning btn-flat btn-block" ng-click="addSwunit()">
                                            Alt Yüklenici Ekle
                                        </a>
                                    </div>
                                </div>
                            </div>


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
                                <div class="col-sm-1">
                                    <span><strong>PLANLANAN</strong></span>
                                </div>
                                <div class="col-sm-1">
                                    <span><strong>YAPILAN</strong></span>
                                </div>
                                <div class="col-sm-6">
                                    <span><strong>YAPILAN İŞLER</strong></span>
                                </div>
                            </div>

                            @if(!empty($report->pwunit()->first()))
                                <div id="staff-to-work-insert">
                                    <?php
                                    $staff = $report->pwunit()->first();
                                    ?>
                                    <div class="form-group">
                                        <div class="row" id="div-pwid{{$staff->id}}">
                                            <div class="col-sm-2">
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <a href="#" class="staffToWorkDelete"
                                                           data-id="{{$staff->id}}"><i
                                                                    class="fa fa-close"></i></a>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        {{$staff->staff->staff}}
                                                        {!! Form::hidden("staffs[]", $staff->staff_id)!!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="number" class="form-control" ng-model="pwQuantity">
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="text" class="form-control" ng-model="pwUnit">
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="text" class="form-control number" ng-model="pwPlanned">
                                            </div>
                                            <div class="col-sm-1">
                                                <input type="text" class="form-control number" ng-model="pwDone">
                                            </div>
                                            <div class="col-sm-4">
                                                <textarea name="pw-work-done" id="pw-work-done" rows="3"
                                                          class="form-control" ng-model="pwWorkDone"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <a href="#!" class="btn btn-success btn-flat btn-block"
                                                   ng-click="addPwunit()">Kaydet</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endif

                            <div id="subcontractor-to-work-insert">
                                <div class="form-group" ng-repeat="sw in sws track by $index">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <a href="#!" ng-click="remove_field(sw)"><i
                                                                class="fa fa-close"></i></a>
                                                </div>
                                                <div class="col-sm-10">
                                                    <%sw.name%>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            <%sw.quantity%>
                                        </div>
                                        <div class="col-sm-1">
                                            <%sw.unit%>
                                        </div>
                                        <div class="col-sm-1">
                                            <%sw.planned|numberFormatter%>
                                        </div>
                                        <div class="col-sm-1">
                                            <%sw.done | numberFormatter %>
                                        </div>
                                        <div class="col-sm-6">
                                            <%sw.work_done%>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--Locked if--}}
                    </div>
                </div>
            </div>


            {{--GELEN MALZEMELER TABLE--}}
            <div class="row" id="incoming_table">
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
                                <div class="col-sm-1"><strong>TAL. NO</strong></div>
                                <div class="col-sm-2"><strong>GELDİĞİ YER</strong></div>
                                <div class="col-sm-1"><strong>BİRİM</strong></div>
                                <div class="col-sm-1"><strong>MİKTAR</strong></div>
                                <div class="col-sm-4"><strong>AÇIKLAMA</strong></div>
                                <div class="col-sm-1"><strong>İRS. NO</strong></div>
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

                                        {!! Form::hidden('inmat-id[]', $inmaterial->id) !!}
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-2"><a href="#"
                                                                             class="inmaterialsDelete"
                                                                             data-id="{{$inmaterial->id}}"><i
                                                                    class="fa fa-close"></i></a></div>
                                                    <div class="col-sm-10">
                                                        <span>{{\App\Material::find($inmaterial->material_id)->material}}</span>
                                                        {!! Form::hidden('inmaterials[]', $inmaterial->material_id) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1">
                                            {{ !is_null($inmaterial->demand) ? $inmaterial->demand->id : "-" }}
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

                                        <div class="col-sm-4">

                                            <input type="text" class="form-control"
                                                   name="inmaterial-explanation[]"
                                                   value="{{$inmaterial->explanation}}"/>
                                        </div>
                                        <div class="col-sm-1">
                                            <input type="text" class="form-control"
                                                   name="inmaterial-irsaliye[]"
                                                   value="{{$inmaterial->irsaliye}}"/>
                                        </div>

                                    </div>
                                @endforeach
                            </div>


                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group pull-right">
                                        <a href="#" data-target="#demandsModal" data-toggle="modal"
                                           class="btn btn-warning btn-flat">
                                            Talepten Malzeme Ekle
                                        </a>

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
            <div class="row" id="outgoing_table">
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
                                                                             class="outmaterialsDelete"
                                                                             data-id="{{$outmaterial->id}}"><i
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
            <div class="row" id="shifts_meals">
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
                                <div class="col-sm-5 text-center"><strong>PERSONEL</strong></div>
                                <div class="col-sm-3 text-center"><strong>PUANTAJ</strong></div>
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
                            $overtime_options = '<option></option>\n';

                            foreach (\App\Overtime::all() as $overtime) {
                                $overtime_options .= "<option value=\"$overtime->id\">" . TurkishChar::tr_up($overtime->name) . "</option>\n";
                            }
                            ?>
                            @for($i = 0; $i < sizeof($report_personnel_id_arr); $i++)
                                <?php
                                $per = $report_personnel_id_arr[$i];
                                $report_person = Personnel::withTrashed()->find($per);
                                $report_shift = $report->shift()->where('personnel_id', $report_person->id)->first();
                                $report_meal = $report->meal()->where('personnel_id', $report_person->id)->first();

                                $cur_tit = $report_person->isSitePersonnel() ? 'Ana Yüklenici' : $report_person->personalize->subdetail->name;

                                $overtime_options = str_replace(" selected", "", $overtime_options);
                                if (!is_null($report_shift->overtime)) {
                                    $search = "<option value=\"$report_shift->overtime_id\">" . TurkishChar::tr_up($report_shift->overtime->name) . "</option>";
                                    $replace = "<option value=\"$report_shift->overtime_id\" selected>" . TurkishChar::tr_up($report_shift->overtime->name) . "</option>";
                                    $overtime_options = str_replace($search, $replace, $overtime_options);
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
                                    <div class="col-sm-5">
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
                                    <div class="col-sm-3">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <select name="overtimes[]" id="select-{{$per}}"
                                                        onchange="checkForOvertime({{$per}})"
                                                        class="js-overtime-select form-control">
                                                    {!! $overtime_options !!}
                                                </select>
                                            </div>

                                            <div class="col-sm-4 overtime-input-div">
                                                {!! Form::text('overtime', (!empty($report_shift->overtime) && stripos($report_shift->overtime->name, "Fazla Mesai") !== false ? str_replace('.', ',', $report_shift->hour) : null), ['class' => 'number form-control overtime_input',
                                                                                    'placeholder' => 'Mesai',
                                                                                     !empty($report_shift->overtime) && stripos($report_shift->overtime->name, "Fazla Mesai") !== false ? "" : "disabled"]) !!}
                                            </div>

                                            {!! Form::hidden('overtime_arr[]', $report_shift->hour, ['class' => 'overtime-hidden']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="row col-sm-offset-1">
                                            @if(!$report_person->isSitePersonnel() && empty($report_person->personalize->fee->first()->has_meal))
                                                <div class="col-sm-10">
                                                <span class="text-danger">
                                                    {{$report_person->personalize->subdetail->name}} için bu şantiyede yemek verilmemektedir!
                                                </span>
                                                </div>
                                            @else
                                                <div class="col-sm-3">
                                                    <label class="checkbox-inline">
                                                        {!! Form::checkbox("meals-$i"."[]", '1', (!is_null($report_meal) && (int) $report_meal->meal%2 == 1) ? true : false, ['class' => 'personnel-row-cb']) !!}
                                                        Kahvaltı
                                                    </label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label class="checkbox-inline">
                                                        {!! Form::checkbox("meals-$i"."[]", '2', (!is_null($report_meal) && in_array($report_meal->meal, [2,3,6,7])) ? true : false, ['class' => 'personnel-row-cb']) !!}
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
                                            @endif
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
            {{--END OF PUANTAJ TABLOSU--}}

        @else

            @include('tekil._locked')

        @endif

        @if(!$locked)
            <div class="row hidden-print" id="notes">
                <div class="col-xs-12 col-md-12">
                    <div class="box box-success box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Ertesi Gün Notları
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

                            <form action="{{url('/tekil/' . $site->slug . '/save-notes')}}" method="POST"
                                  class="form">
                                {!! csrf_field() !!}
                                <input type="hidden" name="rid" value="{{$report->id}}">

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-xs-12">
                                            <label class="control-label">Notlar: </label>
                                        </div>
                                        <div class="col-xs-12">
                                            {!! Form::textarea("notes", $report->notes , ['class' => 'form-control', 'rows' => '3']) !!}

                                        </div>
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-xs-12 ">
                                        <div class="form-group pull-right">
                                            <button type="submit" class="btn btn-success btn-flat">
                                                Kaydet
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

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
                                    <h4 style="text-align: center;color:#428bca;">Şantiye fotoğraflarını bu
                                        alana
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
                                                $image = URL::to('/') . "/img/doc.png";
                                            }
                                            $image_path = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_photo->file()->first()->name;

                                            ?>

                                            <a id="lb-link-{{$report_site_photo->id}}"
                                               href="{{$image_path}}"
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
                                    <h4 style="text-align: center;color:#428bca;">Şantiye faturalarını bu
                                        alana
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
                                            $image_path = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_receipt->file()->first()->name
                                            ?>

                                            <a id="lb-link-{{$report_site_receipt->id}}"
                                               href="{{$image_path}}"
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
                            <br>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4><strong>Etiketler</strong></h4>

                                    <p>Etiket eklemek veya düzenlemek için dosyaları yükledikten sonra sayfayı
                                        yenileyiniz.</p>
                                    <span class="success-message"></span>

                                </div>
                            </div>
                            @if(count($report->receipt) || count($report->photo))
                                <table class="table table-condensed table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Dosya Adı</th>
                                        <th>Etiket</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <form id="attach-tag">
                                        {!! csrf_field() !!}
                                        @foreach($report->photo as $report_site_photo)
                                            <tr>
                                                <input name="file_id[]" type="hidden"
                                                       value="{{$report_site_photo->file()->first()->id}}">
                                                <td>{{$report_site_photo->file()->first()->name}}</td>
                                                <td>
                                                    <select name="tags-{{$report_site_photo->file()->first()->id}}[]"
                                                            class="js-example-basic-multiple form-control"
                                                            multiple>
                                                        @foreach(\App\Tag::all() as $tag)
                                                            <?php
                                                            $selected = '';
                                                            foreach ($report_site_photo->file()->first()->tag as $ptag) {
                                                                if ((int)$tag->id == (int)$ptag->id) {
                                                                    $selected = "selected";
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <option value="{{$tag->id}}" {{$selected}}>{{$tag->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>

                                        @endforeach
                                        @foreach($report->receipt as $report_site_receipt)
                                            <tr>
                                                <input type="hidden"
                                                       value="{{$report_site_receipt->file()->first()->id}}">
                                                <td>{{$report_site_receipt->file()->first()->name}}</td>
                                                <td>
                                                    <div class="col-sm-12">
                                                        <select name="tags-{{$report_site_receipt->file()->first()->id}}[]"
                                                                class="js-example-basic-multiple form-control"
                                                                multiple>
                                                            @foreach(\App\Tag::all() as $tag)
                                                                <?php
                                                                $selected = '';
                                                                foreach ($report_site_receipt->file()->first()->tag as $ptag) {
                                                                    if ((int)$tag->id == (int)$ptag->id) {
                                                                        $selected = "selected";
                                                                        break;
                                                                    }
                                                                }
                                                                ?>
                                                                <option value="{{$tag->id}}" {{$selected}}>{{$tag->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </form>
                                    </tbody>

                                </table>
                                <br>
                                <div class="row">
                                    <div class="col-sm-12 ">
                                        <a href="#" class="btn btn-success btn-flat pull-right"
                                           id="attachTagBtn">Kaydet</a>
                                    </div>
                                </div>

                            @endif
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
                                            $image = URL::to('/') . "/img/doc.png";
                                        }
                                        $image_path = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_photo->file()->first()->name;

                                        ?>

                                        <a id="lb-link-{{$report_site_photo->id}}" href="{{$image_path}}"
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
                                        $image_path = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $report_site_receipt->file()->first()->name;

                                        ?>

                                        <a id="lb-link-{{$report_site_receipt->id}}" href="{{$image_path}}"
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
    </div>

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

    <div class="modal modal-info" role="dialog" id="notesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Notlar</h4>
                </div>
                <div class="modal-body">
                    <p>{{isset($notes) ? $notes : "Gösterilecek not bulunmamaktadır."}}</p>
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

    <div class="modal" role="dialog" id="demandsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Talepten Ekle</h4>
                </div>
                {!! Form::open([
                'url' => "/tekil/$site->slug/from-demand",
                'method' => 'POST',
                'class' => 'form',
                'id' => 'fromDemandform',
                'role' => 'form']) !!}
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-1 col-sm-offset-1">
                            <strong>Tal.No</strong>
                        </div>
                        <div class="col-sm-2">
                            <strong>Malzeme</strong>
                        </div>
                        <div class="col-sm-1">
                            <strong>Birim</strong>
                        </div>
                        <div class="col-sm-1">
                            <strong>Miktar</strong>
                        </div>
                        <div class="col-sm-2">
                            <strong>Firma</strong>
                        </div>
                        <div class="col-sm-4">
                            <strong>Açıklama</strong>
                        </div>
                    </div>
                    <?php

                    $i = 0;
                    ?>
                    @foreach($site->demand()->get() as $demand)
                        @foreach($demand->materials()->get() as $mat)

                            @if(!$mat->hasDemanded($demand->id))
                                <input type="hidden" name="demand[]" value="{{$demand->id}}">
                                <input type="hidden" name="material[]" value="{{$mat->id}}">
                                <input type="hidden" name="coming_from[]" value="{{$demand->firm}}">
                                <input type="hidden" name="quantity[]" value="{{$mat->pivot->quantity}}">
                                <input type="hidden" name="unit[]" value="{{$mat->pivot->unit}}">
                                <input type="hidden" name="explanation[]" value="{{$demand->explanation}}">
                                <input type="hidden" name="rid" value="{{$report->id}}">
                                <input type="hidden" name="mid[]" value="{{$mat->id}}">

                                <div class="row">
                                    <div class="col-sm-1">
                                        <input type="checkbox" name="checked-id[]" value="{{$i}}">

                                    </div>
                                    <div class="col-sm-1">
                                        {{$demand->id}}
                                    </div>
                                    <div class="col-sm-2">
                                        {{$mat->material}}
                                    </div>
                                    <div class="col-sm-1">
                                        {{$mat->pivot->unit}}
                                    </div>
                                    <div class="col-sm-1">
                                        {{$mat->pivot->quantity}}
                                    </div>
                                    <div class="col-sm-2">
                                        {{$demand->firm}}
                                    </div>
                                    <div class="col-sm-4">
                                        {{$demand->details}}
                                    </div>
                                </div>
                                <?php
                                $i++;
                                ?>
                            @endif
                        @endforeach
                    @endforeach

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-flat btn-primary">Kaydet
                    </button>
                    <button type="button" class="btn btn-default btn-flat pull-right" data-dismiss="modal">Kapat
                    </button>
                </div>
                {!! Form::close() !!}

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@stop