<?php
use Carbon\Carbon;

$today = Carbon::now()->toDateString();
?>

@extends('tekil/layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/bootstrap-editable.css" rel="stylesheet"/>

@stop

@section('page-specific-js')

    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-editable.min.js" type="text/javascript"></script>
    <script>
        $.fn.editable.defaults.mode = 'inline';
        $(document).ready(function () {
            $('.inline-edit').editable({
                validate: true
            });
        });
        $(document).on("click", ".subDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #subDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> demirbaşını silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'id',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteSubcontractorConfirm').modal('show');
        });

        $.get("<?=URL::to('/');?>/tekil/{{$site->slug}}/retrieve-stocks",
                function (data) {
                    $(".js-example-data-array").select2({
                        placeholder: 'Şantiyeye kayıt yapmak istediğiniz demirbaşı seçiniz',
                        data: data
                    }).trigger("change");
                    $(".js-example-data-array").prop("disabled", false);
                }
        );
        $(".js-example-data-array").empty().select2({
            placeholder: 'Demirbaş listesi yükleniyor...'
        });
        $(".js-example-data-array").prop("disabled", true);

        $('#stockRegistrationForm').submit(function (e) {
            var emptyTexts = $('#stockRegistrationForm .form-control').filter(function () {
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
                    <li class="active"><a href="#tab_5" data-toggle="tab">Demirbaş Kayıt</a></li>
                    <li><a href="#tab_1" data-toggle="tab">Demirbaş Listele</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">


                    <div class="tab-pane active" id="tab_5">

                        @include('tekil._new-stock')
                    </div>


                    <div class="tab-pane" id="tab_1">
                        @include('tekil._view-stock')
                    </div>


                </div>
            </div>
        </div>
    </div>



    @if(isset($stock_array))
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Demirbaş Kayıt Formu</h3>

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
                            'url' => "/tekil/$site->slug/register-stocks",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'stockRegistrationForm',
                            'role' => 'form'
                            ]) !!}
                        <div class="table-responsive">
                            <table class="table table-condensed table-bordered">
                                <thead>
                                <tr>
                                    <th> Demirbaş</th>
                                    <th> Birim</th>
                                    <th class="text-right"> Miktar</th>
                                </tr>
                                </thead>
                                <tbody>

                                @for($i = 0; $i<sizeof($stock_array); $i++)
                                    <?php
                                    $mat = \App\Stock::find($stock_array[$i]['stock'])
                                    ?>
                                    <tr>
                                        <td>
                                            {{$mat->name}}
                                            <input type="hidden" name="stocks[]"
                                                   value="{{$mat->id}}">
                                        </td>
                                        <td>
                                            {{$mat->unit}}
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                {!! Form::number('quantity[]', null, ['class' =>
                                                'form-control text-right', 'autocomplete' => 'off', 'max' => $stock_array[$i]['left'],
                                                'placeholder' => $mat->name." demirbaş miktarını giriniz"]) !!} (En
                                                fazla {{$stock_array[$i]['left'] . " " . $mat->unit}} istekte
                                                bulunabilirsiniz.)
                                                <span></span>
                                            </div>
                                        </td>

                                    </tr>
                                @endfor
                                </tbody>
                            </table>

                        </div>
                        <br>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
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
                    <h4 class="modal-title">Demirbaş Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/del-site-stock",
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