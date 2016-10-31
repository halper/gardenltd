<?php
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use Carbon\Carbon;

$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
$user = Auth::user();
$can_filter = false;
if ($user->isAdmin()) {
    $can_filter = true;
} else
    foreach ($user->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('puantaj-filtrele')) {
            $can_filter = true;
        }
    }
?>
@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/daterangepicker.css" rel="stylesheet"/>


@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/daterangepicker.js" type="text/javascript"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script>


        function setHeaderHeights() {
            if ($('#searchField').height() > $('#wageField').height()) {
                $('#wageField').height($('#searchField').height());
            }
            else {
                $('#searchField').height($('#wageField').height());
            }
        }

        String.prototype.turkishToLower = function () {
            var string = this;
            var letters = {"İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç"};
            string = string.replace(/(([İIŞĞÜÇÖ]))/g, function (letter) {
                return letters[letter];
            });
            return string.toLowerCase();
        };

        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.data = null;
            $scope.days = [];
            $scope.weekends = [];
            $scope.personnel = [];
            $scope.startDate = '';
            $scope.endDate = '';
            $scope.name = '';
            $scope.loading = false;

            $scope.getOvertimes = function () {
                if (!$('input[name="end-date"]').val()) {
                    $scope.startDate = moment().startOf('month').format('YYYY-MM-DD');
                    $scope.endDate = moment().format('YYYY-MM-DD');
                }
                else {
                    $scope.startDate = $('input[name="start-date"]').val();
                    $scope.endDate = $('input[name="end-date"]').val();
                }
                $scope.loading = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/overtimes", {
                    start_date: $scope.startDate,
                    end_date: $scope.endDate
                }).then(function (response) {
                    $scope.data = response.data;
                    $scope.days = response.data.days;
                    $scope.weekends = response.data.weekends;
                    $scope.personnel = response.data.personnel;
                }).finally(function () {
                    $scope.loading = false;
                    setHeaderHeights();
                });
            }
        }).filter('trCurrency', function () {
            return function (data) {
                return data.toString().replace('.', ',');
            }
        }).filter('searchFor', function () {
            return function (arr, searchStr) {
                if (!searchStr) {
                    return arr;
                }
                var result = [];
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if (!item.tck_no || item.name.turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        }).filter('color', function () {
            return function (data) {
                if (data.indexOf('-') > -1 ||
                        data.indexOf('Yİ') > -1 ||
                        data.indexOf('Gİ') > -1 ||
                        data.indexOf('Hİ') > -1 ||
                        data.indexOf('Üİ') > -1 ||
                        data.indexOf('R') > -1
                ) {
                    return 'red';
                }
                else {
                    return 'green';
                }
            }
        });

        function cb(start, end) {
            $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
        }
        cb(moment().startOf('month'), moment());

        $('#reportrange').daterangepicker({
            locale: {
                format: 'DD.MM.YYYY',
                applyLabel: 'Tamam',
                cancelLabel: 'İptal',
                customRangeLabel: 'Tarih Seç'
            },
            ranges: {
                'Bugün': [moment(), moment()],
                'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Son 1 Hafta': [moment().subtract(6, 'days'), moment()],
                'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
                'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            startDate: moment().startOf('month'),
            endDate: moment(),
            maxDate: '{{$today}}'
        }, cb);

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            $('input[name="start-date"]').val(picker.startDate.format('YYYY-MM-DD'));
            $('input[name="end-date"]').val(picker.endDate.format('YYYY-MM-DD'));
            angular.element('#angPuantaj').scope().getOvertimes();
        });


        $(document).ready(function () {
            angular.element('#angPuantaj').scope().getOvertimes();
            setHeaderHeights();
        });

        $(document).ready(function () {
            $('[data-toggle=tooltip]').hover(function () {
                // on mouseenter
                $(this).tooltip('show');
            }, function () {
                // on mouseleave
                $(this).tooltip('hide');
            });
        });


    </script>
@stop

@section('content')
    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
        <div class="form-group">
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-9">

                    <table class="table-responsive table-extra-condensed">
                        <tbody>
                        <tr>
                            @foreach(\App\Overtime::all() as $ot)
                                <?php
                                $words = preg_split("/\\s+/", $ot->name);
                                $acronym = "";

                                foreach ($words as $w) {
                                    $acronym .= TurkishChar::tr_up(mb_substr($w, 0, 1, 'UTF-8'));
                                }
                                ?>

                                <td>
                                    <small>{!! "<strong>$acronym</strong>: $ot->name"!!}</small>
                                </td>

                            @endforeach
                        </tr>
                        </tbody>
                    </table>

                </div>
                @if($can_filter)
                    <div class="col-xs-12 col-sm-4 col-md-2" style="min-width: 260px">
                        <div id="reportrange"
                             style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span class="text-center"></span>
                        </div>
                    </div>
                @endif
                <input type="hidden" name="start-date" ng-model="startDate">
                <input type="hidden" name="end-date" ng-model="endDate">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-primary">
                    <div class="box-body">
                        <!-- Loading (remove the following to stop the loading)-->
                        <div class="overlay" ng-show="loading">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->


                        <div ng-hide="loading">
                            <div class="row">

                                <div class="col-md-12" style="overflow: auto">
                                    <table class="table table-responsive table-extra-condensed dark-bordered table-striped">
                                        <thead>
                                        <tr style="font-size: smaller">

                                            <th class="puantaj" id="searchField">
                                                <div class="input-group">
                                                    <input type="text" style="width: 100%"
                                                           name="personnel-search" ng-model="name"
                                                           value=""
                                                           placeholder="Personel Ara"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                                </div>
                                            </th>

                                            <th ng-repeat="day in days track by $index" class="text-center rotate"
                                                ng-class="weekends[$index] == 1 && 'garden-orange'"><% day %>
                                            </th>
                                            <th class="text-right" style="min-width: 58px">Pntj Top.</th>
                                            <th class="text-right" id="wageField" style="min-width: 65px">Ücret
                                                Top.
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="main in data.main | searchFor:name track by $index">
                                            <td class="puantaj"><% main.name %></td>
                                            <td ng-repeat="type in main.puantaj track by $index"
                                                class="text-center"
                                                style="font-size: 11px"
                                                ng-class="weekends[$index] == 1 && 'garden-orange'"
                                            ><%
                                                type |
                                                uppercase
                                                %>
                                            </td>
                                            <td class="text-right"><% main.total %></td>
                                            <td class="text-right"><% main.total %></td>
                                        </tr>
                                        <tr ng-repeat="person in personnel | searchFor:name track by $index">

                                            <td class="puantaj"
                                                ng-style="(!person.tck_no && {'background-color' : '#00a9ff',
                                         'font-weight':'900',
                                         'font-size' : '14px'}) || (person.tck_no && {'padding-left': '20px', 'font-size':'13px'})"
                                            >
                                                <span ng-if="person.tck_no && person.iban" data-toggle="tooltip"
                                                      data-original-title="TCK NO:<% person.tck_no %>
                                                      IBAN:<% person.iban %>"
                                                      data-placement="right"><% person.name %></span>
                                                <span ng-if="person.tck_no && !person.iban" data-toggle="tooltip"
                                                      data-original-title="TCK NO:<% person.tck_no %>"
                                                      data-placement="right"><% person.name %></span>
                                                <span ng-if="!person.tck_no"><% person.name %></span></td>
                                            <td ng-repeat="type in person.type track by $index"
                                                class="text-center"
                                                style="font-size: 11px"
                                                ng-class="weekends[$index] == 1 && 'garden-orange'"
                                                ng-style="(!person.tck_no && {'background-color' : '#00a9ff'})
                                                || (person.tck_no && {'color':(type|color)})"><%
                                                type |
                                                uppercase
                                                %>
                                            </td>
                                            <td class="text-right"
                                                ng-style="!person.tck_no && {'background-color' : '#00a9ff',
                                         'font-weight':'900',
                                         'font-size' : 'small'}"><% person.puantaj | trCurrency %>
                                            </td>
                                            <td class="text-right"
                                                ng-style="!person.tck_no && {'background-color' : '#00a9ff',
                                         'font-weight':'900',
                                         'font-size' : 'small'}"><% person.wage %>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->

                </div>

            </div>
        </div>


    </div>


@stop