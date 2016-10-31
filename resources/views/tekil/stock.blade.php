<?php
use Carbon\Carbon;


$today = Carbon::now()->toDateString();


$user = Auth::user();

$addr = explode("/", $_SERVER['REQUEST_URI']);
$slug = $addr[sizeof($addr) - 1];
$module = $modules->whereSlug($slug)->first();

$post_permission = \App\Library\PermissionHelper::checkUserPostPermissionOnModule($user, $module);
?>

@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/bootstrap-editable.css" rel="stylesheet"/>

@stop

@section('page-specific-js')

    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/combodate.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-editable.min.js" type="text/javascript"></script>
    <script>
        $.fn.editable.defaults.mode = 'inline';
        $(function () {
            $('.dob').editable({
                format: 'YYYY-MM-DD',
                viewformat: 'DD.MM.YYYY',
                template: 'DD / MMM / YYYY',
                combodate: {
                    minYear: 2015,
                    maxYear: 2020,
                    minuteStep: 1
                }
            });
        });

        $(document).ready(function () {
            moment.locale('tr');
            $('.inline-edit').editable({
                validate: true
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
            var $stockTId = $(this).data('id');
            $.post("{{"/tekil/$site->slug/del-site-stock"}}",
                    {
                        id: $stockTId
                    })
                    .done(function () {
                        $('#tr-st-' + $stockTId).remove();
                    });
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
                    @if($post_permission)
                        <li class="active"><a href="#tab_5" data-toggle="tab">Demirbaş Kayıt</a></li>
                    @endif
                    <li class="{{!$post_permission ? "active" : ""}}"><a href="#tab_1" data-toggle="tab">Demirbaş
                            Listele</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">

                    @if($post_permission)
                        <div class="tab-pane active" id="tab_5">
                            @include('tekil._new-stock')
                        </div>
                    @endif


                    <div class="tab-pane {{!$post_permission ? "active" : ""}}" id="tab_1">
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
                                    <th class="col-sm-1"> Demirbaş</th>
                                    <th class="col-sm-1"> Birim</th>
                                    <th class="text-right col-sm-2"> Miktar</th>
                                    <th class="col-sm-2 text-center">Giriş Tarihi</th>
                                    <th class="col-sm-2 text-center">Çıkış Tarihi</th>
                                    <th class="col-sm-4">Açıklama</th>
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
                                                'placeholder' =>"Miktar giriniz"]) !!}
                                                (Max: {{$stock_array[$i]['left'] . " " . $mat->unit}})
                                                <span></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-append date dateRangePicker">
                                                <input type="text" class="form-control" name="entry_date[]"
                                                       placeholder="Demirbaş giriş" value="">
                                                <span class="input-group-addon add-on"><span
                                                            class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-append date dateRangePicker">
                                                <input type="text" class="form-control" name="exit_date[]"
                                                       placeholder="Demirbaş çıkış" value="">
                                                <span class="input-group-addon add-on"><span
                                                            class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input class="form-control" autocomplete="off" type="text"
                                                       name="site_detail[]">
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


@stop