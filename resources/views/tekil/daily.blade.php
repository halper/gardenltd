@extends('tekil/layout')

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-4 col-md-offset-3">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Şantiye Günlük Raporu</h3>

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
                                <td><strong>PROJE ADI:</strong></td>
                                <td>{{$site->job_name}}</td>
                            </tr>
                            <tr>
                                <td><strong>TARİH:</strong></td>
                                <td>{{Carbon\Carbon::today('Europe/istanbul')->format('d.m.Y')}}</td>
                            </tr>
                            <tr>
                                <?php
                                $time = strtotime($site->end_date);
                                $myFormatForView = date("d.m.Y", $time);

                                $start_date = date_create($site->start_date);
                                $now = date_create();
                                $end_date = date_create($site->end_date);
                                $left = str_replace("+", "", date_diff($now, $end_date)->format("%R%a"));
                                ?>
                                <td><strong>İŞ BİTİM TARİHİ:</strong></td>
                                <td>{{$myFormatForView}}</td>
                            </tr>
                            <tr>
                                <td><strong>KALAN SÜRE:</strong></td>
                                <td>{{$left}}</td>
                            </tr>
                            <tr>
                                <td><strong>HAVA DURUMU:</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>ŞANTİYE ŞEFİ:</strong></td>
                                <td>{{$site->site_chief}}</td>
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
        <div class="col-xs-12 col-md-6">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Personel Raporu</h3>

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
                            <thead>
                            <tr>
                                <th>Firma</th>
                                <th>Puantaj</th>
                                <th>Yapılan İş ve Mahali</th>
                                <th>Ödemeler</th>
                                <th>Malzeme Talep</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>Garden İnşaat</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Elektrik</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Tesisat</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Sıva</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Güvenlik</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Toplam</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Gelen Malzeme</h3>

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
                            <thead>
                            <tr>
                                <th>Kimden</th>
                                <th>Malzeme Cinsi</th>

                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>Garden İnşaat</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Elektrik</td>
                                <td></td>

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
        <div class="col-xs-12 col-md-6">
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">İş Makinesi ve Ekipman</h3>

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
                                <td>Kule Vinç</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Kamyon</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Eskavatör</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Beko Loader</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Vinç</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>High Up</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Gırgır Vinç</td>
                                <td></td>

                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="box box-warning box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Şantiye Notları</h3>

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
                                <td></td>

                            </tr>

                            <tr>
                                <td></td>

                            </tr>

                            <tr>
                                <td></td>

                            </tr>

                            <tr>
                                <td></td>

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

@stop