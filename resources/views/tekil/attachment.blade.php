<?php

$site_reports = $site->report()->with('photo', 'receipt')->get()

?>

@extends('tekil.layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>
@endsection



@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>

    <script>
        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

    </script>
@endsection



@section('content')


    <div class="row">
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
                        <div class="col-sm-12">
                            <div class="row">
                            @foreach($site_reports as $site_report)
                                @foreach($site_report->photo as $site_photo)
                                    <?php
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_photo->file()->first()->path);
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_photo->file()->first()->name;
                                    if (strpos($site_photo->file()->first()->name, 'pdf') !== false) {
                                        $image = URL::to('/') . "/img/pdf.jpg";
                                    } elseif (strpos($site_photo->file()->first()->name, 'doc') !== false) {
                                        $image = URL::to('/') . "/img/word.png";
                                    }
                                    ?>
<div class="col-sm-4">
                                    <a href="{{$image}}"
                                       data-toggle="lightbox" data-gallery="reportsitephotos"
                                       class="col-sm-4">
                                        <img src="{{$image}}" class="img-responsive">
                                        {{$site_photo->file()->first()->name}}
                                    </a>
    </div>

                                @endforeach
                            @endforeach

                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
    <div class="col-sm-12">
                    @foreach($site_reports as $site_report)
                        @foreach($site_report->receipt as $site_receipt)
                            <?php
                            $my_path_arr = explode(DIRECTORY_SEPARATOR, $site_receipt->file()->first()->path);
                            $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                            $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $site_receipt->file()->first()->name;
                            if (strpos($site_receipt->file()->first()->name, 'pdf') !== false) {
                                $image = URL::to('/') . "/img/pdf.jpg";
                            } elseif (strpos($site_receipt->file()->first()->name, 'doc') !== false) {
                                $image = URL::to('/') . "/img/word.png";
                            }
                            ?>

                            <a href="{{$image}}"
                               data-toggle="lightbox" data-gallery="reportsitereceipts"
                               class="col-sm-4">
                                <img src="{{$image}}" class="img-responsive">
                                {{$site_receipt->file()->first()->name}}
                            </a>

                        @endforeach
                    @endforeach

                </div>
                </div>
                </div>

            </div>
        </div>
    </div>

@endsection