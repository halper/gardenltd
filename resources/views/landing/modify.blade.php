<?php

        $personnel = \App\Personnel::sitePersonnel()->get();

?>


@extends('landing/landing')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_5" data-toggle="tab">Personel</a></li>
                    <li><a href="#tab_1" data-toggle="tab">Alt Yüklenici</a></li>
                    <li><a href="#tab_2" data-toggle="tab">İş Kolu</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Departman</a></li>
                    <li><a href="#tab_mat" data-toggle="tab">Malzeme</a></li>
                    <li><a href="#tab_4" data-toggle="tab">İş Makinesi</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_5">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Adı-Soyadı</th>
                                        <th>TCK No</th>
                                        <th>Kullanıcı İşlemleri</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($personnel as $per)
                                        <tr>
                                            <td>{{ \App\Library\TurkishChar::tr_camel($per->name) }}</td>
                                            <td>{{ $per->tck_no }}</td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <a href="{{"personel-duzenle/$per->id"}}" class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <?php
                                                        echo '<button type="button" class="btn btn-flat btn-danger btn-sm userDelBut" data-id="' . $per->id . '" data-name="' . $per->name . '" data-toggle="modal" data-target="#deleteUserConfirm">Sil</button>';
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

                    <div class="tab-pane" id="tab_1">
                        <div class="row">
                            <div class="col-sm-12">

                            </div>
                        </div>
                    </div>

                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">

                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">


                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_mat">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">



                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">


                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->

                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </div>

@stop