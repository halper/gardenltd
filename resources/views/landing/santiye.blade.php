<?php
use App\Site;

$can_create_site = false;
$can_edit_site = false;
if (Auth::user()->isAdmin() || Auth::user()->canViewAllSites()) {
    $santiyeler = Site::getSites();
} else {
    $santiyeler = Auth::user()->site()->get();
    foreach (Auth::user()->group()->get() as $user_group) {
        foreach ($user_group->site()->get() as $group_site) {
            $santiyeler->push($group_site);

        }
    }

    foreach (Auth::user()->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('santiye-ekle')) {
            $can_create_site = true;
        }
        if ($group->hasSpecialPermissionForSlug('santiye-duzenle')) {
            $can_edit_site = true;
        }
    }
}






?>

@extends('landing.landing')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/js/angular/css/xeditable.css" rel="stylesheet"/>

@stop

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-editable.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/angular/js/xeditable.min.js" type="text/javascript"></script>

    <script>
        if ($('.has-error')[0]) {
            $('#addNewSite').modal('show');
        }

        $('#siteInsertForm').on('submit', function (e) {
            var helper = $('p.helper');
            helper.removeClass('text-danger');
            helper.text();
            var citySelect = $('select[name="city_id"]');
            citySelect.parent().parent().parent().removeClass('has-error');
            if (citySelect.val() == null) {
                citySelect.parent().parent().parent().addClass('has-error');
                helper.addClass('text-danger');
                helper.text('İlgili alanları doldurunuz!');
                e.preventDefault();

            }

        });
        $(document).on("click", ".siteDelBut", function (e) {

            e.preventDefault();
            var mySiteId = $(this).data('id');
            var mySiteName = $(this).data('name');
            var myForm = $('.modal-footer #siteDeleteForm');
            var myP = $('.modal-body .siteDel');
            myP.html("<em>" + mySiteName + "</em> şantiyesini silmek istediğinizden emin misiniz?" +
                    "<p>NOT: <span>SİLME İŞLEMİ GERİ DÖNDÜRÜLEMEZ!</span></p>");
            $('<input>').attr({
                type: 'hidden',
                name: 'siteDeleteIn',
                value: mySiteId
            }).appendTo(myForm);
            $('#deleteSiteConfirm').modal('show');
        });


        $.fn.editable.defaults.mode = 'popup';
        String.prototype.turkishToLower = function () {
            var string = this;
            var letters = {"İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç"};
            string = string.replace(/(([İIŞĞÜÇÖ]))/g, function (letter) {
                return letters[letter];
            });
            return string.toLowerCase();
        };

        var stockApp = angular.module('stockApp', ["xeditable"], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).run(function (editableOptions) {
            editableOptions.theme = 'bs3';
        }).controller('StockController', function ($scope, $http, $filter) {
            $scope.stockTable = true;
            $scope.placeholder = "Şantiye ya da demirbaş yazınız";
            $scope.stocks = [];
            $scope.items = [];
            $scope.mySearch = '';
            $scope.showStockTable = function (myBool) {
                $scope.stockTable = myBool;
                $scope.placeholder = myBool ? "Şantiye ya da demirbaş yazınız" : "Demirbaş yazınız";
                $scope.mySearch = '';
            };

            $http.get("<?=URL::to('/');?>/santiye/retrieve-items")
                    .then(function (response) {
                        $scope.items = response.data;
                    });
            $scope.getStocks = function () {
                $http.get("<?=URL::to('/');?>/santiye/retrieve-stocks")
                        .then(function (response) {
                            $scope.stocks = response.data;
                        });
            };

            $scope.updateStock = function (stock, data) {
                return $http.post("<?=URL::to('/');?>/santiye/modify-stock", {
                    site: stock.site_id,
                    stock: stock.stock_id,
                    detail: data
                });
            }

        }).filter('trUp', function () {
            return function (data) {
                if (data) {
                    data = data.replace(/ı/g, 'I');
                    data = data.replace(/i/g, 'İ');
                    return data.toUpperCase();
                }
            }
        }).filter('searchFor', function () {
            return function (arr, searchStr) {
                if (!searchStr) {
                    return arr;
                }
                var result = [];
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if ((item.site + ' ' + item.st).turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        }).filter('searchForItem', function () {
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
        });

        $(document).ready(function () {
            angular.element('#stockApp').scope().getStocks();
            $('.inline-edit').editable({
                validate: true
            });
        });

    </script>
@stop

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger fade in alert-box">
            Yeni şantiye eklenirken hata oluştu!
            <a href="#" class="close" data-dismiss="alert">&times;</a>
        </div>
    @endif
    @if(isset($santiyeler) && count($santiyeler) > 0)

        <div class="callout callout-info">
            <h4>Şantiyeler sayfası</h4>

            <p>
                {{Auth::user()->isAdmin() ?
                "İşlem yapmak istediğiniz şantiyeyi seçebilir ya da yeni şantiye oluşturabilirsiniz." :
                "İşlem yapmak istediğiniz şantiyeyi seçiniz."
            }}
            </p>
        </div>
    @else
        <div class="callout callout-danger">
            <h4>Uyarı</h4>

            <p>
                Yöneticinizin sizi bir şantiyeye ataması gerekmektedir.
            </p>
        </div>
    @endif

    @if(Auth::user()->isAdmin() || $can_create_site)
        <div class="col-md-4">
            <a href="#" data-toggle="modal" data-target="#addNewSite">
                <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="ion ion-ios-plus-outline"></i></span>

                    <div class="info-box-content">
                        {{--<span class="info-box-text">Mentions</span>--}}
                        <span class="info-box-single">Yenİ şantİye ekle</span>

                    </div>
                    <!-- /.info-box-content -->
                </div>
            </a>
        </div>
    @endif



    @if(isset($santiyeler))
        @foreach($santiyeler as $santiye)
            <?php
            $start_date = date_create($santiye->start_date);
            $now = date_create();
            $end_date = date_create($santiye->end_date);
            $left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));
            $total = str_replace("+", "", date_diff($start_date, $end_date)->format("%R%a"));
            $passed = str_replace("+", "", date_diff($start_date, $now)->format("%R%a"));
            $total_per = floor((int)$passed * 100 / (int)$total);
            $total_per = $total_per >= 100 ? 100 : $total_per;

            ?>

            <div class="col-md-4">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-building-o"></i></span>

                    <div class="info-box-content">
                        @if(Auth::user()->isAdmin())

                            <a style="padding: 0 5px" href="#" class="close siteDelBut" data-toggle="modal"
                               data-id="{{$santiye->id}}" data-name="{{ $santiye->job_name}}"
                               data-target="#deleteSiteConfirm"><i
                                        class="fa fa-trash-o"></i></a>
                        @endif
                        @if(Auth::user()->isAdmin() || $can_edit_site)
                            <a class="close" href="/santiye-duzenle/{{$santiye->slug}}"><i class="fa fa-pencil"></i></a>


                        @endif
                        <span class="info-box-text">{{$santiye->job_name}}</span>
                        <span class="info-box-number">{{"Kalan süre: " . ($left < 0 ? 0 : $left) ." gün"}}</span>

                        <div class="progress">
                            <div class="progress-bar" {!!
                            "style=\"width: $total_per%\"" !!}>
                            </div>
                        </div>
                        <a href={{ "tekil/$santiye->slug" }} class="details">

                  <span class="progress-description">
                    Şantiye detayları için tıklayınız
                      <i class="fa fa-arrow-circle-right"></i>
                  </span>
                        </a>

                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
        @endforeach
    @endif

    @if(Auth::user()->isAdmin())
        <div ng-app="stockApp">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Demirbaş Tabloları
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
                            <div ng-controller="StockController" id="stockApp">
                                <div class="row">
                                    <div class="col-md-4">
                                        <a href="#!" ng-click="showStockTable(true)">Demirbaş Dağılım Tablosu</a> | <a
                                                href="#!" ng-click="showStockTable(false)">Demirbaş Kalan Tablosu</a>
                                    </div>
                                    <div class="col-md-4 pull-right">
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                   name="stock" ng-model="mySearch"
                                                   value="" autocomplete="off"
                                                   placeholder="<%placeholder%>"/>
                                        <span class="input-group-addon add-on"><i
                                                    class="fa fa-search"></i></span>

                                        </div>

                                    </div>
                                </div>
                                <div class="table-responsive" ng-show="stockTable">
                                    <h4>Demirbaş Dağılım Tablosu</h4>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Şantiye</th>
                                            <th>Demirbaş</th>
                                            <th>Miktar (Toplam/Şantiye)</th>
                                            <th>Birim</th>
                                            <th>Açıklama</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="stock in stocks | searchFor:mySearch track by $index"
                                            my-repeat-directive>
                                            <td><% stock.site %></td>
                                            <td><% stock.st %></td>
                                            <td><% stock.total %>/<% stock.amount %></td>
                                            <td><% stock.unit %></td>
                                            <td>
                                                <a href="#" class="inline-edit" data-type="text"
                                                   editable-textarea="stock.detail" e-rows="3" e-cols="30"
                                                   onbeforesave="updateStock(stock, $data)">
                                                    <% stock.detail || "Açıklama Yok" %>
                                                </a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-responsive" ng-show="!stockTable">
                                    <h4>Demirbaş Kalan Tablosu</h4>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Demirbaş</th>
                                            <th>Kalan Miktar (Toplam/Kalan)</th>
                                            <th>Birim</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr ng-repeat="item in items | searchForItem:mySearch track by $index"
                                            my-repeat-directive>
                                            <td><% item.name %></td>
                                            <td><% item.total %>/<% item.left %></td>
                                            <td><% item.unit %></td>

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
    @endif

    <div id="addNewSite" class="modal fade" role="dialog" tabindex="-1"
         aria-labelledby="şantiye eklemek için açılır form"
         aria-hidden="true">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Yeni Şantiye Ekle</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open([
                    'url' => '/santiye/add',
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'siteInsertForm',
                    'role' => 'form'
                    ])!!}
                    <p class="helper"></p>
                    @include('landing._santiye-add-form', ['santiye' => 'true'])


                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">Şantiye Ekle</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

    <div id="deleteSiteConfirm" class="modal modal-danger fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Şantiye Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="siteDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => '/santiye/del',
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'siteDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-outline">Sil</button>
                    <button type="button" class="btn btn-outline" data-dismiss="modal">Vazgeç</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>
@stop

