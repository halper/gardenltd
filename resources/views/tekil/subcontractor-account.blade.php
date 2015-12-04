<?php
use App\Library\TurkishChar;

$manufacturing_options = '';

foreach (\App\Manufacturing::all() as $manufacture) {
    $manufacturing_options .= "'<option value=\"$manufacture->id\">" . TurkishChar::tr_up($manufacture->name) . "</option>'+\n";
}

?>


@extends('tekil/layout')

@section('page-specific-css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script>
        $(document).ready(function () {
            $(".js-example-basic-multiple").select2({
                placeholder: "Çoklu seçim yapabilirsiniz",
                allowClear: true
            });

            $(".select2-container").width('100%');
            $(".select2-search--inline").width('99%');
        });

        $(document).on("click", ".subcontractorDelBut", function (e) {

            e.preventDefault();
            var mySubcontractorId = $(this).data('id');
            var mySubcontractorName = $(this).data('name');
            var myForm = $('.modal-footer #subcontractorDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + mySubcontractorName + "</em> adlı taşeronu silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'subId',
                value: mySubcontractorId
            }).appendTo(myForm);
            $('#deleteSubcontractorConfirm').modal('show');


        });

        $('.dateRangePicker').datepicker({
            language: 'tr'
        });


    </script>

@stop
@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Taşeron Bilgileri Düzenle</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Taşeron Cari Hesap</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Taşeron Ekle</a></li>

                </ul>
                <div class="tab-content">

                    <!-- /.tab-pane -->
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Taşeron</th>
                                        <th>Sözleşme Tarihi</th>
                                        <th>Sözleşme Başlangıç</th>
                                        <th>Sözleşme Bitiş</th>
                                        <th>Sözleşme Dosyası</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($site->subcontractor()->get() as $sub)
                                        <tr>
                                            <td>{{ $sub->name}}</td>

                                            <td>{{ strpos($sub->pivot->contract_date,"0000-00-00") !== false ? "Girilmedi" : \App\Library\CarbonHelper::getTurkishDate($sub->pivot->contract_date) }}</td>

                                            <td>
                                                {{strpos($sub->pivot->contract_start_date,"0000-00-00") !== false ? "Girilmedi" : \App\Library\CarbonHelper::getTurkishDate($sub->pivot->contract_start_date)}}
                                            </td>
                                            <td>{{strpos($sub->pivot->contract_end_date,"0000-00-00") !== false ? "Girilmedi" : \App\Library\CarbonHelper::getTurkishDate($sub->pivot->contract_end_date)}}</td>
                                            <td>
                                                <?php
                                                $my_path = '';
                                                $file_name = '';


                                                if (!is_null($sub->sfile)) {
                                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $sub->sfile->file->path);
                                                    $file_name = $sub->sfile->file->name;
                                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                                }
                                                ?>
                                                <a href="{{!empty($my_path) ? $my_path : ""}}">
                                                {{!empty($file_name) ? $file_name : ""}}
                                            </a></td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <a href="{{"alt-yuklenici-duzenle/$sub->id"}}"
                                                           class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                    </div>
                                                    <div class="col-sm-2 col-sm-offset-1">
                                                        <?php
                                                        echo '<button type="button" class="btn btn-flat btn-danger btn-sm subcontractorDelBut" data-id="' . $sub->id . '" data-name="' . $sub->name . '" data-toggle="modal" data-target="#deleteSubcontractorConfirm">Sil</button>';
                                                        ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="box box-success box-solid">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">DOĞUKENT MEKANİK
                                            <small style="color: #F3f3f3">CARİ HESAP KARTI</small>
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
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-condensed">
                                                <thead class="bg-success">
                                                <tr>
                                                    <th> Sıra</th>
                                                    <th> Hesap Adı</th>
                                                    <th> Tarihi</th>
                                                    <th> Fiş Numarası</th>
                                                    <th> Açıklama</th>
                                                    <th> Borç</th>
                                                    <th> Alacak Tutarı</th>
                                                    <th> Bakiye</th>


                                                </tr>
                                                </thead>
                                                <tbody>

                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td> GENEL TOPLAM</td>
                                                    <td> 383,202.50 TL</td>
                                                    <td> 401,200.00 TL</td>
                                                    <td> -17,997.50 TL</td>
                                                </tr>
                                                <tr>
                                                    <td> 1</td>
                                                    <td> MEKANİK</td>
                                                    <td> SÖZLEŞME</td>
                                                    <td> SÖZLEŞME</td>
                                                    <td> ANAHTAR TESLİMİ GÖTÜRÜ BEDEL TEKLİF BEDELİ</td>
                                                    <td> 0.00 TL</td>
                                                    <td> 401,200.00 TL</td>
                                                    <td> -401,200.00 TL</td>

                                                </tr>

                                                <tr>
                                                    <td> 2</td>
                                                    <td> MEKANİK</td>
                                                    <td> SÖZLEŞME</td>
                                                    <td> SÖZLEŞME</td>
                                                    <td> ALL-RİSK SİGORTA BEDELİ HİSSESİ</td>
                                                    <td> 210.00 TL</td>
                                                    <td></td>
                                                    <td> -400,990.00 TL</td>

                                                </tr>

                                                <tr>
                                                    <td> 3</td>
                                                    <td> MEKANİK</td>
                                                    <td> SÖZLEŞME</td>
                                                    <td> SÖZLEŞME</td>
                                                    <td> EYLÜL-EKİM-KASIM-ARALIK-OCAK-MART-NİSAN-MAYIS- İSG ÜCRETİ</td>
                                                    <td> 1,600.00 TL</td>
                                                    <td></td>
                                                    <td> -399,390.00 TL</td>

                                                </tr>

                                                <tr>
                                                    <td> 5</td>
                                                    <td> MEKANİK</td>
                                                    <td> TOPLAM</td>
                                                    <td> VERGİ DAİRESİ</td>
                                                    <td> KDV TEVFİKATI(TOPLAM)</td>
                                                    <td> 12,240.00 TL</td>
                                                    <td></td>
                                                    <td> -387,150.00 TL</td>

                                                </tr>

                                                <tr>
                                                    <td> 6</td>
                                                    <td> MEKANİK</td>
                                                    <td> 26.03.2015</td>
                                                    <td></td>
                                                    <td> YKB ÇEK 1428143 13.06.2015</td>
                                                    <td> 18,000.00 TL</td>
                                                    <td></td>
                                                    <td> -369,150.00 TL</td>

                                                </tr>

                                                <tr>
                                                    <td> 7</td>
                                                    <td> MEKANİK</td>
                                                    <td> 26.03.2015</td>
                                                    <td></td>
                                                    <td> YKB ÇEK 1428144 11.07.2015</td>
                                                    <td> 23,000.00 TL</td>
                                                    <td></td>
                                                    <td> -346,150.00 TL</td>

                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>


                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-sm-12">

                                <div class="row">
                                    {!! Form::open([
                                                    'url' => "/tekil/$site->slug/add-subcontractor",
                                                    'method' => 'POST',
                                                    'class' => 'form .form-horizontal',
                                                    'id' => 'subcontractorInsertForm',
                                                    'role' => 'form',
                                                    ])!!}


                                    @foreach(App\Subcontractor::all() as $subcontractor)

                                        @if(!$site->hasSubcontractor($subcontractor->id))
                                        <div class="col-md-4 col-xs-6">
                                            <label class="checkbox-inline">
                                                {!! Form::checkbox('subcontractors[]', $subcontractor->id, $site->hasSubcontractor($subcontractor->id),
                                                [
                                                'id'=>$subcontractor->id
                                                ])
                                                !!}{{ $subcontractor->name}}</label>
                                        </div>
                                        @endif

                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-md-4 col-md-offset-3">
                                        <div class="form-group">
                                            <br>
                                            <br>
                                            <button type="submit" class="btn btn-primary btn-flat btn-block">
                                                Güncelle
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>


                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </div>



    <div id="deleteSubcontractorConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Taşeron Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/del-subcontractor",
                    'method' => 'PATCH',
                    'class' => 'form',
                    'id' => 'subcontractorDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-warning btn-flat">Sil</button>
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>



@stop