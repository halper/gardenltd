<?php
use App\Library\CarbonHelper;
use Carbon\Carbon;

$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
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
            $scope.siteId = '{{$site->id}}';
            $scope.name = '';
            $scope.loading = false;

            $scope.getOvertimes = function () {
                if (!$('input[name="end-date"]').val()) {
                    $scope.startDate = moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD');
                    $scope.endDate = moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD');
                }
                else {
                    $scope.startDate = $('input[name="start-date"]').val();
                    $scope.endDate = $('input[name="end-date"]').val();
                }
                $scope.loading = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/overtimes", {
                    'start_date': $scope.startDate,
                    'end_date': $scope.endDate,
                    'sid': $scope.siteId
                }).then(function (response) {
                    $scope.data = response.data;
                    $scope.days = response.data.days;
                    $scope.weekends = response.data.weekends;
                    $scope.personnel = response.data.personnel;
                }).finally(function () {
                    $scope.loading = false;
                });
            }
        }).filter('trCurrency', function () {
            return function (data) {
                return data.toString().replace('.', ',');
            }
        });

        function cb(start, end) {
            $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
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
            angular.element('#angPuantaj').scope().getOvertimes();
        });


        $(document).ready(function () {
            angular.element('#angPuantaj').scope().getOvertimes();
        });
    </script>
@stop

@section('content')
    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
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
                        <h3 class="box-title">Puantajlar Tablosu</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-responsive table-condensed table-bordered" ng-hide="loading">
                            <thead>
                            <tr>
                                <th style="max-width: 60px">
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                               name="personnel-search" ng-model="name"
                                               value=""
                                               placeholder="Aramak istediğiniz personeli giriniz"/>
                                        <span class="input-group-addon add-on"><i class="fa fa-search"></i></span>

                                    </div>
                                </th>
                                <th ng-repeat="day in days track by $index" class="text-center"
                                    ng-class="weekends[$index] == 1 && 'bg-green'"><% day %></th>
                                <th class="text-right">Pntj Top.</th>
                                <th class="text-right">Ücret Top.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="person in personnel | filter:name:false">
                                <td ng-if="!person.tck_no"><strong><% person.name %></strong></td>
                                <td ng-if="person.tck_no" ng-style="person.tck_no && {'padding-left': '15px'}"><% person.name %> (<% person.tck_no %>)</td>
                                <td ng-repeat="type in person.type track by $index" class="text-center"
                                    ng-class="weekends[$index] == 1 && 'bg-green'"><% type | uppercase
                                    %>
                                </td>
                                <td  class="text-right"><% person.puantaj | trCurrency %></td>
                                <td class="text-right"><% person.wage | trCurrency %></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                    <!-- Loading (remove the following to stop the loading)-->
                    <div class="overlay" ng-show="loading">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->
                </div>


            </div>
        </div>
    </div>
@stop