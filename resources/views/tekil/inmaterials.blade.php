<?php
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
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


        function setHeaderHeights() {
            if ($('#searchField').height() > $('#wageField').height()) {
                $('#wageField').height($('#searchField').height());
            }
            else {
                $('#searchField').height($('#wageField').height());
            }
        }

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
            $scope.data = null;
            $scope.incomingmat = [];
            $scope.startDate = '';
            $scope.endDate = '';
            $scope.name = '';
            $scope.loading = false;

            $scope.getOvertimes = function () {
                if ($('input[name="end-date"]').val()) {
                    $scope.startDate = $('input[name="start-date"]').val();
                    $scope.endDate = $('input[name="end-date"]').val();
                }
                $scope.loading = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-inmaterials", {
                    'start_date': $scope.startDate,
                    'end_date': $scope.endDate
                }).then(function (response) {
                    $scope.data = response.data;
                    $scope.incomingmat = response.data.incomingmat;
                }).finally(function () {
                    $scope.loading = false;
                    setHeaderHeights();
                });
            }
        }).filter('searchFor', function () {
            return function (arr, searchStr) {
                if (!searchStr) {
                    return arr;
                }
                var result = [];
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if ((item.name + ' ' + item.firm + ' ' + item.irsaliye).turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
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
            angular.element('#angPuantaj').scope().getOvertimes();
            $('.date-filter').show();
        });

        $(document).ready(function () {
            $('.date-filter').hide();
            angular.element('#angPuantaj').scope().getOvertimes();
            $('#reportrange span').html("Filtrelemek için tarih seçiniz.");
            setHeaderHeights();
            $('.date-filter').on('click', function (e) {
                e.preventDefault();
                $('input[name="start-date"]').val();
                $('input[name="end-date"]').val();
                $('#reportrange span').html("Filtrelemek için tarih seçiniz.");
                angular.element('#angPuantaj').scope().getOvertimes();
                $(this).hide();
            });

        });


    </script>
@stop

@section('content')
    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
        <div class="form-group">
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-4">
                    <div class="input-group">
                        <input type="text" style="width: 100%"
                               name="search" ng-model="name"
                               value=""
                               placeholder="Malzeme, firma veya irsaliye no giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                    </div>
                </div>

                <div class="col-xs-6 col-sm-4 col-md-2 pull-right" style="min-width: 260px">
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
                                            <th>TARİH</th>
                                            <th>TALEP NO</th>
                                            <th>MALZEME</th>
                                            <th>BİRİM</th>
                                            <th>MİKTAR</th>
                                            <th>FİRMA</th>
                                            <th>AÇIKLAMA</th>
                                            <th>İRSALİYE NO</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="mat in incomingmat | searchFor:name track by $index">

                                            <td><% mat.date %></td>
                                            <td><% mat.id %></td>
                                            <td><% mat.name %></td>
                                            <td><% mat.unit %></td>
                                            <td><% mat.quantity %></td>
                                            <td><% mat.firm %></td>
                                            <td><%mat.explanation%></td>
                                            <td><%mat.irsaliye%></td>

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