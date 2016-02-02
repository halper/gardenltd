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
        $("#dateRangePicker > input").val("{{\App\Library\CarbonHelper::getTurkishDate($demand->demand_date)}}");


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
            $.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-material",
                    {
                        'id': $matId,
                        'did': '{{$demand->id}}'
                    },
                    function (data) {
                        $('#tr-mat-' + $matId).remove();
                    });
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
@endsection

@extends('tekil.layout')

@section('content')
    <h4>{{$demand->demand}}</h4>

    @include('tekil._new-demand')
    <br>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open([
                                'url' => "/tekil/$site->slug/update-demand",
                                'method' => 'POST',
                                'class' => 'form',
                                'id' => 'submaterialDemandForm',
                                'role' => 'form'
                                ]) !!}
            <input type="hidden" name="did" value="{{$demand->id}}">

            <div class="table-responsive">
                <table class="table table-condensed table-bordered">
                    <thead>
                    <tr>
                        <th> Malzeme</th>
                        <th> Birim</th>
                        <th class="text-right"> Miktar</th>
                        <th class="text-right"> Birim Fiyat</th>
                        <th> Ödeme Şekli</th>
                        <th class="text-center"> Malzeme Çıkar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($demand->materials()->get() as $mat)
                        <tr id="tr-mat-{{$mat->id}}">
                            <td>
                                {{$mat->material}}
                                <input type="hidden" name="materials[]"
                                       value="{{$mat->id}}">
                            </td>
                            <td>
                                <div class="form-group">
                                    {!! Form::text('unit[]', $mat->pivot->unit, ['class' => 'form-control', 'placeholder'
                                    => $mat->material." malzemesinin birimini giriniz"])
                                    !!}
                                    <span></span>

                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    {!! Form::input('text', 'quantity[]', \App\Library\TurkishChar::convertToTRcurrency($mat->pivot->quantity), ['class' =>
                                    'form-control number text-right',
                                    'placeholder' => $mat->material." malzemesinin birim cinsinden miktarını giriniz"]) !!}
                                    <span></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    {!! Form::input('text', 'price[]', \App\Library\TurkishChar::convertToTRcurrency($mat->pivot->price), ['class' =>
                                    'form-control number text-right',
                                    'placeholder' => $mat->material." malzemesinin birim fiyatını giriniz"]) !!}
                                    <span></span>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    {!! Form::input('text', 'payment_type[]', $mat->pivot->payment_type, ['class' =>
                                    'form-control',
                                    'placeholder' => $mat->material." malzemesinin ödeme şeklini giriniz"]) !!}
                                    <span></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-flat btn-danger btn-approve">Malzemeyi Çıkar</a>

                                <div class="row hidden">
                                    <div class="col-sm-6">
                                        <a href="#" class="text-danger btn-remove-sm" data-id="{{$mat->id}}"><i
                                                    class="fa fa-check"></i>Evet </a>

                                    </div>
                                    <div class="col-sm-6">
                                        <a href="#" class="text-primary btn-cancel-sm"><i
                                                    class="fa fa-times"></i>Hayır</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            @if(isset($material_array))
                @include('tekil._demand-mat-arr')
            @endif

            <br>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-2">
                        <label for="firm">Bağlantı Yapılan Firma: </label>
                    </div>
                    <div class="col-sm-10">
                        {!! Form::text('firm', $demand->firm, ['class' => 'form-control', 'placeholder'
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
                        {!! Form::textarea('details', $demand->details, ['class' => 'form-control', 'placeholder'
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


@endsection

