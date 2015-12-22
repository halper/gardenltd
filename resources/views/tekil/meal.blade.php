<?php

use App\Library\CarbonHelper;use Carbon\Carbon;
$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
        $mealcost = is_null($site->mealcost()->first()) ? null : $site->mealcost()->orderBy('since', 'DESC')->first();

?>

@extends('tekil.layout')


@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link href="<?= URL::to('/'); ?>/css/daterangepicker.css" rel="stylesheet"/>

@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
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
        $('.dateRangePicker').datepicker({
            language: 'tr',
            autoclose: true
        });
    </script>
@stop


@section('content')
    {!! Form::open([
    'url' => "/tekil/$site->slug/update-mealcost",
    'method' => 'POST',
    'class' => 'form',
    'id' => 'mealcostUpdateForm',
    'role' => 'form'
    ])!!}

    <div class="form-group {{ $errors->has('since') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('since', 'Tarih İtibariyle: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                <div class="input-group input-append date dateRangePicker">
                    {!! Form::text('since', empty($mealcost) ? null : \App\Library\CarbonHelper::getTurkishDate($mealcost->since), ['class' => 'form-control', 'placeholder' => 'Ödeme tarihini seçiniz']) !!}
                    <span class="input-group-addon add-on"><span
                                class="glyphicon glyphicon-calendar"></span></span>
                </div>

            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('breakfast', 'YEMEK ÜCRETLERİ: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-2">
                {!! Form::text('breakfast', empty($mealcost) ? null :str_replace('.', ',', $mealcost->breakfast), ['class' => 'form-control number', 'placeholder' => 'Kahvaltı Ücreti']) !!}
            </div>
            <div class="col-sm-2 col-sm-offset-1">
                {!! Form::text('lunch', empty($mealcost) ? null :str_replace('.', ',', $mealcost->lunch), ['class' => 'form-control number', 'placeholder' => 'Öğle Yemeği Ücreti']) !!}
            </div>
            <div class="col-sm-2 col-sm-offset-1">
                {!! Form::text('supper', empty($mealcost) ? null :str_replace('.', ',', $mealcost->supper), ['class' => 'form-control number', 'placeholder' => 'Akşam Yemeği Ücreti']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-md-offset-4">
            <div class="form-group">
                <button type="submit" class="btn btn-flat btn-primary btn-block">Ücretleri
                    Kaydet
                </button>
            </div>
        </div>
    </div>

    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
        <div class="form-group">
            <div class="row">
                <div class="col-xs-6 col-sm-4 col-md-2">
                    <label>
                        Tarih Aralığı Seçiniz:
                    </label>
                </div>
                <div class="col-xs-6 col-sm-8 col-md-3">
                    <div id="reportrange" class="pull-right"
                         style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span class="text-center"></span> <b style="margin-left: 24px;" class="caret"></b>
                    </div>
                </div>
                <input type="hidden" name="start-date" ng-model="startDate">
                <input type="hidden" name="end-date" ng-model="endDate">
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Yemek Tablosu</h3>
                    </div>
                    <div class="box-body">
                        <!-- Loading (remove the following to stop the loading)-->
                        <div class="overlay" ng-show="loading">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->


                        <div ng-hide="loading">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-4 col-md-3">
                                            <table class="table table-responsive table-condensed table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="puantaj" id="searchField">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control"
                                                                   name="personnel-search" ng-model="name"
                                                                   value=""
                                                                   placeholder="Personel Ara"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                                        </div>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="person in personnel | searchFor:name track by $index">
                                                    <td class="puantaj" ng-if="!person.tck_no"
                                                        ng-style="!person.tck_no && {'background-color' : '#f0f0f0',
                                         'font-weight':'900',
                                         'font-size' : 'medium'}"><strong><%
                                                            person.name %></strong></td>
                                                    <td class="puantaj" ng-if="person.tck_no"
                                                        ng-style="person.tck_no && {'padding-left': '15px'}"><%
                                                        person.name %>
                                                        (<%
                                                        person.tck_no %>)
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-xs-6 col-sm-8 col-md-9" style="overflow: auto; padding-left: 0">
                                            <table class="table table-responsive table-condensed table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th ng-repeat="day in days track by $index" class="text-center"
                                                        ng-class="weekends[$index] == 1 && 'bg-light-green'"><% day %>
                                                    </th>
                                                    <th class="text-right" style="min-width: 68px">Öğünler (K/Ö/A)</th>
                                                    <th class="text-right" id="wageField" style="min-width: 75px">Ücret
                                                        Top.
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="person in personnel | searchFor:name track by $index">
                                                    <td ng-repeat="type in person.type track by $index"
                                                        class="text-center"
                                                        ng-class="weekends[$index] == 1 && 'bg-light-green'"
                                                        ng-style="!person.tck_no && {'background-color' : '#f0f0f0'}">
                                                        <% type %>
                                                    </td>
                                                    <td class="text-right"
                                                        ng-style="!person.tck_no && {'background-color' : '#f0f0f0',
                                         'font-weight':'900',
                                         'font-size' : 'medium'}"><% person.meal_total %>
                                                    </td>
                                                    <td class="text-right"
                                                        ng-style="!person.tck_no && {'background-color' : '#f0f0f0',
                                         'font-weight':'900',
                                         'font-size' : 'medium'}"><% person.cost | trCurrency %>
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
        </div>
    </div>

    {!! Form::close() !!}
@stop