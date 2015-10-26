@extends('landing.landing')

@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger fade in alert-box">
            Yeni şantiye eklenirken hata oluştu!
            <a href="#" class="close" data-dismiss="alert">&times;</a>
        </div>
    @endif
    <div class="callout callout-info">
        <h4>Şantiyeler sayfası</h4>

        <p>İşlem yapmak istediğiniz şantiyeyi seçebilir ya da yeni şantiye oluşturabilirsiniz.</p>
    </div>

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
                    <a href="#" class="close siteDelBut" data-toggle="modal"
                       {!! "data-id=\"$site->id\" data-name= \"$site->job_name\""!!} data-target="deleteSiteConfirm"><i
                                class="fa fa-trash-o"></i></a>
                    <span class="info-box-text">{{$site->job_name}}</span>
                    <span class="info-box-number">{{"Kalan süre: $left gün"}}</span>

                    <div class="progress">
                        <div class="progress-bar" {!! "style=\"width: $total_per%\""!!}></div>
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
@stop