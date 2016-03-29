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
            $scope.subError = '';
            $scope.wholeTotal = 0;
            $scope.total = 0;

            $scope.expenditures = [];
            $scope.detail = '';

            $scope.getPayments = function () {
                $http.get("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-labor-exp" !!}"
                ).then(function (response) {
                    $scope.expenditures = response.data;
                    var tot = 0;
                    angular.forEach($scope.expenditures, function (item) {
                        tot += parseFloat(item.amount);
                    });
                    $scope.wholeTotal = tot;
                });
            };
            $scope.getPayments();

            $scope.addPayment = function () {
                if (!$scope.date || !$scope.amount || !$scope.detail) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: Tarih, harcama, açıklama!'

                }
                else {
                    $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/add-labor-exp" !!}", {
                        lab_date: $scope.date,
                        amount: $scope.amount,
                        detail: $scope.detail
                    }).then(function () {
                        $scope.amount = '';
                        $scope.detail = '';
                        $scope.date = $scope.today;
                        $scope.getPayments();
                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/del-lab-exp" !!}", {
                    id: item.id
                }).then(function () {
                    $scope.getPayments();
                    $scope.date = item.date;
                    $scope.detail = item.detail;
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
                    if ((item.date + ' ' + item.detail + ' ' + item.amount).turkishToLower().indexOf(searchStr) !== -1) {
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
                    <h3 class="box-title">Şantiye İşçilik
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
                        <p class="text-danger alert-danger" ng-show="subError"><%subError%></p>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>Tarih</strong>
                                </div>
                                <div class="col-md-8">
                                    <strong>Açıklama</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Harcama</strong>
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


                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="detail"
                                               ng-model="detail" placeholder="Harcama açıklaması"/>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control number" name="amount" ng-model="amount"
                                               placeholder="Harcama"/>
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


                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12 col-sm-8 col-md-6 pull-right">
                                    <div class="input-group" style="padding-top: 6px;">
                                        <input type="text" style="width: 100%"
                                               name="search" ng-model="name"
                                               value=""
                                               placeholder="Tarih, açıklama veya harcama giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-responsive table-extra-condensed dark-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center">S.N</th>
                                        <th class="text-center">Tarih</th>
                                        <th class="text-left">Açıklama</th>
                                        <th class="text-right">Harcama</th>
                                        <th class="text-center">Sil</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="expenditure in expenditures | searchFor:name:this track by $index">
                                        <td class="text-center"><%$index+1%></td>
                                        <td class="text-center"><%expenditure.lab_date%></td>
                                        <td class="text-left"><%expenditure.detail%></td>
                                        <td class="text-right"><%expenditure.amount | numberFormatter%>TL</td>
                                        <td class="text-center"><a href="#!" ng-click="remove_field(expenditure)"><i
                                                        class="fa fa-close"></i></a>
                                    </tr>
                                    <tr class="bg-warning">
                                        <td></td>
                                        <td></td>
                                        <td class="text-center"><strong>GENEL TOPLAM</strong></td>
                                        <td class="text-right"><strong><%total | numberFormatter%>TL</strong>
                                        </td>
                                        <td></td>
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