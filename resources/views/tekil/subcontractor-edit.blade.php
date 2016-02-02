<?php
use Carbon\Carbon;
$personnel = $subcontractor->personnel()->get();
$today = \App\Library\CarbonHelper::getTurkishDate(Carbon::now()->toDateString());
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
    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/dropzone.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>

    <script>

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

        $("#add-personnel").on("click", function (e) {
            e.preventDefault();
            var tckInput = $('input[name=tck_no]');
            var tck = tckInput.val();
            if (tck.length != 11) {
                tckInput.parent('div').parent().closest('div.row').append(
                        '<div class="col-sm-4">' +
                        '<span class="text-danger">TCK No giriniz!</span>' +
                        '</div>'
                );
                tckInput.parent('div').parent().closest('div.row').addClass('has-error');
                return;
            }
            var unique;
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/check-tck"}}',
                data: {
                    "tck_no": tck
                }
            }).success(function (response) {
                unique = (response.indexOf('unique') > -1);
                if (!unique) {
                    tckInput.parent('div').parent().closest('div.row').append(
                            '<div class="col-sm-4">' +
                            '<span class="text-danger">TCK No sistemde kayıtlı!</span>' +
                            '</div>'
                    );
                    tckInput.parent('div').parent().closest('div.row').addClass('has-error');
                }
                else {
                    $('#subcontractorPersonnelForm').submit();
                }
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
        $(".dateRangePicker > input").val("{{$today}}");
    </script>
@stop

@section('content')
    <h2>{{$subcontractor->subdetail->name}}</h2>
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Alt Yüklenici Sözleşme Bilgileri</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Ücretler ve Oranlar</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Ek Ödemeler</a></li>
                    <li><a href="#tab_5" data-toggle="tab">Ek Belgeler</a></li>
                    <li><a href="#tab_6" data-toggle="tab">Personel Ekle</a></li>
                    <li><a href="#tab_7" data-toggle="tab">Personel Düzenle</a></li>

                </ul>
                <div class="tab-content">
                    <!-- /.tab-pane -->
                    <div class="tab-pane active" id="tab_1">

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

                                if (!empty($subcontractor->contract->first()) && !empty($subcontractor->contract->first()->file->first())) {
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $subcontractor->contract->first()->file->first()->path);
                                    $file_name = $subcontractor->contract->first()->file->first()->name;
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


                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-fee-form')
                            </div>
                        </div>
                    </div>


                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-cost-form')
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_5">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-files')
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_6">
                        {!! Form::open([
                                                                        'url' => "/tekil/$site->slug/add-subcontractor-personnel",
                                                                        'method' => 'POST',
                                                                        'class' => 'form',
                                                                        'id' => 'subcontractorPersonnelForm',
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

                    <div class="tab-pane" id="tab_7">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Adı-Soyadı</th>
                                        <th>TCK No</th>
                                        <th>Kullanıcı İşlemleri</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($personnel as $per)
                                        <tr>
                                            <td>{{ \App\Library\TurkishChar::tr_camel($per->name) }}</td>
                                            <td>{{ $per->tck_no }}</td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <a href="{{"$subcontractor->id/personel-duzenle/$per->id"}}"
                                                           class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <?php
                                                        echo '<button type="button" class="btn btn-flat btn-danger btn-sm userDelBut" data-id="' . $per->id . '" data-name="' . $per->name . '" data-toggle="modal" data-target="#deleteUserConfirm">Sil</button>';
                                                        ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    @if(!($subcontractor->payment()->get()->isEmpty()))
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Yapılan Ödemeler Tablosu
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
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>TARİH</th>
                                    <th>ÖDEME</th>
                                    <th>MİKTAR</th>
                                    <th>ÖDEME TİPİ</th>
                                    <th>AÇIKLAMA</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($subcontractor->payment()->get() as $payment)
                                    <tr>
                                        <td>{{ \App\Library\CarbonHelper::getTurkishDate($payment->payment_date) }}</td>
                                        <td>{{ $payment->name }}</td>
                                        <td>{{ \App\Library\TurkishChar::convertToTRcurrency($payment->amount) }} TL</td>
                                        <td>{{ empty($payment->method) ? '-' : $payment->method }}</td>
                                        <td>{{ $payment->detail }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif

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
                    'url' => '/tekil/del-personnel',
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