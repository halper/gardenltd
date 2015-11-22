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
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(".js-example-basic-multiple").select2({
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });

        $('.dateRangePicker').datepicker({
            autoclose: true,
            firstDay: 1,
            format: 'dd.mm.yyyy',
            startDate: '01.01.2010',
            endDate: '30.12.2100'
        });
    </script>

@stop
@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Taşeron Ekle</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Taşeron Cari Hesap</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Taşeron Bilgileri Düzenle</a></li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-sm-12">
                                {!! Form::open([
                                                    'url' => "/tekil/$site->slug/add-subcontractor",
                                                    'method' => 'POST',
                                                    'class' => 'form .form-horizontal',
                                                    'id' => 'subcontractorInsertForm',
                                                    'role' => 'form',
                                                    'files' => true
                                                    ])!!}
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('name', 'Taşeronun Adı: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Taşeronun adını giriniz']) !!}

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
                                                <input type="text" class="form-control" name="contract_date"
                                                       placeholder="Sözleşme tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('contract_start_date') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('contract_start_date', 'Sözleşme Başlangıç Tarihi: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="input-group input-append date dateRangePicker">
                                                <input type="text" class="form-control" name="contract_start_date"
                                                       placeholder="Sözleşme başlangıç tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('contract_end_date') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('contract_end_date', 'Sözleşme Bitim Tarihi: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="input-group input-append date dateRangePicker">
                                                <input type="text" class="form-control" name="contract_end_date"
                                                       placeholder="Sözleşme bitim tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('manufacturings', 'İmalat Grubu: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            <select name="manufacturings[]"
                                                    class="js-example-basic-multiple form-control"
                                                    multiple>

                                                {!! $manufacturing_options !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

<div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('contract', 'Sözleşme Dosyası: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            <input type="file" name="contractToUpload" id="contractToUpload">
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-flat btn-primary">Şantiye Ekle</button>

                                    {!! Form::close() !!}
                                </div>
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

                    </div>
                    <!-- /.tab-pane -->
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </div>







@stop