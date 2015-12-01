<?php
use App\Library\TurkishChar;
?>
<div class="row">
    <div class="col-xs-12 col-md-8">
        @if((int)$report->building_control_staff + (int) $report->management_staff + (int) $report->employer_staff > 0)
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered">
                            <thead>
                            <tr style="background-color: rgb(127,127,127)">
                                <th class="col-sm-10">PERSONEL İCMAL TABLOSU</th>
                                <th class="col-sm-2">ADET</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($report->employer_staff))
                                <tr>
                                    <td><strong>İŞVEREN ({{$site->employer}})</strong></td>
                                    <td>{{$report->employer_staff}}</td>
                                </tr>
                            @endif
                            @if(!empty($report->management_staff))
                                <tr>
                                    <td><strong>PROJE YÖNETİM({{$site->management_name}})</strong></td>
                                    <td>{{$report->management_staff}}</td>
                                </tr>
                            @endif
                            @if(!empty($report->building_control_staff))
                                <tr>
                                    <td><strong>YAPI DENETİM({{$site->building_control}})</strong></td>
                                    <td>{{$report->building_control_staff}}</td>
                                </tr>
                            @endif

                            <tr>
                                <td><strong>TOPLAM</strong></td>
                                <td>{{(int)$report->building_control_staff + (int) $report->management_staff + (int) $report->employer_staff}}</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif


        {{--Main contractor table--}}
        <?php
        $main_contractor_total = 0;
        $number_of_col = 12;
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
                $staff_name_arr = [];
                $staff_quantity_arr = [];
                ?>
                @foreach($report->staff()->get() as $staff)
                    <?php
                    $vowels = ["a", "e", "ı", "i", "o", "ö", "u", "ü"];
                    $pos = strpos($staff->staff, " ");
                    $staff_name = "";

                    if ($pos === false) {
                        // string needle NOT found in haystack
                        $staff_name = $staff->staff;
                    } else {
                        // string needle found in haystack
                        $staff_words = explode(" ", $staff->staff);
                        $i = 1;
                        foreach ($staff_words as $word) {
                            if (strlen($word) > 5) {
                                $cut = in_array(mb_substr($word, 3, 1), $vowels) ? 3 : 4;
                                $staff_name .= mb_substr($word, 0, $cut, 'utf-8') . ".";
                            } else {
                                $staff_name .= $word;
                            }
                            if ($i < sizeof($staff_words)) {
                                $staff_name .= " ";
                                $i++;
                            }
                        }
                    }
                    array_push($staff_name_arr, $staff_name);
                    array_push($staff_quantity_arr, $staff->pivot->quantity);
                    $extra_column = sizeof($staff_name_arr) > $number_of_col ? $number_of_col - 2 : sizeof($staff_name_arr) - 2;
                    ?>
                @endforeach
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed">
                            <tbody class="text-center">

                            @for($i = 0; $i<$table_count; $i++)
                                <tr>
                                    @for($j = $i*$number_of_col; $j<sizeof($staff_name_arr) && $j<($i+1)*$number_of_col; $j++)
                                        <th>{{$staff_name_arr[$j]}}</th>
                                    @endfor
                                </tr>

                                <tr>
                                    @for($j = $i*$number_of_col; $j<sizeof($staff_name_arr) && $j<($i+1)*$number_of_col; $j++)
                                        <td>{{$staff_quantity_arr[$j]}}</td>
                                    @endfor
                                </tr>
                            @endfor
                            <tr>
                                <td><strong>TOPLAM</strong></td>
                                @for($i = 0; $i<$extra_column; $i++)
                                    <td></td>
                                @endfor
                                <td>{{$main_contractor_total}}</td>
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
                    @foreach($subcontractors as $sub)
                        <?php
                        $sub_row_total = 0;
                        ?>


                        <div class="col-sm-12">
                            <legend>{{$sub->name}}
                                @foreach($sub->manufacturing()->get() as $manufacture)
                                    <small>({{TurkishChar::tr_up($manufacture->name) }})</small>
                                @endforeach
                            </legend>
                        </div>

                        <div class="row">
                            <div class="col-sm-11">
                                @foreach($report->substaff()->where('subcontractor_id', $sub->id)->get() as $substaff)
                                    <div class="col-sm-1 text-center">
                                        <strong>{{$substaff->name}}</strong>
                                        <br>
                                        {{$substaff->pivot->quantity}}</div>
                                    <?php
                                    $sub_row_total += (int)$substaff->pivot->quantity;
                                    ?>
                                @endforeach
                            </div>
                            <div class="col-sm-1 text-left"><strong>TOPLAM</strong>
                                <br>
                                {{$sub_row_total}}
                            </div>
                        </div>
                        <?php
                        $subcontractor_staff_total += $sub_row_total;
                        ?>
                    @endforeach
                    @if($main_contractor_total + $subcontractor_staff_total + $total_management>0)
                        @if($subcontractor_staff_total>0)
                            <div class="row">
                                <div class="col-sm-11">
                                    <p class="text-right"><strong>ALT YÜKLENİCİ
                                            TOPLAMI</strong></p>
                                </div>
                                <div class="col-sm-1">
                                    <p class="text-left">{{$subcontractor_staff_total}}</p>
                                </div>
                            </div>
                        @endif
                        <div class="row">
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


    <div class="col-xs-12 col-md-4">
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
                        <table class="table table-bordered table-condensed">
                            <thead>
                            <tr>
                                <th>EKİPMAN ADI</th>
                                <th>ÇALIŞAN</th>
                                <th>MEVCUT</th>
                                <th>ARIZALI</th>
                                <th>TOPLAM</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $equipment_total = 0;
                            ?>
                            @foreach ($report->equipment()->get() as $eq)
                                <tr>
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
                                <td><strong>TOPLAM</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{$equipment_total}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if(sizeof($report->pwunit()->get()) + sizeof($report->swunit()->get()) > 0)
            <div class="text-center" style="background-color: rgb(127,127,127)">
                <span><strong>YAPILAN İŞLER</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>S.N</th>
                        <th>ÇALIŞAN BİRİM</th>
                        <th>KİŞİ SAYISI</th>
                        <th>ÖLÇÜ BİRİMİ</th>
                        <th>YAPILAN İŞLER</th>
                        <th>PLANLANAN</th>
                        <th>YAPILAN</th>
                        <th>YÜZDE</th>

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
                            <tr class="bg-success">
                        @elseif($pw_work_done_in_percent<100)
                            <tr class="bg-danger">
                        @elseif($pw_work_done_in_percent == 100)
                            <tr class="bg-warning">
                                @endif
                                <td>{{$i++}}</td>
                                <td>{{$staffs->find($pw->staff_id)->staff}}</td>
                                <td>{{$pw->quantity}}</td>
                                <td>{{$pw->unit}}</td>
                                <td>{{$pw->works_done}}</td>
                                <td>{{$pw->planned}}</td>
                                <td>{{$pw->done}}</td>
                                <td>%{{$pw_work_done_in_percent}}</td>
                            </tr>

                            @endforeach

                            @foreach($report->swunit()->get() as $sw)
                                <?php

                                $sw_work_done_in_percent = ((int)$sw->planned == 0 || is_null($sw->planned)) ? 0 : 100 * (int)$sw->done / (int)$sw->planned;

                                ?>

                                @if($sw_work_done_in_percent>100)
                                    <tr class="bg-success">
                                @elseif($sw_work_done_in_percent<100)
                                    <tr class="bg-danger">
                                @elseif($sw_work_done_in_percent == 100)
                                    <tr class="bg-warning">
                                        @endif
                                        <td>{{$i++}}</td>
                                        <td>{{\App\Subcontractor::find($sw->subcontractor_id)->name}}</td>
                                        <td>{{$sw->quantity}}</td>
                                        <td>{{$sw->unit}}</td>
                                        <td>{{$sw->works_done}}</td>
                                        <td>{{$sw->planned}}</td>
                                        <td>{{$sw->done}}</td>
                                        <td>%{{$sw_work_done_in_percent}}</td>
                                    </tr>

                                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        @if(sizeof($inmaterials)>0)
            <div class="text-center" style="background-color: rgb(127,127,127)">
                <span><strong>GELEN MALZEMELER</strong></span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>S.N</th>
                        <th>GELEN MALZEME</th>
                        <th>BİRİM</th>
                        <th>MİK.</th>
                        <th>AÇIKLAMASI</th>
                    </tr>
                    </thead>
                    <tbody>

                    @for($i = 1; $i<=sizeof($inmaterials); $i++)

                        <tr>
                            <td>{{$i}}</td>
                            <td>{{TurkishChar::tr_up(\App\Material::find($inmaterials[$i-1]->material_id)->material)}}</td>
                            <td>{{TurkishChar::tr_up($inmaterials[$i-1]->unit)}}</td>
                            <td>{{$inmaterials[$i-1]->quantity}}</td>
                            <td>{{$inmaterials[$i-1]->explanation}}</td>
                        </tr>

                    @endfor

                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>