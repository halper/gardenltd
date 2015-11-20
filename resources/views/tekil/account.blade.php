@extends('tekil/layout')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?=URL::to('/');?>/js/angular-route.min.js"></script>

    <script>
        var sampleApp = angular.module('accApp', [], function($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('AccountController', function($scope, $http) {
            $scope.expenses = [];
            $scope.sortType     = 'date'; // set the default sort type
            $scope.sortReverse  = false;  // set the default sort order
            $scope.total = 0;
            $scope.getTotal = function($rowTotal){
                $scope.total = $scope.total + $rowTotal;
                return $scope.total;
            };

            $http.get("<?=URL::to('/');?>/tekil/{{$site->slug}}/expenses").
            success(function(data, status, headers, config) {
                $scope.expenses = data;
            });
        });

        $('#dateRangePicker').datepicker({
            autoclose: true,
            firstDay: 1,
            format: 'dd.mm.yyyy',
            startDate: '01.01.2015',
            endDate: '30.12.2100'
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
                                <td>SADIK HERGÜL</td>

                            </tr>
                            <tr>
                                <td><strong>DÖNEM:</strong></td>
                                <td>2014 - 2015</td>
                            </tr>
                            <tr>
                                <td><strong>ŞANTİYE ADI:</strong></td>
                                <td>{{$site->job_name}}</td>
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

    <div class="row" ng-app="accApp" ng-controller="AccountController">
        <div class="col-xs-12 col-md-12" >
            <div class="table-responsive">
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <th>S.N</th>
                        <th>
                            <a href="#" ng-click="sortType = 'expenses.date'; sortReverse = !sortReverse">
                                <span ng-show="sortType == 'expenses.date' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expenses.date' && sortReverse" class="fa fa-caret-up"></span>
                                TARİH
                        </a>
                        </th>
                        <th>
                            <a href="#" ng-click="sortType = 'expenses.definition'; sortReverse = !sortReverse">
                                <span ng-show="sortType == 'expenses.definition' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expenses.definition' && sortReverse" class="fa fa-caret-up"></span>
                                AÇIKLAMA
                        </a></th>
                        <th>
                            <a href="#" ng-click="sortType = 'expenses.buyer'; sortReverse = !sortReverse">
                                <span ng-show="sortType == 'expenses.buyer' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expenses.buyer' && sortReverse" class="fa fa-caret-up"></span>
                                HARCAMAYI YAPAN
                            </a></th>
                        <th>
                            <a href="#" ng-click="sortType = 'expenses.type'; sortReverse = !sortReverse">
                                <span ng-show="sortType == 'expenses.type' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expenses.type' && sortReverse" class="fa fa-caret-up"></span>
                                ÖDEME ŞEKLİ
                            </a>
                            </th>
                        <th>
                            <a href="#" ng-click="sortType = 'expenses.income'; sortReverse = !sortReverse">
                                <span ng-show="sortType == 'expenses.income' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expenses.income' && sortReverse" class="fa fa-caret-up"></span>
                                GELİR
                            </a></th>
                        <th>
                            <a href="#" ng-click="sortType = 'expenses.expense'; sortReverse = !sortReverse">
                                <span ng-show="sortType == 'expenses.expense' && !sortReverse" class="fa fa-caret-down"></span>
                                <span ng-show="sortType == 'expenses.expense' && sortReverse" class="fa fa-caret-up"></span>
                                GİDER
                            </a></th>
                        <th>KASA</th>
                    </tr>
                    </thead>
                    <tbody>

                    <tr class="bg-warning">
                        <td></td>
                        <td></td>
                        <td><strong>GENEL TOPLAM:</strong></td>
                        <td></td>
                        <td></td>
                        <td>392.020,25TL</td>
                        <td>391.553,76TL</td>
                        <td>466,49TL</td>

                    </tr>
                    <tr ng-repeat='expense in expenses | orderBy:sortType:sortReverse'>
                        <td><strong>1</strong></td>
                        <td><strong><% expense.date %></strong></td>
                        <td><% expense.definition %></td>
                        <td><% expense.buyer %></td>
                        <td><% expense.type == 0 ? 'Nakit' : 'Kredi Kartı' %></td>
                        <td><% expense.income %>TL</td>
                        <td><% expense.expense %>TL</td>
                        <td><% getTotal(expense.income - expense.expense) %>TL</td>
                    </tr>


                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop