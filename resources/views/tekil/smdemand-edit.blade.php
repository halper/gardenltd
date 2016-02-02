<?php
use Carbon\Carbon;

$today = \App\Library\CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
?>

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
@endsection

@section('page-specific-js')

    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script>
        $('#dateRangePicker').datepicker({
            autoclose: true,
            language: 'tr'
        });
        $("#dateRangePicker > input").val("{{$today}}");
        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.data = null;
            $scope.date = '{{$today}}';
            $scope.submaterials = [];
            $scope.subError = '';
            $scope.submatSelected = '';
            $scope.pricesmds = [];
            $scope.smdID = '{{$smdemand->id}}';
            $scope.price = '';
            $scope.smid = '';


            $scope.drChange = function () {
                $scope.smid = $scope.submatSelected.id;
            };

            $scope.init = function () {

                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-submaterials-from-smdemands", {
                            'id': $scope.smdID
                        }
                ).then(function (response) {
                    $scope.submaterials = response.data.submats;
                });

                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-pricesmds", {
                            'smdID': $scope.smdID
                        }
                ).then(function (response) {
                    $scope.pricesmds = response.data;
                });
            };

            $scope.addPrice = function () {
                $scope.smid = $scope.submatSelected.id;
                if (!$scope.date || !$scope.price || !$scope.smid) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: Tarih, bağlantı malzeme, fiyat!'
                }
                else {
                    $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/add-pricesmd", {
                        'since': $scope.date,
                        'smid': $scope.smid,
                        'price': $scope.price,
                        'smdID': $scope.smdID,
                    }).then(function (response) {
                        $scope.smid = '';
                        $scope.price = '';
                        $scope.date = '{{$today}}';
                        $scope.subError = '';
                        $scope.submatSelected = '';
                        $scope.init();
                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-pricesmd", {
                    'id': item.id
                }).then(function () {
                    $scope.init();
                    angular.forEach($scope.submaterials, function (value, key) {
                        if (parseInt(item.subid) == value.id) {
                            $scope.submatSelected = value;
                        }
                    });
                    if (item.since === "İlk değer") {
                        $scope.date = '01.01.1970';
                    } else {
                        $scope.date = item.since;
                    }
                    $scope.price = item.price;
                });

            }
        }).filter('numberFormatter', function () {
            return function (data) {
                return $.number(data, 2, ',', '.');
            }
        });

        $(document).ready(function () {
            angular.element('#angPuantaj').scope().init();
        });

        $('.btn-approve').on('click', function () {
            $(this).next().removeClass("hidden");
            $(this).next().show();
            $(this).hide();
        });
        $('.btn-cancel-sm').on('click', function () {
            $(this).parent("div").parent("div").prev().show();
            $(this).parent("div").parent("div").hide();
        });
        $('.btn-remove-sm').on('click', function () {
            var $matId = $(this).data('id');
            $.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-submaterial",
                    {
                        'id': $matId,
                        'smdid': '{{$smdemand->id}}'
                    },
                    function (data) {
                        $('#tr-sm-' + $matId).remove();
                    });
        });
        $.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-submaterials",
                {
                    'mid': '{{$smdemand->material->id}}'
                }, function (data) {
                    $(".js-example-data-array").select2({
                        placeholder: '{{$smdemand->material->material}} cinsinden malzeme seçiniz',
                        data: data
                    }).trigger("change");
                    $(".js-example-data-array").prop("disabled", false);
                    $(".js-example-data-array-2").select2({
                        placeholder: '{{$smdemand->material->material}} cinsinden malzeme seçiniz',
                        data: data
                    }).trigger("change");
                    $(".js-example-data-array-2").prop("disabled", false);
                }
        );
        $(".js-example-data-array").select2();
        $(".js-example-data-array").prop("disabled", true);
        $(".js-example-data-array-2").select2();
        $(".js-example-data-array-2").prop("disabled", true);
    </script>
@endsection

@extends('tekil.layout')

@section('content')
    <h2>{{$smdemand->material->material}}</h2>

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_5" data-toggle="tab">Malzeme</a></li>
                    <li><a href="#tab_1" data-toggle="tab">Fiyat</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">


                    <div class="tab-pane active" id="tab_5">
                        @include('tekil._smd-mat-edit')
                    </div>


                    <div class="tab-pane" id="tab_1">
                        @include('tekil._smd-price-edit')
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection

