@extends('tekil.layout')

@section('page-specific-js')

    <script src="<?= URL::to('/'); ?>/js/jquery.flot.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/jquery.flot.resize.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/jquery.flot.pie.min.js" type="text/javascript"></script>

    <?php

    $start_date = date_create($site->start_date);
    $now = date_create();
    $end_date = date_create($site->end_date);
    $left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));
    $total = str_replace("+", "", date_diff($start_date, $end_date)->format("%R%a"));
    $passed = str_replace("+", "", date_diff($start_date, $now)->format("%R%a"));
    $hakedis = $site->contract_worth;
    $total_allowance = 0.0;
    foreach ($site->allowance()->get() as $allowance) {
        $total_allowance += $allowance->amount;
    }
    $left_allowance = $hakedis - $total_allowance;


    $java_func = <<<EOF
    <script>
        $(function() {

            var donutData = [
                {label: "Geçen", data: "$passed", color: "#006dcc"},
                {label: "Kalan", data: "$left", color: "#d9534f"}
            ];
            $.plot("#contract-status-chart", donutData, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.5,
                        label: {
                            show: true,
                            radius: 2 / 3,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }

                    }
                },
                legend: {
                    show: true
                }
            });
        });


        $(function() {

            var followData = [
                {label: " Hakedişler", data: $total_allowance, color: "#006dcc"},
                {label: " Kalan", data: $left_allowance, color: "#5bc0de"}
            ];
            $.plot("#finance-status-chart", followData, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.5,
                        label: {
                            show: true,
                            radius: 2 / 3,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }

                    }
                },
                legend: {
                    show: true
                }
            });
        });

    function labelFormatter(label, series) {
        return '<div class="text-center" style="font-size:13px; padding:2px; color: #fff; font-weight: 600;">'
        + label
        + "<br/>"
        + Math.round(series.percent) + "%</div>";
    }
    </script>

EOF;

    echo $java_func;
    ?>

    @stop

    @section('content')

            <!-- INFO BOXES -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-building-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-header">Şantiye</span>
                    <span class="info-box-detail">{{$site->job_name}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-ios-person"></i></span>

                <div class="info-box-content">
                    <span class="info-box-header">Şef</span>
                    <span class="info-box-detail">{{$site->site_chief}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon" style="background-color: #00a65a !important; color: #f9f9f9"><i
                            class="ion ion-ios-people"></i></span>

                <div class="info-box-content">
                    <span class="info-box-header">İdare</span>
                    <span class="info-box-detail">{{empty($site->management_name) ? "Girilmemiş" : $site->management_name}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="ion ion-location"></i></span>

                <div class="info-box-content">
                    <span class="info-box-header">Adres</span>
                    <span class="info-box-detail">{{$site->address . " " . $site->city->name}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
    <!-- END OF INFO BOXES -->

    <!-- CHARTS -->
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title with-border">Süre</h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="contract-status-chart" style="height: 300px; padding: 0px; position: relative;">

                    </div>
                    <p>
                        Geçen <strong>{{ $passed }} </strong> gün
                        <br>Kalan <strong>{{ $left }} </strong> gün
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="fa fa-bar-chart-o"></i>

                    <h3 class="box-title with-border">Parasal</h3>

                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="finance-status-chart" style="height: 300px; padding: 0px; position: relative;">

                    </div>
                    <p>
                        Hakedişler: <strong><span class="inumber">{{$total_allowance}}</span> TL</strong>
                        <br>
                        Kalan: <strong><span class="inumber">{{$left_allowance}}</span> TL</strong>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row">

                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color: #00a65a !important; color: #f9f9f9"><i
                                    class="ion ion-person-stalker"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">İşveren</span>
                            <span class="info-box-detail">{{$site->employer}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color: #00a65a !important; color: #f9f9f9"><i
                                    class="ion ion-unlocked"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">Ana Yüklenici</span>
                            <span class="info-box-detail">{{$site->main_contractor}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

           <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color: #00a65a !important; color: #f9f9f9"><i
                                    class="ion ion-settings"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">Yapı Denetim</span>
                            <span class="info-box-detail">{{$site->building_control}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

           <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color: #00a65a !important; color: #f9f9f9"><i
                                    class="ion ion-alert-circled"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">İSG</span>
                            <span class="info-box-detail">{{$site->isg}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-code-working"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">Şantiye Kodu</span>
                            <span class="info-box-detail">{{empty($site->code) ? "Belirtilmemiş" : $site->code}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                <div class="col-sm-12">

                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-android-calendar"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">İş</span>
                            <span class="info-box-detail"><strong style="font-weight: 400">Başlangıç: </strong>{{\App\Library\CarbonHelper::getTurkishDate($site->start_date)}}</span>
                            <span class="info-box-detail"><strong  style="font-weight: 400">Bitiş: </strong>{{\App\Library\CarbonHelper::getTurkishDate($site->end_date)}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->

                </div>

                <div class="col-sm-12">

                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-ios-calendar"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">Sözleşme Tarihi</span>
                            <span class="info-box-detail">{{\App\Library\CarbonHelper::getTurkishDate($site->contract_date)}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>

                <div class="col-sm-12">

                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-ios-pricetags"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-header">Kontrat</span>
                            <span class="info-box-detail"><strong style="font-weight: 400">Bedeli: </strong><span class="inumber">{{$site->contract_worth}}</span> TL</span>
                            <span class="info-box-detail"><strong style="font-weight: 400">İş Artış: </strong><span class="inumber">{{$site->extra_cost}}</span> TL</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->

                </div>

            </div>
        </div>
    </div>
    <!-- END OF CHARTS -->


@stop