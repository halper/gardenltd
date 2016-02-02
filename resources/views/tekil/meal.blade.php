<?php

use App\Library\CarbonHelper;use Carbon\Carbon;
$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
$mealcost = is_null($site->mealcost()->first()) ? null : $site->mealcost()->orderBy('since', 'DESC')->first();

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
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/meals", {
                    'start_date': $scope.startDate,
                    'end_date': $scope.endDate
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
                    if (!item.tck_no || item.name.turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        }).controller('MealcostController', function ($scope, $http) {
            $scope.mealData = null;
            $scope.mealOperation = '';
            $scope.breakfast = '';
            $scope.lunch = '';
            $scope.supper = '';
            $scope.since = '';
            $http.get("<?=URL::to('/');?>/tekil/{{$site->slug}}/mealcosts"
            ).then(function (response) {
                $scope.mealData = response.data;
            });
            $scope.removeMealData = function (meal) {
                $scope.mealOperation = '';
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/delete-mealcosts", {
                    'meal': meal
                }).then(function () {
                    var index = $scope.mealData.indexOf(meal);
                    $scope.mealData.splice(index, 1);
                    $scope.mealOperation = 'Silinme işlemi başarılı';
                });
            };
            $scope.addMealcost = function () {
                $scope.mealOperation = '';
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/insert-mealcost", {
                    'breakfast': $scope.breakfast,
                    'lunch': $scope.lunch,
                    'supper': $scope.supper,
                    'since': $scope.since
                }).then(function () {
                    $scope.mealData.push({
                        'breakfast': $scope.breakfast,
                        'lunch': $scope.lunch,
                        'supper': $scope.supper,
                        'since': $scope.since
                    });
                    $scope.breakfast = '';
                    $scope.lunch = '';
                    $scope.supper = '';
                    $scope.since = '';
                    $scope.mealOperation = 'Ekleme işlemi başarılı';
                });

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

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#meal-table" data-toggle="tab">Tablo</a></li>
                    <li><a href="#meal-cost" data-toggle="tab">Ücretler</a></li>
                    <li class="pull-right">
                        <div style="min-width: 40px">
                            <div id="reportrange" class="pull-right"
                                 style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span class="text-center"></span> <b style="margin-left: 24px;"
                                                                     class="caret"></b>
                            </div>
                            <input type="hidden" name="start-date" ng-model="startDate">
                            <input type="hidden" name="end-date" ng-model="endDate">
                        </div>
                    </li>
                </ul>
                <div class="tab-content" ng-app="puantajApp">

                    <div class="tab-pane active" id="meal-table">
                        <div ng-controller="PuantajController" id="angPuantaj">
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
                                                <th class="text-right" style="min-width: 58px">K/Ö/A T.</th>
                                                <th class="text-right" id="wageField" style="min-width: 65px">Ücret
                                                    T.
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr ng-repeat="person in personnel | searchFor:name track by $index">

                                                <td class="puantaj"
                                                    ng-style="(!person.tck_no && {'background-color' : '#00a9ff',
                                         'font-weight':'900',
                                         'font-size' : '14px'}) || (person.tck_no && {'padding-left': '15px', 'font-size':'13px'})"
                                                ><span ng-if="person.tck_no" data-toggle="tooltip"
                                                       data-original-title="TCK NO:<% person.tck_no %>"
                                                       data-placement="right"><% person.name %></span>
                                                    <span ng-if="!person.tck_no"><% person.name %></span>
                                                </td>
                                                <td ng-repeat="type in person.type track by $index"
                                                    class="text-center"
                                                    style="font-size: 11px"
                                                    ng-class="weekends[$index] == 1 && 'garden-orange'"
                                                    ng-style="!person.tck_no && {'background-color' : '#00a9ff'}"><%
                                                    type
                                                    %>
                                                </td>
                                                <td class="text-right"
                                                    ng-style="!person.tck_no && {'background-color' : '#00a9ff',
                                         'font-weight':'900',
                                         'font-size' : 'small'}"><% person.meal_total %>
                                                </td>
                                                <td class="text-right"
                                                    ng-style="!person.tck_no && {'background-color' : '#00a9ff',
                                         'font-weight':'900',
                                         'font-size' : 'small'}"><% person.cost %>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="meal-cost" ng-controller="MealcostController">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-sm-6">


                                        <form class="form" role="form" ng-submit="addMealcost()">
                                            <div class="form-group {{ $errors->has('since') ? 'has-error' : '' }}">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        {!! Form::label('since', 'Tarih İtibariyle: ', ['class' => 'control-label']) !!}
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="input-group input-append date dateRangePicker">
                                                            {!! Form::text('since', empty($mealcost) ? null : \App\Library\CarbonHelper::getTurkishDate($mealcost->since),
                                                            ['class' => 'form-control', 'placeholder' => 'Ödeme tarihini seçiniz', 'ng-model' => 'since']) !!}
                                                            <span class="input-group-addon add-on"><span
                                                                        class="glyphicon glyphicon-calendar"></span></span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row">
                                                <div class="col-sm-3">
                                                    {!! Form::label('breakfast', 'Ücretler (TL): ', ['class' => 'control-label']) !!}
                                                </div>
                                                <div class="col-sm-2">
                                                    {!! Form::text('breakfast', null, ['class' => 'form-control number',
                                                    'placeholder' => 'Kahvaltı', 'ng-model' => 'breakfast']) !!}
                                                </div>
                                                <div class="col-sm-2 col-sm-offset-1">
                                                    {!! Form::text('lunch', null, ['class' => 'form-control number',
                                                    'placeholder' => 'Öğle', 'ng-model' => 'lunch']) !!}
                                                </div>
                                                <div class="col-sm-2 col-sm-offset-1">
                                                    {!! Form::text('supper', null, ['class' => 'form-control number',
                                                    'placeholder' => 'Akşam', 'ng-model' => 'supper']) !!}
                                                </div>
                                            </div>

                                            <br>

                                            <div class="form-group">

                                                <div class="col-sm-8 col-sm-offset-3">
                                                    <button type="submit"
                                                            class="btn btn-flat btn-primary btn-block">
                                                        Ücretleri
                                                        Kaydet
                                                    </button>
                                                </div>

                                            </div>
                                        </form>


                                    </div>
                                    <div class="col-sm-6">
                                        <table class="table">
                                            <tr>
                                                <th>Tarih itibariyle
                                                </th>
                                                <th>Kahvaltı
                                                </th>
                                                <th>Öğle
                                                </th>
                                                <th>Akşam
                                                </th>
                                            </tr>
                                            <tr ng-repeat="meal in mealData | orderBy: meal.since:false">
                                                <td><a href="#" ng-click="removeMealData(meal)"><i
                                                                class="fa fa-close"></i></a><%meal.since%>
                                                </td>
                                                <td><%meal.breakfast | trCurrency%>
                                                </td>
                                                <td><%meal.lunch | trCurrency%>
                                                </td>
                                                <td><%meal.supper | trCurrency%>
                                                </td>
                                            </tr>
                                        </table>
                                        <p ng-hide="!mealOperation" class="text-success"><%mealOperation%></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop