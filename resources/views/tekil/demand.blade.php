<?php
use Carbon\Carbon;

$today = Carbon::now()->toDateString();
        if(Session::has('tab')){
            $tab = Session::get('tab');
        }
        else{
            $tab = '';
        }
?>

@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')

    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script>

        $(document).on("click", ".subDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #subDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> tarihli talebi silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'userDeleteIn',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteSubcontractorConfirm').modal('show');
        });

        $("#dateRangePicker > input").val("{{App\Library\CarbonHelper::getTurkishDate($today)}}");
        $('#dateRangePicker').datepicker({
            autoclose: true,
            language: 'tr'
        });


        $(".js-example-basic-multiple").select2({
            placeholder: "Eklemek istediğiniz malzemeleri seçiniz",
            allowClear: true
        });


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
                    <li {{empty($tab) ? 'class=active' : ''}}><a href="#tab_5" data-toggle="tab">Talep Oluştur</a></li>
                    <li {{$tab == 1 ? 'class=active' : ''}}><a href="#tab_1" data-toggle="tab">Talep Görüntüle</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">


                    <div class="tab-pane {{empty($tab) ? 'active' : ''}}" id="tab_5">
                        <h3>Yeni Talep</h3>

                        <p>Talebini yapacağınız malzemeleri aşağıdaki kutudan seçtikten sonra birim ve miktar
                            belirteceğiniz
                            ayrı bir tablo gelecek.</p>
                        @include('tekil._new-demand')
                    </div>


                    <div class="tab-pane {{$tab == 1 ? 'active' : ''}}" id="tab_1">
                        @include('tekil._view-demand')
                    </div>


                </div>
            </div>
        </div>
    </div>



    @if(isset($material_array))
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Malzeme Talep Formu</h3>

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
                            'url' => "/tekil/$site->slug/demand-materials",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'materialDemandForm',
                            'role' => 'form'
                            ]) !!}
                        @include('tekil._demand-mat-arr')
                        <br>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label for="firm">Bağlantı Yapılan Firma: </label>
                                </div>
                                <div class="col-sm-10">
                                    {!! Form::text('firm', null, ['class' => 'form-control', 'placeholder'
                                    => "Bağlantı yapılan firmayı giriniz"])
                                    !!}
                                    <span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label for="firm">Talep Tarihi: </label>
                                </div>
                                <div class="col-sm-10 date">
                                    <div class="input-group input-append date" id="dateRangePicker">
                                        <input type="text" class="form-control" name="demand_date">
                                            <span class="input-group-addon add-on"><span
                                                        class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label for="details">Talep Detayları: </label>
                                    {!! Form::textarea('details', null, ['class' => 'form-control', 'placeholder'
                                    => "Talep detaylarını giriniz", 'rows' => 4])
                                    !!}
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
                    'url' => "/tekil/$site->slug/del-demand",
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