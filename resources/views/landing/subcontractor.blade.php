<?php


?>

@section('page-specific-css')

@stop

@section('page-specific-js')

@stop

@extends('landing/landing')

@section('content')

    <h2 class="page-header">
        {{\App\Library\TurkishChar::tr_camel($subdetail->name)}}
    </h2>

    {!! Form::open([
    'url' => "/guncelle/modify-subcontractor",
    'method' => 'POST',
    'class' => 'form',
    'id' => 'subcontractorModifyForm',
    'role' => 'form'
    ])!!}

    {!! Form::hidden('id', $subdetail->id) !!}

    @include('landing._subcontractor-insert-form')

    <div class="form-group pull-right">
        <button type="submit" class="btn btn-flat btn-primary" id="add-personnel">Alt Yüklenici
            Güncelle
        </button>
    </div>

@stop