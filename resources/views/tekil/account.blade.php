@extends('tekil/layout')

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-4 col-md-offset-4">
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Kasa Bilgileri</h3>

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
                        <table class="table table-condensed">
                            <tbody>

                            <tr>

                                <td><strong>KASA SAHİBİ:</strong></td>
                                <td>SADIK HERGÜL</td>

                            </tr>
                            <tr>
                                <td><strong>DÖNEM:</strong></td>
                                <td>2014 - 2015</td>
                            </tr>
                            <tr>
                                <td><strong>ŞANTİYE ADI:</strong></td>
                                <td>{{$site->job_name}}</td>
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

    <div class="row">
        <div class="col-xs-12 col-md-12">
        <div class="table-responsive">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th>S.N</th>
                    <th>TARİH</th>
                    <th>AÇIKLAMA</th>
                    <th>HARCAMAYI YAPAN</th>
                    <th>ÖDEME ŞEKLİ</th>
                    <th>GELİR</th>
                    <th>GİDER</th>
                    <th>KASA</th>
                </tr>
                </thead>
                <tbody>

                <tr class="bg-warning">
                    <td></td>
                    <td></td>
                    <td><strong>GENEL TOPLAM:</strong></td>
                    <td></td>
                    <td></td>
                    <td>392.020,25TL</td>
                    <td>391.553,76TL</td>
                    <td>466,49TL</td>

                </tr>
                <tr>
                    <td><strong>1</strong></td>
                    <td><strong>27.06.2014</strong></td>
                    <td>TOPOĞRAF EKİBİNE ÖDEME YAPILDI</td>
                    <td>SADIK HERGÜL</td>
                    <td>NAKİT</td>
                    <td><strong>1.000,00TL</strong></td>
                    <td><strong>1.000,00TL</strong></td>
                    <td>0,00TL</td>
                </tr>
                <tr>
                    <td><strong>2</strong></td>
                    <td><strong>03.07.2014</strong></td>
                    <td>KONTEYNER ELEKTRİK İŞÇİLİK ÜCRETİ</td>
                    <td>SADIK HERGÜL</td>
                    <td>NAKİT</td>
                    <td><strong></strong></td>
                    <td><strong>50,00TL</strong></td>
                    <td>-50,00TL</td>
                </tr>
                <tr>
                    <td><strong>3</strong></td>
                    <td><strong>07.07.2014</strong></td>
                    <td>PROJE FOTOKOPİSİ ÇEKİLDİ</td>
                    <td>SADIK HERGÜL</td>
                    <td>KREDİ KARTI</td>
                    <td><strong></strong></td>
                    <td><strong>20,00TL</strong></td>
                    <td>-70,00TL</td>
                </tr>
                <tr>
                    <td><strong>4</strong></td>
                    <td><strong>11.07.2014</strong></td>
                    <td>ASKİ EKİBİNE ÖDEME YAPILDI</td>
                    <td>SADIK HERGÜL</td>
                    <td>NAKİT</td>
                    <td><strong></strong></td>
                    <td><strong>20,00TL</strong></td>
                    <td>-90,00TL</td>
                </tr>
                <tr>
                    <td><strong>5</strong></td>
                    <td><strong>17.07.2014</strong></td>
                    <td>ELEKTRİK KABLOSU PRİZ APARATI ALINDI</td>
                    <td>SADIK HERGÜL</td>
                    <td>NAKİT</td>
                    <td><strong></strong></td>
                    <td><strong>4,50TL</strong></td>
                    <td>-94,50TL</td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>
    </div>

@stop