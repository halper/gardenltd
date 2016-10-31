<?php

$user = Auth::user();

$addr = explode("/", $_SERVER['REQUEST_URI']);
$slug = $addr[sizeof($addr) - 1];
$module = $modules->whereSlug($slug)->first();

$post_permission = \App\Library\PermissionHelper::checkUserPostPermissionOnModule($user, $module);


?>

@extends('tekil.layout')

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script>
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
            $scope.date = moment().format('DD.MM.YYYY');
            $scope.today = moment().format('DD.MM.YYYY');
            $scope.expItems = [];
            $scope.expItemSelected = '';
            $scope.payments = [];
            $scope.paymentSelect = '';
            $scope.explanation = '';
            $scope.showPayment = false;
            $scope.amount = '';
            $scope.total = 0;
            $scope.wholeTotal = 0;
            $scope.type = '{{$type}}';
            $scope.eid = '';
            $scope.months = [];
            $scope.monthsTotal = [];
            $scope.expDetails = [];
            $scope.dailyTable = true;
            $scope.showDailyTable = function (myBool) {
                $scope.dailyTable = myBool;
            };
            $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-monthly-exp" !!}", {
                type: $scope.type
            }).then(function (response) {
                $scope.months = response.data.months;
                $scope.monthsTotal = response.data.month_total;
                $scope.expDetails = response.data.items;
            });

            $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-general-exp-items" !!}", {
                type: $scope.type
            }).then(function (response) {
                $scope.expItems = response.data;
            });

            $scope.getPayments = function () {
                $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-general-exp" !!}", {
                            type: $scope.type
                        }
                ).then(function (response) {
                    $scope.payments = response.data;
                    var tot = 0;
                    angular.forEach($scope.payments, function (item) {
                        tot += parseFloat(item.total);
                    });
                    $scope.wholeTotal = tot;
                });
            };
            $scope.getPayments();

            $scope.addPayment = function () {
                if (!$scope.date || !$scope.paymentSelect || !$scope.amount || !$scope.expItemSelected) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: Tarih, KDV, gider kalemi, fiyat!'

                }
                else {
                    $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/add-general-exp" !!}", {
                        exp_date: $scope.date,
                        eid: $scope.expItemSelected.id,
                        type: $scope.type,
                        amount: $scope.amount,
                        kdv: $scope.paymentSelect,
                        explanation: $scope.explanation
                    }).then(function (response) {
                        $scope.amount = '';
                        $scope.explanation = '';
                        $scope.paymentSelect = '';
                        $scope.expItemSelected = '';
                        $scope.eid = '';
                        $scope.date = $scope.today;
                        $scope.getPayments();
                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/del-general-exp" !!}", {
                    id: item.id
                }).then(function () {
                    $scope.getPayments();
                    $scope.date = item.date;
                    $scope.paymentSelect = item.kdv;
                    $scope.expItemSelected = item.expItem;
                    $scope.amount = item.amount;
                });

            };
            $scope.name = '';
        }).filter('numberFormatter', function () {
            return function (data) {
                return $.number(data, 2, ',', '.');
            }
        }).filter('searchFor', function () {
            return function (arr, searchStr, scope) {
                if (!searchStr) {
                    scope.total = scope.wholeTotal;
                    return arr;
                }
                var result = [];
                scope.total = 0;
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if ((item.date + ' ' + item.type + ' ' + item.amount).turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                        scope.total += parseFloat(item.total);
                    }
                });
                return result;
            };
        }).filter('filTotal', function () {
            return function (data, key) {
                if (angular.isUndefined(data))
                    return 0;
                var subTotal = 0;

                angular.forEach(data, function (v, k) {
                    if (angular.isUndefined(key))
                        subTotal += parseFloat(v);
                    else
                        subTotal += parseFloat(v[key]);
                });

                return subTotal;
            }
        });
    </script>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$title}}
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


                    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
                        @if($post_permission)
                            <p class="text-danger alert-danger" ng-show="subError"><%subError%></p>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <strong>Tarih</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>KDV</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Gider Kalemi</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Fiyat</strong>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="input-group input-append date dateRangePicker" id="paymentDate">
                                                <input type="text" class="form-control" name="payment_date"
                                                       ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <select class="form-control"
                                                    ng-model="paymentSelect">
                                                <option value="" selected disabled>KDV Miktarı Seçiniz</option>
                                                <option value="0">%0</option>
                                                <option value="1">%1</option>
                                                <option value="8">%8</option>
                                                <option value="18">%18</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <select class="form-control"
                                                    ng-options="expItem as expItem.name for expItem in expItems track by expItem.id"
                                                    ng-model="expItemSelected"
                                                    ng-change="drChange()">
                                                <option value="" selected disabled>Gider Kalemi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control number" name="price"
                                                   ng-model="amount"
                                                   placeholder="Fiyat"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-offset-1 col-md-1">
                                            <strong>Açıklama: </strong>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="explanation"
                                                   ng-model="explanation"
                                                   placeholder="Açıklama"/>
                                        </div>

                                    </div>
                                    </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-offset-2 col-md-8">
                                            <button type="button" class="btn btn-primary btn-flat btn-block"
                                                    ng-click="addPayment()">Güncelle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        @endif


                        <div class="row">
                            <div class="col-md-4">
                                <a href="#!" ng-click="showDailyTable(true)">Günlük Görünüm</a> | <a
                                        href="#!" ng-click="showDailyTable(false)">Aylık Görünüm</a>
                            </div>
                        </div>

                        <div class="form-group" ng-hide="!dailyTable">
                            <div class="row">
                                <div class="col-xs-12 col-sm-4 col-md-6">
                                    <h4>Günlük Görünüm</h4>
                                </div>
                                <div class="col-xs-12 col-sm-8 col-md-6 pull-right">
                                    <div class="input-group" style="padding-top: 6px;">
                                        <input type="text" style="width: 100%"
                                               name="search" ng-model="name"
                                               value=""
                                               placeholder="Tarih, gider kalemi veya fiyat giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" ng-hide="!dailyTable">
                            <div class="col-sm-12">
                                <table class="table table-responsive table-extra-condensed dark-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center">S.N</th>
                                        <th class="text-center">Tarih</th>
                                        <th class="text-center">Gider Kalemi</th>
                                        <th class="text-center">Açıklama</th>
                                        <th class="text-center">Fiyat</th>
                                        <th class="text-center">KDV</th>
                                        <th class="text-center">Toplam</th>
                                        @if($post_permission)
                                            <th class="text-center">Sil</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="payment in payments | searchFor:name:this track by $index">
                                        <td class="text-center"><%$index+1%></td>
                                        <td class="text-center"><%payment.date%></td>
                                        <td class="text-center"><%payment.type%></td>
                                        <td class="text-center"><%payment.explanation%></td>
                                        <td class="text-center"><%payment.amount | numberFormatter%> TL</td>
                                        <td class="text-center">%<%payment.kdv%></td>
                                        <td class="text-right"><%payment.total|numberFormatter%> TL</td>
                                        @if($post_permission)
                                            <td class="text-center"><a href="#!" ng-click="remove_field(payment)"><i
                                                            class="fa fa-close"></i></a></td>
                                        @endif
                                    </tr>
                                    <tr class="bg-warning">
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right"><strong>GENEL TOPLAM</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right"><strong><%total | numberFormatter%>TL</strong>
                                        </td>
                                        @if($post_permission)
                                            <td></td>
                                        @endif
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>

                        <div class="row" ng-hide="dailyTable">
                            <div class="col-sm-12">
                                <h4>Aylık Görünüm</h4>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-responsive table-extra-condensed dark-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Gider Adı</th>
                                        <th class="text-center" ng-repeat="month in months track by $index"><% month
                                            %>
                                        </th>
                                        <th class="text-right" style="min-width: 58px">Gider Top.</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="expDetail in expDetails | searchFor:name:this track by $index">
                                        <td class="text-center"><%expDetail.name%></td>
                                        <td ng-repeat="total in expDetail.total track by $index" class="text-right">
                                            <%total | numberFormatter%> TL
                                        </td>
                                        <td class="text-right"><% expDetail.item_total | numberFormatter %> TL</td>
                                    </tr>
                                    <tr class="bg-warning">
                                        <td style="text-align: right"><strong>GENEL TOPLAM</strong></td>
                                        <td ng-repeat="tot in monthsTotal track by $index" style="text-align: right">
                                            <%tot | numberFormatter %> TL
                                        </td>
                                        <td style="text-align: right"><strong><%monthsTotal | filTotal |
                                                numberFormatter%>TL</strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>



@endsection