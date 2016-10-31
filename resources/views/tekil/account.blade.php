<?php
$account = $site->account()->get()->isEmpty() ? null : $site->account;

use App\Library\CarbonHelper;
use Carbon\Carbon;

$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());


$user = Auth::user();

$addr = explode("/", $_SERVER['REQUEST_URI']);
$slug = $addr[sizeof($addr) - 1];
$module = $modules->whereSlug($slug)->first();

$post_permission = \App\Library\PermissionHelper::checkUserPostPermissionOnModule($user, $module);

$can_delete = false;
$can_create_record = false;


if ($user->isAdmin()) {
    $can_delete = true;
    $can_create_record = true;
} else
    foreach ($user->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('harcama-sil')) {
            $can_delete = true;
        }
        if ($group->hasSpecialPermissionForSlug('harcama-kaydi-olustur')) {
            $can_create_record = true;
        }
    }

?>

@extends('tekil/layout')

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
                $scope.startDate = '';
                $scope.endDate = '';
                $scope.buyer = '{{$account->user()->get()->isEmpty() ? '' : $account->user()->owner()->first()->name}}';
                $scope.date = moment().format('DD.MM.YYYY');
                $scope.search = '';

                $scope.getExpenses = function () {
                    if ($('input[name="end-date"]').val()) {
                        $scope.startDate = $('input[name="start-date"]').val();
                        $scope.endDate = $('input[name="end-date"]').val();
                    }
                    $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/expenses", {
                        start_date: $scope.startDate,
                        end_date: $scope.endDate
                    }).then(function (response) {
                        $scope.expenses = response.data;
                        angular.forEach($scope.expenses, function (expense) {
                            expense.income = parseFloat(expense.income);
                            expense.expense = parseFloat(expense.expense);
                            expense.type = parseInt(expense.type);
                        });
                    }).finally(function () {
                        $scope.startDate = '';
                        $scope.endDate = '';
                    });
                };
                $scope.getExpenses();

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
                        exp_date: $scope.date,
                        account_id: '{{$account->id}}',
                        definition: $scope.definition,
                        buyer: $scope.buyer,
                        type: $scope.type,
                        income: $scope.income,
                        expense: $scope.expense
                    }).then(function () {
                        $scope.getExpenses();
                        $scope.date = moment().format('DD.MM.YYYY');
                        $scope.definition = '';
                        $scope.buyer = '{{$account->user()->get()->isEmpty() ? '' : $account->user()->owner()->first()->name}}';
                        $scope.type = '';
                        $scope.income = '';
                        $scope.expense = '';

                    });

                };

                $scope.orderByDate = function (item) {
                    var parts = item.date.split('.');
                    var number = parseInt(parts[2] + parts[1] + parts[0]);

                    return $scope.sortReverse ? -number : number;
                };
                $scope.remove_field = function (item) {
                    $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-expense", {
                        id: item.id
                    }).then(function () {
                        $scope.getExpenses();
                        $scope.date = item.date;
                        $scope.definition = item.definition;
                        $scope.buyer = item.buyer;
                        $scope.type = (item.type).toString();
                        $scope.income = item.income;
                        $scope.expense = item.expense;
                    });

                }
            }).filter('numberFormatter', function () {
                return function (data) {
                    return $.number(data, 2, ',', '.');
                }
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
                language: 'tr'
            });

            function cb(start, end) {
                $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
            }

            //        cb(moment().startOf('month'), moment());

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
                angular.element('#angAccount').scope().getExpenses();
                $('.date-filter').show();
            });

            $(document).ready(function () {
                $('.date-filter').hide();
                angular.element('#angAccount').scope().getExpenses();
                $('#reportrange span').html("Filtrelemek için tarih seçiniz.");

                $('.date-filter').on('click', function (e) {
                    e.preventDefault();
                    $('input[name="start-date"]').val('');
                    $('input[name="end-date"]').val('');
                    $('#reportrange span').html("Filtrelemek için tarih seçiniz.");
                    angular.element('#angAccount').scope().getExpenses();
                    $(this).hide();
                });

            });

        </script>
    @endif
@stop

@section('content')
    @if($account && !$account->user()->get()->isEmpty())
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
        <div ng-app="accApp" ng-controller="AccountController" id="angAccount">
            @if($post_permission || $can_create_record)
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
                                <input type="text" class="form-control number"
                                       name="income" ng-model="income"
                                       value="0"
                                       placeholder="Gelir"/>
                            </div>
                            <div class="col-md-1">
                                <input type="text" class="form-control number"
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
            @endif
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
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <input type="text" style="width: 100%"
                                   name="search" ng-model="search"
                                   value=""
                                   placeholder="Tarih, açıklama veya harcamayı yapanı giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-2 pull-right" style="min-width: 260px">
                        <div id="reportrange" class="pull-right"
                             style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span class="text-center"></span>
                        </div>
                    </div>
                    <input type="hidden" name="start-date" ng-model="startDate">
                    <input type="hidden" name="end-date" ng-model="endDate">
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-2 pull-right">
                    <div class="form-group">
                        <a href="#" class="btn btn-flat btn-primary date-filter btn-block">Tümünü Göster</a>
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
                                    AÇIKLAMA
                                </th>
                                <th id="buyer">
                                    HARCAMAYI YAPAN
                                </th>
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
                                @if($post_permission || $can_delete)
                                    <th id="del">SİL</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            <tr class="bg-warning" ng-show="show=='both'">
                                <td></td>
                                <td></td>
                                <td><strong>GENEL TOPLAM:</strong></td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right"><% expenses | sumOfValue:'income' | numberFormatter %>TL
                                </td>
                                <td style="text-align: right"><% expenses | sumOfValue:'expense' | numberFormatter
                                    %>TL
                                </td>
                                <td style="text-align: right"><% (expenses | sumOfValue:'income') - (expenses |
                                    sumOfValue:'expense') |
                                    numberFormatter %>TL
                                </td>
                                <td></td>
                            </tr>
                            <tr ng-repeat="expense in (expenses | orderBy:orderByDate | searchFor:search) as sorted track by $index">
                                <td ng-show="showMe(expense.type)"><strong><% $index+1 %></strong></td>
                                <td ng-show="showMe(expense.type)"><strong><% expense.date %></strong></td>
                                <td ng-show="showMe(expense.type)"><% expense.definition %></td>
                                <td ng-show="showMe(expense.type)"><% expense.buyer %></td>
                                <td ng-show="showMe(expense.type)"><% expense.type == 0 ? 'Nakit' : 'Kredi Kartı (' +
                                    expense.card_owner + ')' %>
                                </td>
                                <td style="text-align: right" ng-show="showMe(expense.type)"><% expense.income |
                                    numberFormatter%>TL
                                </td>
                                <td style="text-align: right" ng-show="showMe(expense.type)"><% expense.expense |
                                    numberFormatter%>TL
                                </td>
                                <td style="text-align: right" ng-show="showMe(expense.type)"><% show=='both' ? (sorted |
                                    subTotal:$index+1 |
                                    numberFormatter) : ''
                                    %>TL
                                </td>
                                @if($post_permission || $can_delete)
                                    <td ng-show="showMe(expense.type)"><a href="#" ng-click="remove_field(expense)"><i
                                                    class="fa fa-close"></i></a></td>
                                @endif

                            </tr>
                            <tr class="bg-warning" ng-show="show!='both'">
                                <td></td>
                                <td></td>
                                <td><strong><% show=='0' ? 'NAKİT HARCAMALAR' : 'KART HARCAMALARI' %> TOPLAMI</strong>
                                </td>
                                <td></td>
                                <td></td>
                                <td style="text-align: right"><% expenses | filTotal:show:'income' | numberFormatter
                                    %>TL
                                </td>
                                <td style="text-align: right"><% expenses | filTotal:show:'expense' |
                                    numberFormatter%>TL
                                </td>
                                <td style="text-align: right"><% ((expenses | filTotal:show:'income') - ( expenses |
                                    filTotal:show:'expense')) |
                                    numberFormatter %>
                                    TL
                                </td>

                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>


    @else
        <p class="text-danger">Şantiye için yöneticinizin kasa ve kasa sahibi tanımlaması gerekmektedir.</p>
    @endif
@stop