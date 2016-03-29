<?php

$site_reports = $site->report()->with('photo', 'receipt')->orderBy('created_at', 'DESC')->get();
$tags = DB::table('tags')->select('id', 'name')->get();
$my_arr = [];
foreach ($tags as $tag) {
    array_push($my_arr, [
            'id' => $tag->id,
            'name' => $tag->name]);
}
$tags = json_encode($my_arr);

?>

@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>

@endsection



@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js" type="text/javascript"></script>
    <script>
        $(".js-example-basic-multiple").select2({
            width: 570,
            placeholder: "Etiket seçiniz",
            closeOnSelect: false,
            allowClear: true
        });
        $(".js-example-basic-multiple").on('select2:close', function (e) {
            var $this = $(this);
            var $id = $(this).data('id');
            $.post("/tekil/{{$site->slug}}/attach-tag", {
                        tags: $(this).val(),
                        fileid: $id
                    })
                    .done(function () {
                        $this.next('span').next('.success-message').html('<p class="alert-success">Kayıt başarılı!</p>');
                        $('p.alert-success').not('.alert-important').delay(4500).slideUp(300);
                    });
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
            var $fileId = $(this).data('id');
            $.post("{{"/tekil/$site->slug/delete-files"}}",
                    {
                        fileid: $fileId
                    })
                    .done(function () {
                        $('#tr-st-' + $fileId).remove();
                    });
        });

        String.prototype.turkishToLower = function () {
            var string = this;
            var letters = {"İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç"};
            string = string.replace(/(([İIŞĞÜÇÖ]))/g, function (letter) {
                return letters[letter];
            });
            return string.toLowerCase();
        };

        var attachmentApp = angular.module('attachmentApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('AttachmentController', function ($scope, $http) {
            $scope.data = null;
            $scope.photos = [];
            $scope.receipts = [];
            $scope.name = '';
            $scope.loading = false;

            $scope.getPhotos = function () {
                $scope.loading = true;
                $http.get("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-photos"
                ).then(function (response) {
                    $scope.photos = response.data;
                }).finally(function () {
                    $scope.loading = false;
                });
            };
            $scope.getReceipts = function () {
                $scope.loading = true;
                $http.get("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-receipts"
                ).then(function (response) {
                    $scope.receipts = response.data;
                }).finally(function () {
                    $scope.loading = false;
                });
            };
            $scope.getPhotos();
            $scope.getReceipts();
        }).filter('searchFor', function () {
            return function (arr, searchStr) {
                if (!searchStr) {
                    return arr;
                }
                var result = [];
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if ((item.name + ' ' + item.tags).turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        });

        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

    </script>
@endsection



@section('content')

    <div ng-app="attachmentApp" ng-controller="AttachmentController" id="angAttachment">
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_5" data-toggle="tab">Dökümanlar Listele</a></li>
                        <li><a href="#tab_1" data-toggle="tab">Dökümanları Güncelle</a></li>

                    </ul>

                    <!-- /.tab-content -->
                    <div class="tab-content">


                        <div class="tab-pane active" id="tab_5">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-8 col-md-4">
                                        <div class="input-group">
                                            <input type="text" style="width: 100%"
                                                   name="search" ng-model="name"
                                                   value=""
                                                   placeholder="Dosya adı veya etiket giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" ng-show="photos.length > 0">
                                <div class="col-xs-12 col-md-12">
                                    <div class="box box-primary box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Fotoğraflar
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
                                            <div class="row">
                                                <div class="col-sm-4"
                                                     ng-repeat="photo in photos | searchFor:name track by $index">
                                                    <a ng-href="<%photo.image%>"
                                                       data-toggle="lightbox" data-gallery="reportsitephotos"
                                                       data-title="<%photo.name%>"
                                                       data-footer="<strong>ETİKETLER: </strong><%photo.tags%>"
                                                       class="col-sm-4">
                                                        <img ng-src="<%photo.image%>" class="img-responsive">
                                                        <%photo.name%>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" ng-show="receipts.length > 0">
                                <div class="col-xs-12 col-md-12">
                                    <div class="box box-primary box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Faturalar
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
                                            <div class="row">
                                                <div class="col-sm-4"
                                                     ng-repeat="receipt in receipts | searchFor:name track by $index">
                                                    <a ng-href="<%receipt.image%>"
                                                       data-toggle="lightbox" data-gallery="reportsitereceipts"
                                                       class="col-sm-4">
                                                        <img ng-src="<%receipt.image%>" class="img-responsive">
                                                        <%receipt.name%>
                                                    </a>

                                                    <p><%receipt.tags%></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="tab_1">
                            <?php
                            $site_reports = $site->report()->with('photo', 'receipt')->get();
                            ?>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th>Rapor Tarihi</th>
                                    <th>Adı</th>
                                    <th>Tipi</th>
                                    <th class="col-sm-6">Etiketler</th>
                                    <th style="text-align: center">İşlemler</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($site_reports as $site_report)
                                    @foreach ($site_report->photo as $site_photo)
                                        @if (count($site_photo->file))
                                            <?php
                                            $report_date = \App\Library\CarbonHelper::getTurkishDate($site_report->created_at);
                                            $tags = '';
                                            $i = 0;
                                            foreach ($site_photo->file()->first()->tag as $tag) {
                                                $tags .= $tag->name;
                                                if ($i + 1 < count($site_photo->file()->first()->tag)) {
                                                    $tags .= ', ';
                                                }
                                                $i++;
                                            }
                                            $file_name = $site_photo->file()->first()->name;
                                            $id = $site_photo->file()->first()->id;
                                            ?>
                                            <tr id="tr-st-{{$id}}">
                                                <td>{{$report_date}}</td>
                                                <td>{{$file_name}}</td>
                                                <td>Fotoğraf</td>
                                                <td>
                                                    <select name="tags-{{$site_photo->file()->first()->id}}[]"
                                                            class="js-example-basic-multiple form-control"
                                                            multiple data-id="{{$id}}">
                                                        @foreach(\App\Tag::all() as $tag)
                                                            <?php
                                                            $selected = '';
                                                            foreach ($site_photo->file()->first()->tag as $ptag) {
                                                                if ((int)$tag->id == (int)$ptag->id) {
                                                                    $selected = "selected";
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <option value="{{$tag->id}}" {{$selected}}>{{$tag->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="success-message"></span>
                                                </td>

                                                <td style="text-align: center">
                                                    <a href="#" class="btn btn-flat btn-danger btn-sm btn-approve"
                                                       data-id="{{$id}}">
                                                        Sil
                                                    </a>

                                                    <div class="row" style="display: none;">
                                                        <div class="col-sm-6">
                                                            <a href="#" class="text-danger btn-remove-sm"
                                                               data-id="{{$id}}"><i
                                                                        class="fa fa-check"></i>Evet </a>

                                                        </div>
                                                        <div class="col-sm-6">
                                                            <a href="#" class="text-primary btn-cancel-sm"><i
                                                                        class="fa fa-times"></i>Hayır</a>
                                                        </div>
                                                    </div>

                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                @foreach ($site_reports as $site_report)
                                    @foreach ($site_report->receipt as $site_receipt)
                                        @if (count($site_receipt->file))
                                            <?php
                                            $report_date = \App\Library\CarbonHelper::getTurkishDate($site_report->created_at);
                                            $tags = '';
                                            $i = 0;
                                            foreach ($site_receipt->file()->first()->tag as $tag) {
                                                $tags .= $tag->name;
                                                if ($i + 1 < count($site_receipt->file()->first()->tag)) {
                                                    $tags .= ', ';
                                                }
                                                $i++;
                                            }
                                            $file_name = $site_receipt->file()->first()->name;
                                            $id = $site_receipt->file()->first()->id;
                                            ?>
                                            <tr id="tr-st-{{$id}}">
                                                <td>{{$report_date}}</td>
                                                <td>{{$file_name}}</td>
                                                <td>Fatura</td>
                                                <td>
                                                    <select name="tags-{{$site_receipt->file()->first()->id}}[]"
                                                            class="js-example-basic-multiple form-control"
                                                            multiple data-id="{{$id}}">
                                                        @foreach(\App\Tag::all() as $tag)
                                                            <?php
                                                            $selected = '';
                                                            foreach ($site_receipt->file()->first()->tag as $ptag) {
                                                                if ((int)$tag->id == (int)$ptag->id) {
                                                                    $selected = "selected";
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <option value="{{$tag->id}}" {{$selected}}>{{$tag->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="success-message"></span>
                                                </td>

                                                <td style="text-align: center">
                                                    <a href="#" class="btn btn-flat btn-danger btn-sm btn-approve"
                                                       data-id="{{$id}}">
                                                        Sil
                                                    </a>

                                                    <div class="row" style="display: none;">
                                                        <div class="col-sm-6">
                                                            <a href="#" class="text-danger btn-remove-sm"
                                                               data-id="{{$id}}"><i
                                                                        class="fa fa-check"></i>Evet </a>

                                                        </div>
                                                        <div class="col-sm-6">
                                                            <a href="#" class="text-primary btn-cancel-sm"><i
                                                                        class="fa fa-times"></i>Hayır</a>
                                                        </div>
                                                    </div>

                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection