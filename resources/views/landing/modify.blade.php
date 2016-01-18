<?php

$personnel = \App\Personnel::sitePersonnel()->get();
$subcontractor = \App\Subdetail::all();
$equipments = \App\Equipment::all();
$materials = \App\Material::all();
$departments = \App\Department::all();
        $staff = \App\Staff::all();

?>


@extends('landing/landing')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/bootstrap-editable.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/bootstrap-editable.min.js" type="text/javascript"></script>
    <script>
        $.fn.editable.defaults.mode = 'inline';

        $(document).on("click", ".userDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #userDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> adlı kullanıcıyı silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'userDeleteIn',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteUserConfirm').modal('show');
        });
        $(document).on("click", ".subDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #subDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> adlı alt yükleniciyi silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'userDeleteIn',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteSubcontractorConfirm').modal('show');
        });
        $(document).ready(function () {
            $('.inline-edit').editable({
                validate: true
            });
        });
        $('.inline-edit').on('save', function(e, params) {
            if(params.newValue.length == 0){
                $(this).parent().closest('div.col-sm-4').remove();
            }
        });
    </script>
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
                                                        <a href="{{"personel-duzenle/$per->id"}}"
                                                           class="btn btn-flat btn-warning btn-sm">Düzenle</a>
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
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Alt Yüklenici</th>
                                        <th>Firma Yetkilisi</th>
                                        <th>Tel. No</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($subcontractor as $sub)
                                        <tr>
                                            <td>{{ \App\Library\TurkishChar::tr_camel($sub->name) }}</td>
                                            <td>{{ $sub->official }}</td>
                                            <td>{{ $sub->mobilecode->code . " $sub->mobile" }}</td>

                                            <td>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <a href="{{"altyuklenici-duzenle/$sub->id"}}"
                                                           class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <?php
                                                        echo '<button type="button" class="btn btn-flat btn-danger btn-sm subDelBut" data-id="' . $sub->id . '" data-name="' . $sub->name . '" data-toggle="modal" data-target="#deleteSubcontractorConfirm">Sil</button>';
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

                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                @foreach($staff as $st)
                                    <div class="col-sm-3">
                                        <a href="#" class="inline-edit" data-type="text"
                                           data-pk="{{$st->id}}"
                                           data-url="/admin/modify-staff"
                                           data-title="İş Kolu">{{$st->staff}}</a>
                                    </div>
                                <div class="col-sm-3">
                                        <a href="#" class="inline-select2" data-type="select2"
                                           data-pk="{{$st->id}}"
                                           data-url="/admin/modify-staff"
                                           data-title="İş Kolu"
                                           data-dept-id = "{{$st->department->id}}">{{$st->department->department}}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                @foreach($departments as $department)
                                    <div class="col-sm-4">
                                        <a href="#" class="inline-edit" name="name" data-type="text"
                                           data-pk="{{$department->id}}"
                                           data-url="/admin/modify-department"
                                           data-title="Departman">{{$department->department}}</a>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_mat">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                @foreach($materials as $material)
                                    <div class="col-sm-4">
                                        <a href="#" class="inline-edit" name="name" data-type="text"
                                           data-pk="{{$material->id}}"
                                           data-url="/admin/modify-material"
                                           data-title="Malzeme">{{$material->material}}</a>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    @foreach($equipments as $equipment)
                                        <div class="col-sm-4">
                                            <a href="#" class="inline-edit" name="name" data-type="text"
                                               data-pk="{{$equipment->id}}"
                                               data-url="/admin/modify-equipment"
                                               data-title="İş Makinesi">{{$equipment->name}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /.tab-pane -->

            </div>
            <!-- nav-tabs-custom -->
        </div>
    </div>

    <div id="deleteUserConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Personel Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => '/admin/del-personnel',
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'userDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-flat btn-warning">Sil</button>
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

    <div id="deleteSubcontractorConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Alt Yüklenici Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => '/admin/del-subcontractor',
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'subDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-flat btn-warning">Sil</button>
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>


@stop