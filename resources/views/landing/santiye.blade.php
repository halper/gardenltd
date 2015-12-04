<?php
use App\Site;


if (Auth::user()->isAdmin() || Auth::user()->canViewAllSites()) {
    $sites = Site::getSites();
}
else{
    $sites = Auth::user()->site()->get();
}

?>

@extends('landing.landing')

@section('page-specific-css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script>
    $('.dateRangePicker').datepicker({
    language: 'tr'
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
    @if(isset($sites) && count($sites) > 0)

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

    @if(Auth::user()->isAdmin())
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



    @if(isset($sites))
        @foreach($sites as $site)
            <?php
            $start_date = date_create($site->start_date);
            $now = date_create();
            $end_date = date_create($site->end_date);
            $left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));
            $total = str_replace("+", "", date_diff($start_date, $end_date)->format("%R%a"));
            $passed = str_replace("+", "", date_diff($start_date, $now)->format("%R%a"));
            $total_per = floor((int)$passed * 100 / (int)$total);

            ?>

            <div class="col-md-4">
                <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-building-o"></i></span>

                    <div class="info-box-content">
                        {!!
                        Auth::user()->isAdmin() ?
                        "<a href=\"#\" class=\"close siteDelBut\" data-toggle=\"modal\"
                        data-id=\"$site->id\" data-name= \"$site->job_name\" data-target=\"deleteSiteConfirm\"><i
                                class=\"fa fa-trash-o\"></i></a>" : ""
                        !!}
                        <span class="info-box-text">{{$site->job_name}}</span>
                        <span class="info-box-number">{{"Kalan süre: $left gün"}}</span>

                        <div class="progress">
                            <div class="progress-bar" {!!
                            "style=\"width: $total_per%\"" !!}>
                        </div>
                    </div>
                    <a href={{ "tekil/$site->slug" }} class="details">

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
                    'class' => 'form .form-horizontal',
                    'id' => 'siteInsertForm',
                    'role' => 'form'
                    ])!!}
                    <div class="form-group {{ $errors->has('job_name') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('job_name', 'İşin Adı: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('job_name', null, ['class' => 'form-control', 'placeholder' => 'İşin adını giriniz']) !!}

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('management_name') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('management_name', 'İdarenin Adı: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('management_name', null, ['class' => 'form-control', 'placeholder' =>
                                'İdarenin adını giriniz']) !!}

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('employer') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('employer', 'İşverenin Adı: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('employer', null, ['class' => 'form-control', 'placeholder' =>
                                'İşverenin adını giriniz']) !!}

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('building_control') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('building_control', 'Yapı Denetim: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('building_control', null, ['class' => 'form-control', 'placeholder' =>
                                'Yapı Denetim firması giriniz']) !!}

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('main_contractor') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('main_contractor', 'Ana Yüklenici: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('main_contractor', null, ['class' => 'form-control', 'placeholder' =>
                                'Ana yüklenicinin adını giriniz']) !!}

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('isg') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('isg', 'İSG: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('isg', null, ['class' => 'form-control', 'placeholder' =>
                                'İSG\'nin adını giriniz']) !!}

                            </div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('start_date') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('start_date', 'Başlangıç Tarihi: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                <div class="input-group input-append date dateRangePicker">
                                    <input type="text" class="form-control" name="start_date" placeholder="Başlangıç tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('contract_date') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('contract_date', 'Sözleşme Tarihi: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                <div class="input-group input-append date dateRangePicker">
                                    <input type="text" class="form-control" name="contract_date" placeholder="Sözleşme tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('end_date') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('end_date', 'İş Bitim Tarihi: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                <div class="input-group input-append date dateRangePicker">
                                    <input type="text" class="form-control" name="end_date" placeholder="İş bitim tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('address', 'Adres: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::textarea('address', null,
                                ['class' => 'form-control',
                                'placeholder' => 'Şantiye adresi',
                                'rows' => '3']) !!}

                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('site_chief') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('site_chief', 'Şantiye şefi: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('site_chief', null, ['class' => 'form-control', 'placeholder' => 'Şantiye şefini giriniz']) !!}

                            </div>
                        </div>
                    </div>


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

@section('page-specific-js')
    <script>
        if ($('.has-error')[0]) {
            $('#addNewSite').modal('show');

        }


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


    </script>
@stop