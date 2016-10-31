<?php
use Carbon\Carbon;
$personnel = $subcontractor->personnel()->get();
$today = \App\Library\CarbonHelper::getTurkishDate(Carbon::now()->toDateString());

if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = '';
}

$user = Auth::user();
$can_access_contract_info_tab = false;
$can_access_fees_tab = false;
$can_access_additional_docs_tab = false;
$can_access_add_personnel_tab = false;
$can_add_employer_docs = false;
$can_edit_personnel = false;
$can_delete_personnel = false;


if ($user->isAdmin()) {

    $can_access_contract_info_tab = true;
    $can_access_fees_tab = true;
    $can_access_additional_docs_tab = true;
    $can_access_add_personnel_tab = true;
    $can_add_employer_docs = true;
    $can_edit_personnel = true;
    $can_delete_personnel = true;
} else
    foreach ($user->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('sozlesme-bilgileri')) {
            $can_access_contract_info_tab = true;
        }
        if ($group->hasSpecialPermissionForSlug('ucret-oranlari')) {
            $can_access_fees_tab = true;
        }
        if ($group->hasSpecialPermissionForSlug('ek-belgeler')) {
            $can_access_additional_docs_tab = true;
        }
        if ($group->hasSpecialPermissionForSlug('personel-ekle')) {
            $can_access_add_personnel_tab = true;
        }
        if ($group->hasSpecialPermissionForSlug('ise-giris-belgesi-ekle')) {
            $can_add_employer_docs = true;
        }
        if ($group->hasSpecialPermissionForSlug('personel-duzenle')) {
            $can_edit_personnel = true;
        }
        if ($group->hasSpecialPermissionForSlug('personel-sil')) {
            $can_delete_personnel = true;
        }
    }
?>
@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link href="<?= URL::to('/'); ?>/css/dropzone.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>

@stop

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/dropzone.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/personnel.js" type="text/javascript"></script>

    <script>
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
            $scope.date = '{{$today}}';
            $scope.today = moment().format('DD.MM.YYYY');
            $scope.payments = [];
            $scope.debt = '';
            $scope.balance = '';
            $scope.claim = '';
            $scope.subId = '{{$subcontractor->id}}';
            $scope.paymentSelect = '';
            $scope.showPayment = false;

            $scope.getPayments = function () {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-payments", {
                    subId: $scope.subId
                }).then(function (response) {
                    $scope.payments = response.data.payments;
                    $scope.balance = response.data.balance;
                    $scope.claim = response.data.claim;
                    $scope.debt = response.data.debt;
                    $scope.showPayment = parseFloat($scope.claim.replace(',', '.')) * 0.9 <= parseFloat($scope.debt.replace(',', '.'));
                });
            };
            $scope.getPayments();

            $scope.addPayment = function () {
                if (!$scope.date || !$scope.paymentSelect || !$scope.amount || !$scope.method) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: Tarih, ödeme cinsi, ödeme şekli, fiyat!'
                }
                else {
                    $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/add-payment", {
                        payment_date: $scope.date,
                        subId: $scope.subId,
                        amount: $scope.amount,
                        method: $scope.method,
                        name: $scope.paymentSelect,
                        detail: $scope.detail
                    }).then(function (response) {
                        $scope.amount = '';
                        $scope.method = '';
                        $scope.paymentSelect = '';
                        $scope.detail = '';
                        $scope.date = $scope.today;
                        $scope.getPayments();
                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-payment", {
                    id: item.id
                }).then(function () {
                    $scope.getPayments();
                    $scope.date = item.date;
                    $scope.paymentSelect = item.type;
                    $scope.method = item.method;
                    $scope.detail = item.detail;
                    $scope.amount = item.debt;
                });

            };
            $scope.name = '';
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
                    if ((item.date + ' ' + item.type + ' ' + item.method + ' ' + item.detail).turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        });
        $(document).on("click", ".userDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #userDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> adlı kullanıcıyı silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'userDeleteIn',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteUserConfirm').modal('show');
        });

        $('a[href=#tab_6]').on("shown.bs.tab", function () {
            $(".staff-select").select2({
                placeholder: "İş kolu seçiniz",
                allowClear: true
            });
        });


        function removeFiles(fid) {
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-subcontractor-files"}}',
                data: {
                    "fileid": fid
                }
            }).success(function () {
                var linkID = "lb-link-" + fid;
                $('#' + linkID).remove();
            });

        }

        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

        Dropzone.options.fileInsertForm = {
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, response) {
                    file.serverId = response.id;
                });
                this.on("removedfile", function (file) {
                    var name = file.name;

                    $.ajax({
                        type: 'POST',
                        url: '{{"/tekil/$site->slug/delete-subcontractor-files"}}',
                        data: {
                            "fileid": file.serverId
                        }
                    });
                });
            }
        };

        $(".js-example-basic-multiple").select2({
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });
        $('.dateRangePicker').datepicker({
            language: 'tr',
            autoclose: true
        });
        $(".dateRangePicker > input#payment_date").val("{{$today}}");
    </script>
@stop

@section('content')
    <h2>{{$subcontractor->subdetail->name}}</h2>
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li {{empty($tab) ? 'class=active' : ''}}><a href="#tab_1" data-toggle="tab">Alt Yüklenici Sözleşme
                            Bilgileri</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Ücretler ve Oranlar</a></li>
                    <li><a href="#tab_5" data-toggle="tab">Ek Belgeler</a></li>
                    <li {{strpos($tab, 'insert') !== false ? 'class=active' : ''}}><a href="#tab_6" data-toggle="tab">Personel
                            Ekle</a></li>
                    <li><a href="#tab_7" data-toggle="tab">Personel Düzenle</a></li>

                </ul>
                <div class="tab-content">
                    @if($can_access_contract_info_tab)
                        <div class="tab-pane {{empty($tab) ? 'active' : ''}}" id="tab_1">

                            {!! Form::model($subcontractor, [
                                                                            'url' => "/tekil/$site->slug/update-subcontractor",
                                                                            'method' => 'POST',
                                                                            'class' => 'form',
                                                                            'id' => 'subcontractorEditForm',
                                                                            'role' => 'form',
                                                                            'files' => true
                                                                            ])!!}
                            {!! Form::hidden('sub-id', $subcontractor->id) !!}
                            @include('tekil._subcontractor-form')

                            <div class="row">
                                <div class="col-sm-2"><strong>Sözleşme: </strong></div>
                                <div class="col-sm-10">
                                    <?php
                                    $my_path = '';
                                    $file_name = '';

                                    if (!($subcontractor->contract()->get()->isEmpty()) && !($subcontractor->contract->file()->get()->isEmpty())) {
                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $subcontractor->contract->file->path);
                                        $file_name = $subcontractor->contract->file->name;
                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                    }
                                    ?>
                                    <a href="{{!empty($my_path) ? $my_path : ""}}">
                                        {{!empty($file_name) ? $file_name : ""}}
                                    </a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-md-offset-4">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-flat btn-primary btn-block">Sözleşme
                                            Detaylarını
                                            Kaydet
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                        {{--Tab pane--}}
                        @endif

                        @if($can_access_fees_tab)
                                <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_2">
                            <div class="row">
                                <div class="col-xs-12">
                                    @include('tekil._subcontractor-fee-form')
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($can_access_additional_docs_tab)
                                <!-- /.tab-pane -->
                        <div class="tab-pane" id="tab_5">
                            <div class="row">
                                <div class="col-xs-12">
                                    @include('tekil._subcontractor-files')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($can_access_add_personnel_tab)
                        <div class="tab-pane {{strpos($tab, 'insert') !== false ? 'active' : ''}}" id="tab_6">
                            {!! Form::open([
                                                                            'url' => "/tekil/$site->slug/add-subcontractor-personnel",
                                                                            'method' => 'POST',
                                                                            'class' => 'form',
                                                                            'id' => 'personnelForm',
                                                                            'role' => 'form',
                                                                            'files' => true
                                                                            ])!!}
                            {!! Form::hidden('subcontractor_id', $subcontractor->id) !!}
                            <div class="row">
                                <div class="col-sm-12">

                                    @include('landing._personnel-insert-form', ['wage_exists' => true])

                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    @endif

                    @if($can_edit_personnel)
                        <div class="tab-pane" id="tab_7">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Adı-Soyadı</th>
                                            <th>TCK No</th>
                                            <th>Nüfus Cüzdanı</th>
                                            <th>İşe Giriş Belgesi</th>
                                            <th>Kullanıcı İşlemleri</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($personnel as $per)
                                            <tr>
                                                <td>{{ \App\Library\TurkishChar::tr_camel($per->name) }}</td>
                                                <td>{{ $per->tck_no }}</td>
                                                <?php
                                                $id_path = '';
                                                $id_file_name = '';
                                                $iddoc = '-';

                                                if (count($per->iddoc) && count($per->iddoc->file()->get())) {
                                                    $id_path_arr = explode(DIRECTORY_SEPARATOR, $per->iddoc->file()->orderBy('created_at', 'DESC')->first()->path);
                                                    $id_file_name = $per->iddoc->file()->orderBy('created_at', 'DESC')->first()->name;
                                                    $id_path = "/uploads/" . $id_path_arr[sizeof($id_path_arr) - 1] . "/" . $id_file_name;
                                                    $iddoc = '<a href="' . $id_path . '">' . $id_file_name . '</a>';
                                                }


                                                $my_path = '';
                                                $file_name = '';
                                                $cont = '-';

                                                if (count($per->contract) && count($per->contract->file()->get())) {

                                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $per->contract->file()->orderBy('created_at', 'DESC')->first()->path);
                                                    $file_name = $per->contract->file()->orderBy('created_at', 'DESC')->first()->name;

                                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                                    $cont = '<a href="' . $my_path . '">' . $file_name . '</a>';
                                                }
                                                $exit_date = $per->contract()->get()->isEmpty() || (!($per->contract()->get()->isEmpty()) && (strpos($per->contract->exit_date, '0000-00-00') !== false)) ? null : \App\Library\CarbonHelper::getTurkishDate($per->contract->exit_date);
                                                ?>

                                                <td>{!! $iddoc!!}</td>
                                                <td>{!! $cont !!}</td>

                                                <td>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <a href="{{"$subcontractor->id/personel-duzenle/$per->id"}}"
                                                               class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                        </div>
                                                        @if($can_delete_personnel)
                                                            <div class="col-sm-2">
                                                                <?php
                                                                echo '<button type="button" class="btn btn-flat btn-danger btn-sm userDelBut" data-id="' . $per->id . '" data-name="' . $per->name . '" data-toggle="modal" data-target="#deleteUserConfirm">Sil</button>';
                                                                ?>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>

    @include('tekil._subcontractor-cost-form')


    <div id="deleteUserConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Personel Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/del-personnel",
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'userDeleteForm',
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