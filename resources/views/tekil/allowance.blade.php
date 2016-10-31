<?php

use Carbon\Carbon;
$today = \App\Library\CarbonHelper::getTurkishDate(Carbon::now()->toDateString());

$user = Auth::user();

$addr = explode("/", $_SERVER['REQUEST_URI']);
$slug = $addr[sizeof($addr) - 1];
$module = $modules->whereSlug($slug)->first();

$post_permission = \App\Library\PermissionHelper::checkUserPostPermissionOnModule($user, $module);
?>

@extends('tekil.layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@endsection

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>

    <script>
        $('#dateRangePicker').datepicker({
            autoclose: true,
            language: 'tr'
        });
        $("#dateRangePicker > input").val("{{$today}}");
        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.data = null;
            $scope.total = null;
            $scope.left = null;
            $scope.date = '{{$today}}';
            $scope.staff = [];
            $scope.loading = false;

            $scope.getOvertimes = function () {

                $scope.loading = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-hakedis"
                ).then(function (response) {
                    $scope.total = response.data.total;
                    $scope.left = response.data.left;
                    $scope.staff = response.data.hakedis;
                }).finally(function () {
                    $scope.loading = false;
                });
            };

            $scope.addExpense = function () {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/add-hakedis", {
                    'allowance_date': $scope.date,
                    'detail': $scope.detail,
                    'no': $scope.no,
                    'amount': $scope.amount
                }).then(function (response) {

                    $scope.staff.push({
                        'date': $scope.date,
                        'detail': $scope.detail,
                        'no': $scope.no,
                        'amount': $scope.amount,
                        'id': response.data.id
                    });
                    $scope.total += parseFloat($scope.amount);
                    $scope.left -= parseFloat($scope.amount);
                    $scope.date = '{{$today}}';
                    $scope.detail = '';
                    $scope.no = '';
                    $scope.amount = '';

                });

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-hakedis", {
                    'id': item.id
                }).then(function () {
                    var index = $scope.staff.indexOf(item);
                    var amount = parseFloat(item.amount.replace('.', '').replace(',', '.'));
                    $scope.date = item.date;
                    $scope.detail = item.detail;
                    $scope.no = item.no;
                    $scope.amount = item.amount;
                    $scope.total -= amount;
                    $scope.left += amount;
                    $scope.staff.splice(index, 1);
                });

            }
        }).filter('numberFormatter', function () {
            return function (data) {
                return $.number(data, 2, ',', '.');
            }
        });

        $(document).ready(function () {
            angular.element('#angPuantaj').scope().getOvertimes();
        });


    </script>
@stop

@section('content')
    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">

        <div class="row">
            <div class="col-md-12">
                <h4>Yeni Hakediş Ekle</h4>

                @if($post_permission)
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control"
                                   name="no" ng-model="no"
                                   value=""
                                   placeholder="Hakediş No"/>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-append date " id="dateRangePicker">
                                <input type="text" class="form-control" name="exp_date" ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control number"
                                   name="amount" ng-model="amount"
                                   value=""
                                   placeholder="Hakediş Bedeli"/>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control"
                                   name="detail" ng-model="detail"
                                   value=""
                                   placeholder="Hakediş açıklaması"/>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="button" ng-click="addExpense()"
                                        class="btn btn-primary btn-flat btn-block btn-sm">Kaydet
                                </button>
                            </div>
                        </div>

                    </div>
                @endif
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
                                    <table class="table table-responsive table-extra-condensed dark-bordered">
                                        <thead>
                                        <tr style="font-size: smaller">
                                            <th class="text-center">NO</th>
                                            <th class="text-center">TARİH</th>
                                            <th>AÇIKLAMA</th>
                                            <th class="text-right">TUTAR</th>
                                            @if($post_permission)
                                                <th class="text-center">SİL</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="st in staff track by $index">

                                            <td class="text-center"><% st.no %></td>
                                            <td class="text-center"><% st.date%></td>
                                            <td><%st.detail%></td>
                                            <td class="text-right"><%st.amount | numberFormatter%> TL</td>
                                            @if($post_permission)
                                                <td class="text-center"><a href="#" ng-click="remove_field(st)"><i
                                                                class="fa fa-close"></i></a></td>
                                            @endif
                                        </tr>
                                        <tr class="bg-warning">
                                            <td></td>
                                            <td></td>
                                            <td class="text-center"><strong>TOPLAM: </strong></td>
                                            <td class="text-right"><%total | numberFormatter%> TL</td>
                                        </tr>
                                        <tr class="bg-info">
                                            <td></td>
                                            <td></td>
                                            <td class="text-center"><strong>KALAN: </strong></td>
                                            <td class="text-right"><%left | numberFormatter%> TL</td>
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
@endsection