<?php
$account = $site->account;
?>

@extends('tekil/layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>


    <script>
        var accountApp = angular.module('accApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('AccountController', function ($scope, $http) {
            $scope.expenses = [];

            $http.get("<?=URL::to('/');?>/tekil/{{$site->slug}}/expenses/{{$account->id}}"
            ).success(function (data, status, headers, config) {
                $scope.expenses = data;
                angular.forEach($scope.expenses, function (expense) {
                    expense.income = parseFloat(expense.income);
                    expense.expense = parseFloat(expense.expense);
                    expense.type = parseInt(expense.type);
                });
            });

            $scope.sortType = 'date';
            $scope.sortReverse = true;
            $scope.order = function(sortType) {
                $scope.sortReverse = ($scope.sortType === sortType) ? !$scope.sortReverse : false;
                $scope.sortType = sortType;
            };
            $scope.exp = 0;
            $scope.inc = 0;
            $scope.show = 'both';

            $scope.expTotal = function () {
                for (var i = 0; i < $scope.expenses.length; i++) {
                    var data = $scope.expenses[i];
                    $scope.exp += 1 * data.expense;
                }
                return $scope.exp;
            };
            $scope.incTotal = function () {
                for (var i = 0; i < $scope.expenses.length; i++) {
                    $scope.inc += 1 * $scope.expenses[i].income;
                }
                return $scope.inc;
            };

            $scope.showMe = function (type) {
                if ($scope.show == 'both') {
                    return true;
                }
                else {
                    if ($scope.show == '0' && type == 0) {
                        return true;
                    }
                    if ($scope.show == '1' && type == 1) {
                        return true;
                    }
                }
                return false;
            };

            $scope.addExpense = function () {
                if (!$('input[name="income"]').val()) {
                    $scope.income = 0;
                }
                if (!$('input[name="expense"]').val()) {
                    $scope.expense = 0;
                }
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/add-expense", {
                    'exp_date': $scope.date,
                    'account_id': '{{$account->id}}',
                    'definition': $scope.definition,
                    'buyer': $scope.buyer,
                    'type': $scope.type,
                    'income': $scope.income,
                    'expense': $scope.expense
                }).success(function (response) {

                    $scope.expenses.push({
                        'date': $scope.date,
                        'definition': $scope.definition,
                        'buyer': $scope.buyer,
                        'type': $scope.type,
                        'income': $scope.income,
                        'expense': $scope.expense
                    });
                    $scope.date = '';
                    $scope.definition = '';
                    $scope.buyer = '';
                    $scope.type = '';
                    $scope.income = '';
                    $scope.expense = '';

                });

            };



        }).filter('sumOfValue', function () {
            return function (data, key) {
                if (angular.isUndefined(data) && angular.isUndefined(key))
                    return 0;
                var sum = 0;

                angular.forEach(data, function (v, k) {
                    sum = sum + parseInt(v[key]);
                });
                return sum;
            }
        }).filter('subTotal', function () {
            return function (data, index) {
                if (angular.isUndefined(data))
                    return 0;
                var subTotal = 0;

                for (var i = 0; i < index; i++) {
                    subTotal += parseInt(data[i].income) - parseInt(data[i].expense);
                }

                return subTotal;
            }
        }).filter('filTotal', function () {
            return function (data, show, key) {
                if (angular.isUndefined(data))
                    return 0;
                var subTotal = 0;

                angular.forEach(data,function(v,k){
                    if(v.type == show)
                        subTotal += parseInt(v[key]);
                });

                return subTotal;
            }
        });

        $('#dateRangePicker').datepicker({
            autoclose: true,
            firstDay: 1,
            format: 'dd.mm.yyyy'
        });

    </script>
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-4 col-md-offset-4">
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Kasa Bilgileri</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <tbody>

                            <tr>

                                <td><strong>KASA SAHİBİ:</strong></td>
                                <td>{{$account->owner}}</td>

                                <td></td>
                                <td></td>

                                <td><strong>DÖNEM:</strong></td>
                                <td>{{$account->period}}</td>
                            </tr>


                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <div ng-app="accApp" ng-controller="AccountController">
        <div class="row">
            <div class="col-md-4">
                <a href="#" ng-click="show = '1'">Kredi kartı harcamalarını göster</a>
            </div>
            <div class="col-md-1">
                <span> | </span>
            </div>
            <div class="col-md-3">

                <a href="#" ng-click="show = '0'">Nakit harcamaları göster</a>
            </div>
            <div class="col-md-1">
                <span> | </span>
            </div>
            <div class="col-md-3">

                <a href="#" ng-click="show = 'both'">Tüm harcamaları göster</a>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th id="index">S.N</th>
                            <th>
                                <a href="#" ng-click="order('date')">
                                <span ng-show="sortType == 'date' && !sortReverse"
                                      class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'date' && sortReverse"
                                      class="fa fa-caret-up"></span>
                                    TARİH
                                </a>
                            </th>
                            <th id="definition">
                                <a href="#" ng-click="order('definition')">
                                <span ng-show="sortType == 'definition' && !sortReverse"
                                      class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'definition' && sortReverse"
                                      class="fa fa-caret-up"></span>
                                    AÇIKLAMA
                                </a></th>
                            <th id="buyer">
                                <a href="#" ng-click="order('buyer')">
                                <span ng-show="sortType == 'buyer' && !sortReverse"
                                      class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'buyer' && sortReverse"
                                      class="fa fa-caret-up"></span>
                                    HARCAMAYI YAPAN
                                </a></th>
                            <th id="type">
                                <a href="#" ng-click="order('type')">
                                <span ng-show="sortType == 'type' && !sortReverse"
                                      class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'type' && sortReverse"
                                      class="fa fa-caret-up"></span>
                                    ÖDEME ŞEKLİ
                                </a>
                            </th>
                            <th id="income">
                                <a href="#"
                                   ng-click="order('income')">
                                <span ng-show="sortType == 'income' && !sortReverse"
                                      class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'income' && sortReverse"
                                      class="fa fa-caret-up"></span>
                                    GELİR
                                </a></th>
                            <th id="expense">
                                <a href="#"
                                   ng-click="order('expense')">
                                <span ng-show="sortType == 'expense' && !sortReverse"
                                      class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expense' && sortReverse"
                                      class="fa fa-caret-up"></span>
                                    GİDER
                                </a></th>
                            <th id="account">KASA</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr class="bg-warning" ng-show="show=='both'">
                            <td></td>
                            <td></td>
                            <td><strong>GENEL TOPLAM:</strong></td>
                            <td></td>
                            <td></td>
                            <td><% expenses | sumOfValue:'income' %>TL</td>
                            <td><% expenses | sumOfValue:'expense' %>TL</td>
                            <td><% (expenses | sumOfValue:'income') - (expenses | sumOfValue:'expense') %>TL</td>

                        </tr>
                        <tr ng-repeat="expense in (expenses | orderBy:sortType:sortReverse) as sorted track by $index">
                            <td ng-show="showMe(expense.type)"><strong><% $index+1 %></strong></td>
                            <td  ng-show="showMe(expense.type)"><strong><% expense.date %></strong></td>
                            <td  ng-show="showMe(expense.type)"><% expense.definition %></td>
                            <td  ng-show="showMe(expense.type)"><% expense.buyer %></td>
                            <td  ng-show="showMe(expense.type)"><% expense.type == 0 ? 'Nakit' : 'Kredi Kartı (' + expense.card_owner + ')' %></td>
                            <td  ng-show="showMe(expense.type)"><% expense.income %>TL</td>
                            <td  ng-show="showMe(expense.type)"><% expense.expense%>TL</td>
                            <td  ng-show="showMe(expense.type)"><% show=='both' ? (sorted | subTotal:$index+1) : '' %>TL</td>
                        </tr>
                        <tr class="bg-warning" ng-show="show!='both'">
                            <td></td>
                            <td></td>
                            <td><strong><% show=='0' ? 'NAKİT HARCAMALAR' : 'KART HARCAMALARI' %> TOPLAMI</strong></td>
                            <td></td>
                            <td></td>
                            <td><% expenses | filTotal:show:'income' %>TL</td>
                            <td><% expenses | filTotal:show:'expense' %>TL</td>
                            <td><% (expenses | filTotal:show:'income') - ( expenses | filTotal:show:'expense') %> TL</td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="input-group input-append date " id="dateRangePicker">
                            <input type="text" class="form-control" name="exp_date" ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <input type="text" class="form-control"
                               name="definition" ng-model="definition"
                               value=""/>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control"
                               name="buyer" ng-model="buyer"
                               value=""/>
                    </div>
                    <div class="col-md-2">
                        <select name="type" ng-model="type" class="form-control">
                            <option value="" disabled selected>Ödeme Şekli</option>
                            <option value="0">Nakit</option>
                            <option value="1">Kredi Kartı</option>

                        </select>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control"
                               name="income" ng-model="income"
                               value="0"/>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control"
                               name="expense" ng-model="expense"
                               value="0"/>
                    </div>

                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-4 col-md-offset-4">
                        <br>
                        <button  type="button" ng-click="addExpense()"
                                class="btn btn-primary btn-flat btn-block">Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>



@stop