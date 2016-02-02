<?php

if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = '';
}
?>

@extends('landing/landing')

@section('page-specific-js')
    <script>
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
        $(document).on("click", ".demandRejectBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('#rejectDemandForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> tarihli talebi reddetme sebebiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'demand_id',
                value: myUserId
            }).appendTo(myForm);
            $('#rejectDemandConfirm').modal('show');
        });

        if ($('.has-error')[0]) {
            $('#insertUser').modal('show');
        }

    </script>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li {{empty($tab) ? 'class=active' : ''}}><a href="#tab_5" data-toggle="tab">Kullanıcı</a></li>
                    <li {{$tab == 1 ? 'class=active' : ''}}><a href="#tab_1" data-toggle="tab">Talep</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">


                    <div class="tab-pane {{empty($tab) ? 'active' : ''}}" id="tab_5">
                        @include('landing._ayarlar-users')
                    </div>

                    <div class="tab-pane {{$tab == 1 ? 'active' : ''}}" id="tab_1">
                        @include('landing._ayarlar-demands')
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div id="deleteUserConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Kullanıcı Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => '/admin/del-user',
                    'method' => 'PATCH',
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

    <div id="insertUser" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Yeni Kullanıcı Ekle</h4>
                </div>
                <div class="modal-body">
                    {!! Form::model($users, [
                    'url' => '/admin/add-user',
                    'method' => 'POST',
                    'class' => 'form .form-horizontal',
                    'id' => 'userInsertForm',
                    'role' => 'form'
                    ]) !!}
                    @include('landing._user-form')


                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-flat btn-primary">Kullanıcı ekle</button>
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

    <div id="rejectDemandConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Talep Reddet</h4>
                </div>
                <div class="modal-body">
                    <div class="userDel"></div>
                    {!! Form::open([
                    'url' => '/admin/reject-demand',
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'rejectDemandForm',
                    'role' => 'form'
                    ]) !!}

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <textarea class="form-control" name="reason" rows="4" cols="30"></textarea>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-flat btn-primary">Reddet</button>
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

@stop

