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
                {label: " Hakedişler", data: 75, color: "#006dcc"},
                {label: " Kalan", data: 25, color: "#5bc0de"}
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
                <span class="info-box-icon" style="background-color: #00a65a !important; color: #f9f9f9"><i
                            class="ion ion-ios-people"></i></span>

                <div class="info-box-content">
                    <span class="info-box-header">İdare</span>
                    <span class="info-box-detail">{{$site->management_name}}</span>
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
                <span class="info-box-icon bg-red"><i class="ion ion-home"></i></span>

                <div class="info-box-content">
                    <span class="info-box-header">Adres</span>
                    <span class="info-box-detail">{{$site->address}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
    <!-- END OF INFO BOXES -->

    <!-- CHARTS -->
    <div class="row">
        <div class="col-md-6">
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
                        Geçen <strong>{{ $passed }} </strong> gün, kalan <strong>{{ $left }} </strong> gün.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
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
                        Hakedişler ve kalan miktarları gelecek.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF CHARTS -->

    <?php
    $i = 0;
    $i_modulus = 0;
    ?>
    @foreach($modules->getModules() as $module)
        @if(Auth::User()->hasAnyPermissionOnModule($module->id) || Auth::User()->isAdmin())
        <?php
        if (strpos($module->icon, "ion-") !== false) {
            $i_icon = "ion ";
        } else {
            $i_icon = "fa ";
        }
        $i_icon .= $module->icon;

        $i_modulus = $i % 4;
        $i = $i + 1;
        $box_bg = "bg-";

        switch ($i_modulus) {
            case(0):
                $box_bg .= "aqua";
                break;
            case(1):
                $box_bg .= "green";
                break;
            case(2):
                $box_bg .= "yellow";
                break;
            case(3):
                $box_bg .= "red";
                break;
        }


        ?>
{!! $i_modulus == 0 ? "<div class=\"row\">" : "" !!}
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box {{$box_bg}}">
                <div class="inner">
                    <h3>{{$module->name}}</h3>
                </div>
                <div class="icon">
                    {!! empty($module->icon) ? "" : ("<i class=\"$i_icon\"></i>")!!}
                </div>
                <a href="{{$site->slug . "/" . $module->slug}}" class="small-box-footer">
                    Detaylar <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        {!! $i_modulus == 3 ? "</div>" : "" !!}
        @endif

    @endforeach
    {!! $i_modulus != 3 ? "</div>" : "" !!}
@stop