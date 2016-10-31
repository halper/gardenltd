<?php
use Carbon\Carbon;

$today = \App\Library\CarbonHelper::getTurkishDate(Carbon::now()->toDateString());


$user = Auth::user();

$addr = explode("/", $_SERVER['REQUEST_URI']);
$slug = $addr[sizeof($addr) - 1];
$module = $modules->whereSlug($slug)->first();

$post_permission = \App\Library\PermissionHelper::checkUserPostPermissionOnModule($user, $module);

$can_request_demand = false;
$can_view_demand = false;
$can_update_demand = false;


if ($user->isAdmin()) {
    $can_request_demand = true;
    $can_view_demand = true;
    $can_update_demand = true;
} else
    foreach ($user->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('baglantili-malzeme-talep-olustur')) {
            $can_request_demand = true;
        }
        if ($group->hasSpecialPermissionForSlug('baglantili-malzeme-talep-goruntule')) {
            $can_view_demand = true;
        }
        if ($group->hasSpecialPermissionForSlug('baglantili-malzeme-talep-guncelle')) {
            $can_update_demand = true;
        }
    }
?>

@extends('tekil/layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')

    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>

    <script>
        $('#dateRangePicker').datepicker({
            autoclose: true,
            language: 'tr'
        });
        $("#dateRangePicker > input").val("{{$today}}");

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
            $scope.total = null;
            $scope.remaining = null;
            $scope.date = '{{$today}}';
            $scope.materials = [];
            $scope.submaterials = [];
            $scope.submatSpent = [];
            $scope.expenses = [];
            $scope.contract_cost = '';
            $scope.loading = false;
            $scope.showRest = false;
            $scope.sid = '';
            $scope.subError = '';
            $scope.submatSelected = '';
            $scope.inputSize = '';
            $scope.inputCounter = 1;
            $scope.name = '';
            $scope.orderedIrsaliye = '';

            $scope.bill = [];
            $scope.quantity = [];
            $scope.detail = [];

            $scope.removeRow = function (rowNumber) {
                angular.element(document).find('#ang-' + rowNumber).remove();
                $scope.bill[rowNumber] = -999;
            };

            $scope.getOvertimes = function () {

                $scope.loading = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-smdemands"
                ).then(function (response) {
                    $scope.materials = response.data;
                });
            };

            $scope.drChange = function () {
                $scope.sid = $scope.submatSelected.id;
            };

            $scope.init = function () {
                $scope.showRest = true;
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-submaterials-from-smdemands", {
                            id: $scope.selected.id
                        }
                ).then(function (response) {
                    $scope.submaterials = response.data.submats;
                    $scope.contract_cost = response.data.contract_cost;
                });

                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-smdexpenses", {
                            id: $scope.selected.id
                        }
                ).then(function (response) {
                    $scope.expenses = response.data.smdexpense;
                    $scope.submatSpent = response.data.submat_spent;
                    $scope.total = response.data.total_spent;
                    $scope.remaining = parseFloat(response.data.contract_cost) - parseFloat(response.data.total_spent);
                }).finally(function () {
                    $scope.loading = false;
                });
            };

            $scope.addExpense = function () {

                if (!$scope.date || !$scope.sid) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: İrsaliye, miktar, bağlantı malzeme!'
                }

                else {
                    $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/add-smdexpense", {
                        delivery_date: $scope.date,
                        detail: $scope.detail,
                        bill: $scope.bill,
                        quantity: $scope.quantity,
                        subid: $scope.sid,
                        smdid: $scope.selected.id
                    }).then(function (response) {

                        $scope.submatSelected = '';
                        $scope.date = '{{$today}}';
                        $scope.detail = [];
                        $scope.bill = [];
                        $scope.quantity = [];
                        $scope.irsaliye = [];
                        $scope.orderedIrsaliye = '';
                        $scope.hiddenRows = [];
                        $scope.sid = '';
                        $scope.subError = '';
                        for (var i = $scope.inputCounter; i > 1; i--) {
                            angular.element(document).find('.ang-added').remove();
                            // Increment the counter for the next input to be added
                            $scope.inputCounter--;
                        }
                        $scope.init();

                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-smdexpense", {
                    'id': item.id
                }).then(function () {
                    $scope.init();
                    angular.forEach($scope.submaterials, function (value, key) {
                        if (parseInt(item.subid) == value.id) {
                            $scope.submatSelected = value;
                        }
                    });
                    $scope.sid = item.subid;
                    $scope.date = item.date;
                    $scope.detail[0] = item.detail;
                    $scope.bill[0] = item.bill;
                    $scope.quantity[0] = item.quantity;
                });

            }
        }).filter('numberFormatter', function () {
            return function (data) {
                return $.number(data, 2, ',', '.');
            }
        }).filter('searchFor', function () {
            return function (arr, searchStr) {
                if (!searchStr) {
                    return arr;
                }
                var result = [];
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if ((item.date + ' ' + item.bill + ' ' + item.subname).turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        }).directive('addInput', ['$compile', function ($compile) { // inject $compile service as dependency
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    // click on the button to add new input field
                    element.find('.btn-success').bind('click', function () {
                        // I'm using Angular syntax. Using jQuery will have the same effect
                        // Create input element
                        var size = 1;
                        if (scope.inputSize) {
                            size = parseInt(scope.inputSize);
                        }
                        for (var i = 0; i < size; i++) {
                            var input = angular.element('' +
                                    '<div class="form-group ang-added" id="ang-' + scope.inputCounter + '">' +
                                    '<div class="row">' +
                                    '<div class="col-md-2"><div class="row">' +
                                    '<div class="col-md-2">' +
                                    '<a ng-click="removeRow(' + scope.inputCounter + ')"><i class="fa fa-close"></i></a></div>' +
                                    '<div class="col-md-10">' +
                                    '<input type="text" class="form-control"' +
                                    'name="no" ng-model="bill[' + scope.inputCounter + ']"' +
                                    'value=""' +
                                    'placeholder="İrsaliye No"/>' +
                                    '</div></div></div>' +
                                    '<div class="col-md-2">' +
                                    '<input type="text" class="form-control number"' +
                                    'name="quantity" ng-model="quantity[' + scope.inputCounter + ']"' +
                                    'value=""' +
                                    'placeholder="Miktar"/>' +
                                    '</div>' +
                                    '<div class="col-md-8">' +
                                    '<input type="text" class="form-control"' +
                                    'name="detail" ng-model="detail[' + scope.inputCounter + ']"' +
                                    'value=""' +
                                    'placeholder="Açıklama"/>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>');
                            // Compile the HTML and assign to scope
                            var compile = $compile(input)(scope);

                            scope.detail[scope.inputCounter] = scope.detail[0];
                            scope.quantity[scope.inputCounter] = scope.quantity[0];

                            if (scope.orderedIrsaliye) {
                                scope.bill[scope.inputCounter] = (parseInt(scope.bill[0]) + (size - i));
                            }
                            else {
                                scope.bill[scope.inputCounter] = scope.bill[0];
                            }


                            // Append input to div
                            element.prepend(input);

                            // Increment the counter for the next input to be added
                            scope.inputCounter++;
                        }
                        $('.number').number(true, 2, ',', '.');
                        scope.inputSize = '';
                    });

                }
            }
        }]);

        $(document).ready(function () {
            angular.element('#angPuantaj').scope().getOvertimes();
        });
        $(document).on("click", ".subDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #subDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> malzemesinin talebini silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'id',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteSubcontractorConfirm').modal('show');
        });


        $(".js-example-basic-multiple").select2({
            placeholder: "Eklemek istediğiniz malzemeleri seçiniz",
            allowClear: true
        });
        $(".js-example-basic-multiple").on("select2:select", function (e) {
            var $matId = $(this).val();
            var $matText = $(this).select2('data');
            $(".js-example-data-array").empty().select2().trigger("change");
            $(".js-example-data-array-2").empty().select2().trigger("change");
            $.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-submaterials",
                    {
                        'mid': $matId
                    }, function (data) {
                        $(".js-example-data-array").select2({
                            placeholder: $matText[0].text + ' cinsinden malzeme seçiniz',
                            data: data
                        }).trigger("change");
                        $(".js-example-data-array").prop("disabled", false);
                        $(".js-example-data-array-2").select2({
                            placeholder: $matText[0].text + ' cinsinden malzeme seçiniz',
                            data: data
                        }).trigger("change");
                        $(".js-example-data-array-2").prop("disabled", false);
                    }
            );
        });
        $(".js-example-data-array").select2();
        $(".js-example-data-array").prop("disabled", true);
        $(".js-example-data-array-2").select2();
        $(".js-example-data-array-2").prop("disabled", true);


        $('#materialDemandForm').submit(function (e) {
            var emptyTexts = $('#materialDemandForm .form-control').filter(function () {
                return !this.value;
            });
            if (emptyTexts.length > 0) {
                e.preventDefault();

                jQuery.each(emptyTexts, function () {
                    $(this).next("span").text("Lütfen ilgili alanları doldurunuz!");
                    $(this).next("span").addClass('text-danger');
                    $(this).parent().closest("div").addClass('has-error');

                });
            }
        });

    </script>

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    @if($post_permission || $can_request_demand)
                        <li class="active"><a href="#tab_5" data-toggle="tab">Talep Oluştur</a></li>
                    @endif
                    <li class="{{!($post_permission && $can_request_demand) ? "active" : ""}}"><a href="#tab_1" data-toggle="tab">Talep
                            Görüntüle</a></li>
                    @if($post_permission || $can_update_demand)
                        <li><a href="#tab_2" data-toggle="tab">Talep Güncelle</a></li>
                    @endif

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">

                    @if($post_permission || $can_request_demand)
                        <div class="tab-pane active" id="tab_5">
                            @include('tekil._new-followup')
                        </div>
                    @endif

                    @if($can_view_demand)
                    <div class="tab-pane {{!($post_permission && $can_request_demand) ? "active" : ""}}" id="tab_1">
                        @include('tekil._view-followup')
                    </div>
                        @endif

                    @if($post_permission || $can_update_demand)
                        <div class="tab-pane" id="tab_2">
                            @include('tekil._update-followup')
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>


    @if(isset($material_array))
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Bağlantı Malzeme Takip Formu</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        {!! Form::open([
                            'url' => "/tekil/$site->slug/create-smdemand",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'submaterialDemandForm',
                            'role' => 'form'
                            ]) !!}

                        @include('tekil._mat-inv-arr')

                        <br>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label for="firm">Sözleşme Tutarı: </label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" name="contract_cost" class="form-control number text-right"
                                           placeholder="Sözleşme tutarını giriniz">

                                    <span></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-flat">Talep Et</button>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>


            </div>
        </div>
    @endif

    <div id="deleteSubcontractorConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Talep Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/del-smdemand",
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'subDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-flat btn-warning">Sil</button>
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>
@stop