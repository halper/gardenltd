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
        $('#updateButton').on('click', function (e) {
            e.preventDefault();
            $.post("<?=URL::to('/');?>{!! "/tekil/$site->slug/add-employee" !!}", $("#employee-form").serialize()
            ).done(function () {
                $('.success-message').html('<p class="alert-success">Kayıt başarılı!</p>');
                $('p.alert-success').not('.alert-important').delay(7500).slideUp(300);
                angular.element('#angPuantaj').scope().getSalaries();
            });
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
            $scope.total = 0;
            $scope.wholeTotal = 0;
            $scope.dates = '';
            $scope.salaries = [];
            $scope.monthsTotal = [];

            $scope.getSalaries = function () {
                $http.get("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-salaries" !!}"
                ).then(function (response) {
                    $scope.salaries = response.data.salaries;
                    $scope.dates = response.data.dates;
                    $scope.monthsTotal = response.data.monthly_tot;
                });
            };
            $scope.getSalaries();

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
                    if ((item.personnel).turkishToLower().indexOf(searchStr) !== -1) {
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
                console.log(data + ' ' + key);

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
                    <h3 class="box-title">Şantiye Personel Maaş
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
                    @if($post_permission)
                        <form id="employee-form">
                            <span class="success-message"></span>

                            <div class="row">
                                @foreach(\App\Personnel::sitePersonnel()->get() as $personnel)
                                    <?php
                                    $checked = !($personnel->site()->where('site_id', '=', $site->id)->get()->isEmpty());
                                    ?>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="employee[]"
                                                       value="{{$personnel->id}}" {{$checked ? "checked" : ""}}>
                                                {{$personnel->name . " (". $personnel->tck_no.")"}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </form>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8">
                                    <button type="button" class="btn btn-primary btn-flat btn-block" id="updateButton">
                                        Güncelle
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
                        <p class="text-danger alert-danger" ng-show="subError"><%subError%></p>
                        <br>

                        <div class="form-group">
                            <div class="row">

                                <div class="col-xs-12 col-sm-8 col-md-6 pull-right">
                                    <div class="input-group" style="padding-top: 6px;">
                                        <input type="text" style="width: 100%"
                                               name="search" ng-model="name"
                                               value=""
                                               placeholder="Personel giriniz"/>
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
                                        <th class="text-center">Personel</th>
                                        <th class="text-center" ng-repeat="month in dates track by $index"><% month %>
                                        </th>
                                        <th class="text-right">Toplam</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="salary in salaries | searchFor:name:this track by $index">
                                        <td class="text-center"><%salary.personnel%></td>
                                        <td ng-repeat="total in salary.salary track by $index" class="text-right">
                                            <%total | numberFormatter%> TL
                                        </td>
                                        <td class="text-right"><% salary.salary | filTotal | numberFormatter %> TL</td>
                                    </tr>
                                    <tr class="bg-warning" ng-hide="name">
                                        <td style="text-align: right"><strong>GENEL TOPLAM</strong></td>
                                        <td ng-repeat="tot in monthsTotal track by $index" style="text-align: right">
                                            <i><%tot | numberFormatter %> TL</i>
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