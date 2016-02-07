<?php
$account = $site->account()->get()->isEmpty() ? null : $site->account;
?>

@extends('tekil/layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="{{asset("js/moment.min.js")}}"></script>

    @if($account)
        <script>
            String.prototype.turkishToLower = function () {
                var string = this;
                var letters = {"İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç"};
                string = string.replace(/(([İIŞĞÜÇÖ]))/g, function (letter) {
                    return letters[letter];
                });
                return string.toLowerCase();
            };
            var accountApp = angular.module('accApp', [], function ($interpolateProvider) {
                $interpolateProvider.startSymbol('<%');
                $interpolateProvider.endSymbol('%>');
            }).controller('AccountController', function ($scope, $http) {
                $scope.expenses = [];
                $scope.buyer = '{{$account->user()->owner()->first()->name}}';
                $scope.date = moment().format('DD.MM.YYYY');
                $scope.search = '';

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
                $scope.order = function (sortType) {
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
                        $scope.date = moment().format('DD.MM.YYYY');;
                        $scope.definition = '';
                        $scope.buyer = '{{$account->user()->owner()->first()->name}}';
                        $scope.type = '';
                        $scope.income = '';
                        $scope.expense = '';

                    });

                };

                $scope.orderByDate = function(item) {
                    var parts = item.date.split('.');
                    var number = parseInt(parts[2] + parts[1] + parts[0]);

                    return $scope.sortReverse ? -number : number;
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

                    angular.forEach(data, function (v, k) {
                        if (v.type == show)
                            subTotal += parseInt(v[key]);
                    });

                    return subTotal;
                }
            }).filter('searchFor', function () {
                return function (arr, searchStr) {
                    if (!searchStr) {
                        return arr;
                    }
                    var result = [];
                    searchStr = searchStr.turkishToLower();
                    angular.forEach(arr, function (item) {
                        if ((item.date + ' ' + item.definition + ' ' + item.buyer).turkishToLower().indexOf(searchStr) !== -1) {
                            result.push(item);
                        }
                    });
                    return result;
                };
            });

            $('#dateRangePicker').datepicker({
                language: 'tr',
            });

        </script>
    @endif
@stop

@section('content')
    @if($account)
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
                                    <td>{{$account->user()->owner()->first()->name}}</td>

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
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-4 pull-right">
                        <div class="input-group">
                            <input type="text" style="width: 100%"
                                   name="search" ng-model="search"
                                   value=""
                                   placeholder="Tarih, açıklama veya harcamayı yapanı giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                        </div>
                    </div>
                </div>
            </div>

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
                                        AÇIKLAMA</th>
                                <th id="buyer">
                                        HARCAMAYI YAPAN</th>
                                <th id="type">

                                        ÖDEME ŞEKLİ

                                </th>
                                <th id="income">
                                        GELİR
                                    </th>
                                <th id="expense">
                                    GİDER
                                    </th>
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
                            <tr ng-repeat="expense in (expenses | orderBy:orderByDate | searchFor:search) as sorted track by $index">
                                <td ng-show="showMe(expense.type)"><strong><% $index+1 %></strong></td>
                                <td ng-show="showMe(expense.type)"><strong><% expense.date %></strong></td>
                                <td ng-show="showMe(expense.type)"><% expense.definition %></td>
                                <td ng-show="showMe(expense.type)"><% expense.buyer %></td>
                                <td ng-show="showMe(expense.type)"><% expense.type == 0 ? 'Nakit' : 'Kredi Kartı (' +
                                    expense.card_owner + ')' %>
                                </td>
                                <td ng-show="showMe(expense.type)"><% expense.income %>TL</td>
                                <td ng-show="showMe(expense.type)"><% expense.expense%>TL</td>
                                <td ng-show="showMe(expense.type)"><% show=='both' ? (sorted | subTotal:$index+1) : ''
                                    %>TL
                                </td>
                            </tr>
                            <tr class="bg-warning" ng-show="show!='both'">
                                <td></td>
                                <td></td>
                                <td><strong><% show=='0' ? 'NAKİT HARCAMALAR' : 'KART HARCAMALARI' %> TOPLAMI</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td><% expenses | filTotal:show:'income' %>TL</td>
                                <td><% expenses | filTotal:show:'expense' %>TL</td>
                                <td><% (expenses | filTotal:show:'income') - ( expenses | filTotal:show:'expense') %>
                                    TL
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <h4>Yeni Harcama Kaydı Oluştur</h4>

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
                                   value=""
                                   placeholder="Harcama açıklaması"/>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control"
                                   name="buyer" ng-model="buyer"
                                   value=""
                                   placeholder="Harcamayı Yapan"/>
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
                                   value="0"
                                   placeholder="Gelir"/>
                        </div>
                        <div class="col-md-1">
                            <input type="number" class="form-control"
                                   name="expense" ng-model="expense"
                                   value="0"
                                   placeholder="Gider"/>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-md-4 col-md-offset-4">
                            <br>
                            <button type="button" ng-click="addExpense()"
                                    class="btn btn-primary btn-flat btn-block">Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>


    @else
        <p class="text-danger">Şantiye için yöneticinizin kasa tanımlaması gerekmektedir.</p>
    @endif
@stop