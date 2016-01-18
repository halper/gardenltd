<?php
use App\Library\TurkishChar;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xs-10 col-sm-10 text-center">
                <span><strong>GÜNLÜK ŞANTİYE RAPORU</strong></span>
            </div>
            <div class="col-xs-2 col-sm-2">
                <span><strong>NO:</strong> {{$report_no}}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 text-center">
        <div style="background-color: rgb(255,204,0)">
            <span><strong>MKE Kırıkkale Hurda Müdürlüğünde P1,P2,P3 ve P5 Parselleri Geçirimsiz Saha Betonu ve Altyapısı
                    İşi</strong></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-8 col-sm-8">
        <div style="background-color: rgb(0, 102, 204)">
            <div class="row">
                <div class="col-xs-6 col-sm-6">
                    <div>
                        <span>ŞANTİYE KODU/ADI</span>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6">
                    <div style="background-color: rgb(0, 102, 204); margin-right: 3px">
                        <span><strong>{{$site->job_name}}</strong></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-xs-4 col-sm-4">
        <div class="row">
            {!! Form::open([
            'url' => "/tekil/$site->slug/select-date",
            'method' => 'POST',
            'class' => 'form form-horizontal',
            'id' => 'dateRangeForm',
            'role' => 'form'
            ]) !!}

            <div class="col-xs-6 date text-center hidden-print">
                <div class="input-group input-append date" id="dateRangePicker">
                    <input type="text" class="text-center" name="date" style="line-height: 22px; height:22px"
                           autocomplete="false"/>
                    <span class="input-group-addon add-on" style="border: none; background-color: inherit"></span>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="col-xs-6 text-right visible-print">
                {{\App\Library\CarbonHelper::getTurkishDate($report->created_at)}}
            </div>
            <div class="col-xs-6 text-center">
                <?php
                $creation = strtotime($report->updated_at);
                $myFormatForCreation = date("H:m:s", $creation);
                ?>
                {{$myFormatForCreation}}
            </div>
        </div>

    </div>
</div>
<div class="table-responsive">
    <table class="table table-exxxtra-condensed table-bordered" style="font-size: smaller">

        <tbody>

        <tr>
            <td style="vertical-align: middle;"><strong>İŞİN SÜRESİ</strong></td>
            <td  style="vertical-align: middle;">{{$total_date}} gün</td>
            <td  style="vertical-align: middle;" class="text-center"><strong>KALAN SÜRE:</strong></td>
            <td  style="vertical-align: middle;"></td>
            <td  style="vertical-align: middle;"><strong>HAVA</strong></td>
            <td  class="text-center"
                style="vertical-align: middle; color: #fff; background-color: rgb(0, 102, 204); font-size: 16px;">{!! $weather_symbol !!}</td>
            <td  style="vertical-align: middle;" class="text-center">{!! !is_null($report->weather) ? $report->weather : $my_weather->getDescription() !!}</td>
            <td  style="vertical-align: middle;" class="text-center">{!! !is_null($report->temp_min) ? str_replace('.', ',', $report->temp_min) ."<sup>o</sup>C / ". str_replace('.', ',', $report->temp_max) : $my_weather->getMin() ."<sup>o</sup>C / ". $my_weather->getMax() !!}
                <sup>o</sup>C
            </td>

        </tr>
        <tr style="vertical-align: middle;">
            <td  style="vertical-align: middle;"><strong>GEÇEN SÜRE</strong></td>
            <td  style="vertical-align: middle;">{{$total_date - $left}} gün</td>
            <td  style="vertical-align: middle;" class="text-center" {{$left<$day_warning ? "style=background-color:red;color:white" : ""}}>{{$left}}
                gün
            </td>
            <td  style="vertical-align: middle;"></td>
            <td  style="vertical-align: middle;"><strong>RÜZGAR</strong></td>
            <td  style="vertical-align: middle;" class="text-center">{{ !is_null($report->weather) ? str_replace('.', ',', $report->wind) : $my_weather->getWind()}} m/s</td>
            <td  class="text-center"
                 style="vertical-align: middle; font-size: 18px; margin-top: 0; margin-bottom: 0"><i
                        class="wi wi-wind towards-{{ !is_null($report->weather) ? $report->degree :$my_weather->getDirection()}}-deg"></i>
            </td>
            <td  class="text-center" style="vertical-align: middle; background-color: {{$report->is_working == 1 ? "rgb(0,128,0)" : "red"}}">
                <strong>ÇALIŞMA {{$report->is_working == 1 ? "VAR":"YOK"}}</strong></td>
        </tr>

        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-xs-6 col-md-8">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table class="table table-exxxtra-condensed table-bordered" style="font-size: smaller">
                        <thead>
                        <tr style="background-color: rgb(127,127,127)">
                            <th class="col-sm-10">PERSONEL İCMALİ</th>
                            <th class="col-sm-2" style="text-align: right">TOPLAM</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>YÖNETİM/DENETİM PERSONEL TOPLAMI</td>
                            <td id="man-tot-res" class="text-right"></td>
                        </tr>
                        <tr>
                            <td>ANA YÜKLENİCİ PERSONEL TOPLAMI</td>
                            <td id="main-con-tot-res" class="text-right"></td>
                        </tr>
                        <tr>
                            <td>ALT YÜKLENİCİLER PERSONEL TOPLAMI</td>
                            <td id="sub-staff-tot-res" class="text-right"></td>
                        </tr>
                        <tr>
                            <td>GENEL TOPLAM</td>
                            <td id="gen-tot-res" class="text-right"></td>
                        </tr>

                        </tbody>


                        @if((int)$report->building_control_staff + (int) $report->management_staff + (int) $report->employer_staff > 0)

                            <thead>
                            <tr style="background-color: rgb(127,127,127)">
                                <th class="col-sm-10">YÖNETİM/DENETİM PERSONEL TABLOSU</th>
                                <th class="col-sm-2" style="text-align: right">ADET</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($report->employer_staff))
                                <tr>
                                    <td>İŞVEREN ({{$site->employer}})</td>
                                    <td class="text-right">{{$report->employer_staff}}</td>
                                </tr>
                            @endif
                            @if(!empty($report->management_staff))
                                <tr>
                                    <td>PROJE YÖNETİM({{$site->management_name}})</td>
                                    <td class="text-right">{{$report->management_staff}}</td>
                                </tr>
                            @endif
                            @if(!empty($report->building_control_staff))
                                <tr>
                                    <td>YAPI DENETİM({{$site->building_control}})</td>
                                    <td class="text-right">{{$report->building_control_staff}}</td>
                                </tr>
                            @endif
                            @if(!empty($report->isg_staff))
                                <tr>
                                    <td>İSG({{$site->isg}})</td>
                                    <td class="text-right">{{$report->isg_staff}}</td>
                                </tr>
                            @endif

                            <tr>
                                <td>TOPLAM</td>
                                <td id="man-tot"
                                    class="text-right">{{(int)$report->building_control_staff + (int) $report->management_staff + (int) $report->employer_staff + (int) $report->isg_staff}}</td>
                            </tr>

                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>


        {{--Main contractor table--}}
        <?php
        $main_contractor_total = 0;
        $number_of_col = 9;
        foreach ($report->staff()->get() as $staff) {
            $main_contractor_total += $staff->pivot->quantity;
        }
        ?>
        @if($main_contractor_total>0)
            <div class="row">

                <div class="col-sm-12">
                    <div class="text-center" style="background-color: rgb(127,127,127)">
                    <span><strong>{{$site->main_contractor}}
                            <small style="color: #f0f0f0;">(Ana Yüklenici)</small>
                            PERSONEL TABLOSU</strong></span>
                    </div>
                </div>

                <?php
                $table_count = (int)floor(sizeof($report->staff()->get()) / $number_of_col) + 1;
                $staff_name_arr = \App\Library\Shortener::shorten_collection($report->staff()->get(), 'staff');
                $staff_quantity_arr = [];

                foreach ($report->staff()->get() as $staff) {
                    array_push($staff_quantity_arr, $staff->pivot->quantity);
                    $extra_column = sizeof($staff_name_arr) > $number_of_col ? $number_of_col - 2 : sizeof($staff_name_arr) - 2;
                }
                ?>

                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-exxxtra-condensed" style="font-size: smaller">
                            <tbody style="text-align: center">

                            @for($i = 0; $i<$table_count; $i++)
                                <tr>
                                    @for($j = $i*$number_of_col; $j<sizeof($staff_name_arr) && $j<($i+1)*$number_of_col; $j++)
                                        <th style="text-align: center">{{$staff_name_arr[$j]}}</th>
                                    @endfor
                                </tr>

                                <tr>
                                    @for($j = $i*$number_of_col; $j<sizeof($staff_name_arr) && $j<($i+1)*$number_of_col; $j++)
                                        <td>{{$staff_quantity_arr[$j]}}</td>
                                    @endfor
                                </tr>
                            @endfor
                            <tr>

                                @for($i = 0; $i<=$extra_column; $i++)
                                    @if($i+1 > $extra_column)
                                        <td><strong>TOPLAM</strong></td>
                                    @else
                                        <td></td>
                                    @endif
                                @endfor
                                <td id="main-con-tot" class="text-right">{{$main_contractor_total}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        {{--End of ana yüklenici table--}}

        {{--Subcontractor table--}}
        <div class="row">
            <div class="col-sm-12">
                <div class="box">
                    <div class="box-body">
                        <div class="text-center" style="background-color: rgb(127,127,127)">
                            <span><strong>ALT YÜKLENİCİLER PERSONEL TABLOSU</strong></span>
                        </div>
                    </div>

                    @foreach($report_subcontractors as $sub)
                        <?php
                        $sub_row_total = 0;
                        $report_substaffs = $report->substaff()->where('subcontractor_id', $sub->id)->get();
                        $row_count = (int)floor(sizeof($report_substaffs) / $number_of_col) + 1;
                        $staff_name_arr = \App\Library\Shortener::shorten_collection($report_substaffs, 'staff');
                        $staff_quantity_arr = [];

                        foreach ($report_substaffs as $staff) {
                            array_push($staff_quantity_arr, $staff->pivot->quantity);
                            $extra_column = sizeof($staff_name_arr) > $number_of_col ? $number_of_col - 2 : sizeof($staff_name_arr) - 2;

                            $sub_row_total += (int)$staff->pivot->quantity;

                        }
                        ?>
                        <div class="col-sm-12">
                            <legend style="font-size: 18px; margin-bottom: 2px">{{$sub->subdetail->name}}
                                @foreach($sub->manufacturing()->get() as $manufacture)
                                    <small>({{TurkishChar::tr_up($manufacture->name) }})</small>
                                @endforeach
                            </legend>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-exxxtra-condensed"
                                           style="font-size: smaller">
                                        <tbody style="text-align: center">
                                        @for($i = 0; $i<$row_count; $i++)
                                            <tr>
                                                @for($j = $i*$number_of_col; $j<sizeof($staff_name_arr) && $j<($i+1)*$number_of_col; $j++)
                                                    <th style="text-align: center">{{$staff_name_arr[$j]}}</th>
                                                @endfor
                                            </tr>
                                            <tr>
                                                @for($j = $i*$number_of_col; $j<sizeof($staff_name_arr) && $j<($i+1)*$number_of_col; $j++)
                                                    <td>{{$staff_quantity_arr[$j]}}</td>
                                                @endfor
                                            </tr>

                                        @endfor
                                        <tr>
                                            @for($i = 0; $i<=$extra_column; $i++)
                                                @if($i+1 > $extra_column)
                                                    <td><strong>TOPLAM</strong></td>
                                                @else
                                                    <td></td>
                                                @endif
                                            @endfor
                                            <td class="sub-staff-tot text-right">{{$sub_row_total}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                        $subcontractor_staff_total += $sub_row_total;
                        ?>
                    @endforeach
                    @if($main_contractor_total + $subcontractor_staff_total + $total_management>0)
                        @if($subcontractor_staff_total>0)
                            <div class="row hidden">
                                <div class="col-sm-11">
                                    <p class="text-right"><strong>ALT YÜKLENİCİ
                                            TOPLAMI</strong></p>
                                </div>
                                <div class="col-sm-1">
                                    <p class="text-left" id="sub-staff-tot">{{$subcontractor_staff_total}}</p>
                                </div>
                            </div>
                        @endif
                        <div class="row hidden">
                            <div class="col-sm-11">
                                <p class="text-right" style="font-size: large"><strong>GENEL
                                        TOPLAM</strong>
                                </p>
                            </div>
                            <div class="col-sm-1">
                                <p class="text-left"
                                   style="font-size: large">{{$main_contractor_total + $subcontractor_staff_total + $total_management}}</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
    {{--END OF SUBCONTRACTOR TABLE--}}

    {{--EKİPMAN TABLE--}}

    <div class="col-xs-6 col-md-4">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center" style="background-color: rgb(255,204,0)">
                    <span><strong>EKİPMAN İCMALİ</strong></span>
                </div>
                <?php
                $equipment_total = 0;

                foreach ($report->equipment()->get() as $eq) {
                    $equipment_total += $eq->pivot->present + $eq->pivot->working + $eq->pivot->broken;
                }
                ?>
                @if($equipment_total>0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-exxxtra-condensed" style="font-size: smaller">
                            <thead>
                            <tr>
                                <th style="text-align: center">EKİPMAN ADI</th>
                                <th style="text-align: center">ÇALIŞAN</th>
                                <th style="text-align: center">MEVCUT</th>
                                <th style="text-align: center">ARIZALI</th>
                                <th style="text-align: center">TOPLAM</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $equipment_total = 0;
                            ?>
                            @foreach ($report->equipment()->get() as $eq)
                                <tr style="text-align: center">
                                    <td>{{$eq->name}}</td>
                                    <td>{{$eq->pivot->present}}</td>
                                    <td>{{$eq->pivot->working}}</td>
                                    <td>{{$eq->pivot->broken}}</td>
                                    <td>{{$eq->pivot->present + $eq->pivot->working + $eq->pivot->broken}}</td>
                                </tr>
                                <?php
                                $equipment_total += (int)$eq->pivot->present + (int)$eq->pivot->working + (int)$eq->pivot->broken;
                                ?>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right"><strong>TOPLAM</strong></td>
                                <td class="text-right">{{$equipment_total}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
{{--END OF EKİPMAN TABLE--}}


{{--WORK DONE TABLE--}}
<div class="row">
    <div class="col-md-12">
        @if(sizeof($report->pwunit()->get()) + sizeof($report->swunit()->get()) > 0)
            <div class="text-center" style="background-color: rgb(127,127,127)">
                <span><strong>YAPILAN İŞLER</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-exxxtra-condensed" style="font-size: smaller">
                    <thead>
                    <tr>
                        <th style="text-align: center">S.N</th>
                        <th style="text-align: center">ÇALIŞAN BİRİM</th>
                        <th style="text-align: center">KİŞİ SAYISI</th>
                        <th style="text-align: center">ÖLÇÜ BİRİMİ</th>
                        <th style="text-align: center">YAPILAN İŞLER</th>
                        <th style="text-align: center">PLANLANAN</th>
                        <th style="text-align: center">YAPILAN</th>
                        <th style="text-align: center">YÜZDE</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 1;
                    ?>
                    @foreach($report->pwunit()->get() as $pw)
                        <?php
                        $pw_work_done_in_percent = ((int)$pw->planned == 0 || is_null($pw->planned)) ? 0 : 100 * (int)$pw->done / (int)$pw->planned;
                        ?>

                        @if($pw_work_done_in_percent>100)
                            <tr class="bg-success" style="text-align: center">
                        @elseif($pw_work_done_in_percent<100)
                            <tr class="bg-danger" style="text-align: center">
                        @elseif($pw_work_done_in_percent == 100)
                            <tr class="bg-warning" style="text-align: center">
                                @endif
                                <td>{{$i++}}</td>
                                <td>{{$staffs->find($pw->staff_id)->staff}}</td>
                                <td>{{$pw->quantity}}</td>
                                <td>{{$pw->unit}}</td>
                                <td class="number">{{str_replace(".", ",", $pw->works_done)}}</td>
                                <td class="number">{{str_replace(".", ",", $pw->planned)}}</td>
                                <td class="number">{{str_replace(".", ",", $pw->done)}}</td>
                                <td class="number">%{{str_replace(".", ",", $pw_work_done_in_percent)}}</td>
                            </tr>

                            @endforeach

                            @foreach($report->swunit()->get() as $sw)
                                <?php

                                $sw_work_done_in_percent = ((int)$sw->planned == 0 || is_null($sw->planned)) ? 0 : 100 * (int)$sw->done / (int)$sw->planned;

                                ?>

                                @if($sw_work_done_in_percent>100)
                                    <tr class="bg-success" style="text-align: center">
                                @elseif($sw_work_done_in_percent<100)
                                    <tr class="bg-danger" style="text-align: center">
                                @elseif($sw_work_done_in_percent == 100)
                                    <tr class="bg-warning" style="text-align: center">
                                        @endif
                                        <td>{{$i++}}</td>
                                        <td>{{\App\Subcontractor::find($sw->subcontractor_id)->name}}</td>
                                        <td>{{$sw->quantity}}</td>
                                        <td>{{$sw->unit}}</td>
                                        <td class="number">{{$sw->works_done}}</td>
                                        <td class="number">{{$sw->planned}}</td>
                                        <td class="number">{{$sw->done}}</td>
                                        <td class="number">%{{$sw_work_done_in_percent}}</td>
                                    </tr>

                                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
{{--END OF YAPILAN İŞLER TABLE--}}

{{--GELEN MALZEMELER TABLE--}}
<div class="row">
    <div class="col-md-12">
        @if(sizeof($inmaterials)>0)
            <div class="text-center" style="background-color: rgb(127,127,127)">
                <span><strong>GELEN MALZEMELER</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-exxxtra-condensed" style="font-size: smaller">
                    <thead>
                    <tr>
                        <th style="text-align: center">S.N</th>
                        <th style="text-align: center">GELEN MALZEME</th>
                        <th style="text-align: center">GELDİĞİ YER</th>
                        <th style="text-align: center">BİRİM</th>
                        <th style="text-align: center">MİK.</th>
                        <th style="text-align: center">AÇIKLAMASI</th>
                    </tr>
                    </thead>
                    <tbody>

                    @for($i = 1; $i<=sizeof($inmaterials); $i++)

                        <tr style="text-align: center">
                            <td>{{$i}}</td>
                            <td>{{TurkishChar::tr_up(\App\Material::find($inmaterials[$i-1]->material_id)->material)}}</td>
                            <td>{{TurkishChar::tr_up($inmaterials[$i-1]->coming_from)}}</td>
                            <td>{{TurkishChar::tr_up($inmaterials[$i-1]->unit)}}</td>
                            <td class="number">{{str_replace(".", ",", $inmaterials[$i-1]->quantity)}}</td>
                            <td class="text-left">{{$inmaterials[$i-1]->explanation}}</td>
                        </tr>

                    @endfor
                    </tbody>

                </table>
            </div>

        @endif
    </div>
</div>
{{--END OF GELEN MALZEMELER TABLE--}}

{{--GİDEN MALZEMELER TABLE--}}
<div class="row">
    <div class="col-md-12">
        @if(sizeof($outmaterials)>0)
            <div class="text-center" style="background-color: rgb(127,127,127)">
                <span><strong>GÖNDERİLEN MALZEMELER</strong></span>
            </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-exxxtra-condensed" style="font-size: smaller">

                        <thead>
                        <tr>
                            <th style="text-align: center">S.N</th>
                            <th style="text-align: center">GİDEN MALZEME</th>
                            <th style="text-align: center">GİTTİĞİ YER</th>
                            <th style="text-align: center">BİRİM</th>
                            <th style="text-align: center">MİK.</th>
                            <th style="text-align: center">AÇIKLAMASI</th>
                        </tr>
                        </thead>
                        <tbody>

                        @for($i = 1; $i<=sizeof($outmaterials); $i++)

                            <tr style="text-align: center">
                                <td>{{$i}}</td>
                                <td>{{TurkishChar::tr_up(\App\Material::find($outmaterials[$i-1]->material_id)->material)}}</td>
                                <td>{{TurkishChar::tr_up($outmaterials[$i-1]->coming_from)}}</td>
                                <td>{{TurkishChar::tr_up($outmaterials[$i-1]->unit)}}</td>
                                <td class="number">{{str_replace(".", ",", $outmaterials[$i-1]->quantity)}}</td>
                                <td class="text-left">{{$outmaterials[$i-1]->explanation}}</td>
                            </tr>

                        @endfor

                        </tbody>
                    </table>
                </div>
            @endif
    </div>
</div>
{{--END OF GELEN MALZEMELER TABLE--}}