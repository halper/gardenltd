<?php
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use Carbon\Carbon;

$today = CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
$user = Auth::user();
$can_filter = false;
if ($user->isAdmin()) {
    $can_filter = true;
} else
    foreach ($user->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('is-ilerleme-filtrele')) {
            $can_filter = true;
        }
    }
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
            $scope.staff = [];
            $scope.startDate = '';
            $scope.endDate = '';
            $scope.name = '';
            $scope.loading = false;

            $scope.getOvertimes = function () {
                if (!$('input[name="end-date"]').val()) {
                    $scope.startDate = moment().startOf('month').format('YYYY-MM-DD');
                    $scope.endDate = moment().format('YYYY-MM-DD');
                }
                else {
                    $scope.startDate = $('input[name="start-date"]').val();
                    $scope.endDate = $('input[name="end-date"]').val();
                }
                $scope.loading = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/progress", {
                    'start_date': $scope.startDate,
                    'end_date': $scope.endDate
                }).then(function (response) {
                    $scope.data = response.data;
                    $scope.staff = response.data.staff;
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
                    if (item.name.turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        }).filter('bgColor', function () {
            return function (data) {
                var percentage = 100 * (data.done / data.planned);
                if (percentage == 100) {
                    return '#fcf8e3';
                }
                if (percentage > 100) {
                    return '#dff0d8';
                }
                if (percentage < 100) {
                    return '#f2dede';
                }
            }
        });

        function cb(start, end) {
            $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
        }
        cb(moment().startOf('month'), moment());

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
        });


        $(document).ready(function () {
            angular.element('#angPuantaj').scope().getOvertimes();
            setHeaderHeights();
        });


    </script>
@stop

@section('content')
    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">

        <div class="form-group">
            <div class="row">
                @if($can_filter)
                    <div class="col-xs-12 col-sm-4 col-md-2" style="min-width: 260px">
                        <div id="reportrange" class="pull-right"
                             style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span class="text-center"></span>
                        </div>
                    </div>
                @endif
                <input type="hidden" name="start-date" ng-model="startDate">
                <input type="hidden" name="end-date" ng-model="endDate">
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
                                            <th class="puantaj" id="searchField">
                                                <div class="input-group">
                                                    <input type="text" style="width: 100%"
                                                           name="personnel-search" ng-model="name"
                                                           value=""
                                                           placeholder="Birim Ara"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                                </div>
                                            </th>
                                            <th>KİŞİ SAYISI</th>
                                            <th>ÖLÇÜ BİRİMİ</th>
                                            <th>PLANLANAN</th>
                                            <th>YAPILAN</th>
                                            <th>YAPILAN İŞLER</th>
                                            <th>TARİH</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="st in staff | searchFor:name track by $index"
                                            ng-style="{'background-color': (st|bgColor)}">

                                            <td><% st.name %></td>
                                            <td><% st.quantity %></td>
                                            <td><%st.unit%></td>
                                            <td><%st.planned%></td>
                                            <td><%st.done%></td>
                                            <td><%st.works_done%></td>
                                            <td><%st.date%></td>
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