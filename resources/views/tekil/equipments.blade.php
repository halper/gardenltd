<?php

$user = Auth::user();

$addr = explode("/", $_SERVER['REQUEST_URI']);
$slug = $addr[sizeof($addr) - 1];
$module = $modules->whereSlug($slug)->first();

$post_permission = \App\Library\PermissionHelper::checkUserPostPermissionOnModule($user, $module);
if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = '';
}

$tab = empty($tab) ? 1 : $tab;

?>

@extends('tekil.layout')

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>

    <script>
        $('#select_all').change(function () {
            var checkboxes = $('body').find(':checkbox').not($(this));
            console.log(checkboxes);
            if ($(this).is(':checked')) {
                checkboxes.prop('checked', true);
            } else {
                checkboxes.prop('checked', false);
            }
        });


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
            $scope.siteEquipments = [];
            $scope.instruments = [];
            $scope.siteEquipmentSelected = '';
            $scope.firm = '';
            $scope.plate = '';
            $scope.detail = '';
            $scope.fee = '';
            $scope.fuelStatSelect = '';
            $scope.fuel = '';
            $scope.work = '';
            $scope.unit = '';
            $scope.wholeTotal = '';

            $http.get("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-site-equipments" !!}", {}).then(function (response) {
                $scope.siteEquipments = response.data;
            });

            $scope.getInstruments = function () {
                $http.get("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-instruments" !!}", {}
                ).then(function (response) {
                    $scope.instruments = response.data;
                    var tot = 0;
                    angular.forEach($scope.instruments, function (item) {
                        tot += parseFloat(item.total);
                    });
                    $scope.wholeTotal = tot;
                });
            };
            $scope.getInstruments();

            $scope.addInstrument = function () {
                if (!$scope.date || !$scope.siteEquipmentSelected || !$scope.unit || !$scope.work || !$scope.fee) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: Tarih, İş Makinesi, Birim Çalışma, Birim, Ücret!'
                }
                else {
                    $scope.subError = '';
                    $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/add-instrument" !!}", {
                        followup_date: $scope.date,
                        eid: $scope.siteEquipmentSelected.id,
                        firm: $scope.firm,
                        plate: $scope.plate,
                        detail: $scope.detail,
                        fee: $scope.fee,
                        fuel_stat: $scope.fuelStatSelect.id,
                        fuel: $scope.fuel,
                        work: $scope.work,
                        unit: $scope.unit
                    }).
                    then(function () {
                        $scope.plate = '';
                        $scope.detail = '';
                        $scope.fee = '';
                        $scope.firm = '';
                        $scope.fuelStatSelect = '';
                        $scope.siteEquipmentSelected = '';
                        $scope.fuel = '';
                        $scope.work = '';
                        $scope.unit = '';
                        $scope.date = $scope.today;
                        $scope.getInstruments();
                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/del-instrument" !!}", {
                    id: item.id
                }).then(function () {
                    $scope.getInstruments();
                    $scope.date = item.date;
                    $scope.firm = item.firm;
                    $scope.plate = item.plate;
                    $scope.fuelStatSelect = item.fuel_stat;
                    $scope.siteEquipmentSelected = item.eid;
                    $scope.fuel = item.fuel;
                    $scope.work = item.work;
                    $scope.unit = item.unit;
                    $scope.fee = item.fee;
                    $scope.detail = item.detail;
                });

            };
            $scope.name = '';
        }).filter('fuelStatConverter', function () {
            return function (data) {
                return data == 0 ? "Hariç" : "Dahil";
            }
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
                    if ((item.date + ' ' + item.firm + ' ' + item.name + ' ' + item.plate + ' ' + item.detail).turkishToLower().indexOf(searchStr) !== -1) {
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
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li {{$tab == 1 ? 'class=active' : ''}}><a href="#tab_1" data-toggle="tab">İş Makineleri Takip</a>
                    </li>
                    <li {{$tab == 2 ? 'class=active' : ''}}><a href="#tab_2" data-toggle="tab">Şantiye İş Makinesi
                            Düzenle</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content" ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">

                    <div class="tab-pane {{$tab == 1 ? 'active' : ''}}" id="tab_1">
                        @include('tekil._instrument-followup')
                    </div>

                    <div class="tab-pane {{ $tab == 2 ? 'active' : ''}}" id="tab_2">
                        @include('tekil._edit-equipments')
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop