<?php
use App\Site;
?>

@extends('landing/landing')

@section('content')

    <h2 class="page-header">
        {{$user->name}}
    </h2>

    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Kullanıcı</a></li>
                <li><a href="#tab_2" data-toggle="tab">Şantiye Erişim</a></li>
                <li><a href="#tab_3" data-toggle="tab">Modül İzinleri</a></li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    {!!  Form::model($user,
                    ['action' => ['AdminController@update', $user],
                    'method' => 'PATCH',
                    'class' => 'form',
                    'id' => 'userEditForm',
                    'role' => 'form']) !!}

                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('name', 'Kullanıcı adı: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Kullanıcı adı giriniz']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('email', 'E-Posta: ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Bir e-posta adresi girin']) !!}

                                @if($errors->first('email') == "The email has already been taken.")
                                    {!! '<span class="help-block">E-posta adresi zaten kayıtlı' !!}
                                @endif

                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('password', 'Şifre ', ['class' => 'control-label']) !!}
                            </div>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control" placeholder="Şifre">
                                <span class="help-block">Şifreniz en az 6 karakterden oluşmalı</span>

                            </div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                        <div class="row">
                            <div class="col-sm-2">
                                {!! Form::label('password_confirmation', 'Şifre tekrar ', ['class' => 'control-label']) !!}

                            </div>
                            <div class="col-sm-10">
                                <input type="password" name="password_confirmation" class="form-control"
                                       placeholder="Şifreyi tekrar giriniz">
                                @if($errors->first('password') == "The password confirmation does not match.")
                                    {!! '<span class="help-block">Girmiş olduğunuz şifreler uyuşmuyor' !!}
                                @endif
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Kaydet</button>

                    {!! Form::close()  !!}
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    Kullanıcın erişim sağlayabileceği şantiyeleri seçin.

                        @foreach(Site::getSites() as $site)
                        <br>{!! Form::checkbox('asap',null,$user->hasSite($site->id), array('id'=>'asap')) !!}{{$site->job_name}}
                        @endforeach

                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_3">
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                    when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                    It has survived not only five centuries, but also the leap into electronic typesetting,
                    remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset
                    sheets containing Lorem Ipsum passages, and more recently with desktop publishing software
                    like Aldus PageMaker including versions of Lorem Ipsum.
                </div>
                <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
@stop