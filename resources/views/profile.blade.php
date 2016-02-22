<?php
$user = Auth::user();
?>

@extends('landing.landing')

@section('content')

    <form action="/bilgilerim/update" method="POST">
        {{csrf_field()}}
    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('name', 'Adınız: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                {!! Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => 'Adınızı giriniz']) !!}
            </div>


        </div>
    </div>

    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('email', 'E-Posta: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                {!! Form::text('email', $user->email, ['class' => 'form-control', 'placeholder' => 'E-posta adresiniz']) !!}

                @if($errors->first('email') == "The email has already been taken.")
                    {!! '<span class="help-block">E-posta adresi zaten kayıtlı</span>' !!}
                @endif

            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('employer') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('employer', 'Firma ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                {!! Form::text('employer', $user->employer, ['class' => 'form-control', 'placeholder' => 'Firmanızı giriniz']) !!}
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
                    {!! '<span class="help-block">Girmiş olduğunuz şifreler uyuşmuyor</span>' !!}
                @endif
            </div>
        </div>
    </div>
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                    <button type="submit" class="btn btn-block btn-primary btn-flat">Kaydet</button>
                </div>
            </div>
        </div>
    </form>

@endsection