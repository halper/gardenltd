<?php

if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = '';
}
?>

@extends('landing/landing')

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
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

        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.sites = $scope.init;
            $scope.users = [];
            $scope.account = [];
            $scope.showRest = false;
            $scope.selected = null;
            $scope.selectUser = null;

            $scope.owner = '';
            $scope.cardOwner = '';
            $scope.period = '';

            $scope.showRest = false;
            $scope.loading = true;
            $scope.message = '';


            $scope.init = function () {

                $http.get("{{url("/santiye/sites")}}")
                        .then(function (response) {
                            $scope.sites = response.data;
                        });
                $http.get("{{url("/santiye/users")}}")
                        .then(function (response) {
                            $scope.users = response.data;
                        });
            };

            $scope.getAccInfo = function () {
                $scope.selectUser = null;
                $http.post("{{url("/santiye/account-details")}}", {
                            'id': $scope.selected.id
                        }
                ).then(function (response) {
                    $scope.loading = false;
                    $scope.showRest = true;
                    $scope.account = response.data;
                    $scope.owner = '';
                    $scope.cardOwner = $scope.account.cardOwner;
                    $scope.period = $scope.account.period;

                    if ($scope.account.uid) {
                        angular.forEach($scope.users, function (value) {
                            if (parseInt(value.id) == parseInt($scope.account.uid)) {
                                $scope.selectUser = value;
                            }
                        })
                    }
                });

            };

            $scope.saveAccount = function () {
                $scope.message = '';
                $http.post("{{url("/santiye/save-account")}}", {
                            id: $scope.account.id,
                            uid: $scope.selectUser.id,
                            card_owner: $scope.cardOwner,
                            period: $scope.period
                        }
                ).then(function () {
                    $scope.message = 'Kayıt başarılı';
                });
            };

        });

        $(document).ready(function () {
            angular.element('#angPuantaj').scope().init();
        });

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
                    <li {{$tab == 2 ? 'class=active' : ''}}><a href="#tab_2" data-toggle="tab">Kasa</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">


                    <div class="tab-pane {{empty($tab) ? 'active' : ''}}" id="tab_5">
                        @include('landing._ayarlar-users')
                    </div>

                    <div class="tab-pane {{$tab == 1 ? 'active' : ''}}" id="tab_1">
                        @include('landing._ayarlar-demands')
                    </div>
                    <div class="tab-pane {{$tab == 2 ? 'active' : ''}}" id="tab_2">
                        @include('landing._ayarlar-account')
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

