<?php
use App\Library\CarbonHelper;
use Carbon\Carbon;

$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
?>
@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/daterangepicker.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/daterangepicker.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js" type="text/javascript"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script>

        function cb(start, end) {
            $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
        }
        cb(moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month'));

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
            startDate: moment().subtract(1, 'month').startOf('month'),
            endDate: moment().subtract(1, 'month').endOf('month'),
            maxDate: '{{$today}}'
        }, cb);

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            $('input[name="start-date"]').val(picker.startDate.format('YYYY-MM-DD'));
            $('input[name="end-date"]').val(picker.endDate.format('YYYY-MM-DD'));
        });

        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.data = null;
            $scope.days = [];
            $scope.personnel = [];
            $scope.startDate = '';
            $scope.endDate = '';
            $scope.siteId = '{{$site->id}}';
            $scope.name = '';

            $scope.getOvertimes = function () {
                if (!$('input[name="end-date"]').val()) {
                    $scope.startDate = moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD');
                    $scope.endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
                }
                else {
                    $scope.startDate = $('input[name="start-date"]').val();
                    $scope.endDate = $('input[name="end-date"]').val();
                }
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/overtimes", {
                    'start_date': $scope.startDate,
                    'end_date': $scope.endDate,
                    'sid': $scope.siteId
                }).success(function (data, status, headers, config) {
                    $scope.data = data;
                    $scope.days = data.days;
                    $scope.personnel = data.personnel;
                });
            }
        }).filter('pntjTotal', function () {
            return function (data) {
                var sum = 0.0;
                var i = 0;
                angular.forEach(data.multiplier, function (v) {
                    if (data.type[i].toLowerCase().match('fm')) {
                        sum = sum + parseFloat(data.overtime[i] * v);
                    }
                    else {
                        sum = sum + parseFloat(v);
                    }
                    i++;
                });
                return sum.toString().replace('.', ',');
            }
        }).filter('wageTotal', function () {
            return function (data) {
                var sum = 0.0;
                var i = 0;
                angular.forEach(data.multiplier, function (v) {
                    if (data.type[i].toLowerCase().match('fm')) {
                        sum = sum + parseFloat(data.overtime[i] * v);
                    }
                    else {
                        sum = sum + parseFloat(v);
                    }
                    i++;
                });
                sum = sum * data.wage;
                return sum.toString().replace('.', ',');
            }
        }).filter('fmCheck', function () {
            return function (data, index) {
                return data.type[index].toLowerCase().match('fm') ? "FM (" + data.overtime[index] + ")" : data.type[index];
            }
        });
    </script>
@stop

@section('content')
    <div ng-app="puantajApp" ng-controller="PuantajController">
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <label>
                        Tarih Aralığı Seçiniz:
                    </label>
                </div>
                <div class="col-md-2">
                    <div id="reportrange" class="pull-right"
                         style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                </div>
                <input type="hidden" name="start-date" ng-model="startDate">
                <input type="hidden" name="end-date" ng-model="endDate">
                <div class="col-md-8">
                    <a href="#" class="btn btn-flat btn-primary" ng-click="getOvertimes()">Filtrele</a>
                </div>
            </div>
        </div>

        <div class="row" >
            <div class="col-sm-12">
                <span class="text-danger" ng-hide="data != null">Başlamak için tarih aralığı seçiniz!</span>
                <table class="table table-responsive table-condensed table-bordered" ng-hide="data == null">
                    <thead>
                    <tr>
                        <th style="max-width: 60px">
                            <div class="input-group">
                            <input type="text" class="form-control"
                                   name="personnel-search" ng-model="name"
                                   value=""
                                   placeholder="Aramak istediğiniz personeli giriniz"/>
                                <span class="input-group-addon add-on"><i class="fa fa-search"></i></span>

                        </div></th>
                        <th ng-repeat="day in days"><% day %></th>
                        <th>Pntj Top.</th>
                        <th>Ücret Top.</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="person in personnel | filter:name">
                        <td><% person.name %> (<% person.tck_no %>)</td>
                        <td ng-repeat="type in person.type track by $index"><% (person | fmCheck:$index) | uppercase
                            %>
                        </td>
                        <td><% person | pntjTotal %></td>
                        <td><% person | wageTotal %></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop