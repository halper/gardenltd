<?php

$staff_options = '<option></option>';
$management_depts = new \App\Department();

foreach ($management_depts->management() as $dept) {
    $staff_options .= "<optgroup label=\"$dept->department\">";
    foreach ($dept->staff()->notGarden()->get() as $staff) {
        if ((int)$personnel->staff->id == (int)$staff->id)
            $staff_options .= "<option value=\"$staff->id\" selected>" . \App\Library\TurkishChar::tr_up($staff->staff) . "</option>";
        else
            $staff_options .= "<option value=\"$staff->id\">" . \App\Library\TurkishChar::tr_up($staff->staff) . "</option>";
    }
}
?>

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>

    <script>
        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

        function removeFiles(fid) {
            $.ajax({
                type: 'POST',
                url: '{{"/admin/delete-personnel-files"}}',
                data: {
                    "fileid": fid
                }
            }).success(function () {
                var linkID = "lb-link-" + fid;
                $('#' + linkID).remove();
            });

        }
    </script>
@stop

@extends('tekil.layout')

@section('content')

    <h2 class="page-header">
        {{\App\Library\TurkishChar::tr_camel($personnel->name)}}
    </h2>

    {!! Form::open([
    'url' => "/tekil/modify-personnel",
    'method' => 'POST',
    'class' => 'form',
    'id' => 'personnelModifyForm',
    'role' => 'form',
    'files' => true
    ])!!}

    {!! Form::hidden('id', $personnel->id) !!}

    <div class="form-group {{ $errors->has('tck_no') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('tck_no', 'TCK No:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::number('tck_no', $personnel->tck_no, ['class' => 'form-control', 'placeholder' => 'TCK no.su giriniz']) !!}

            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('name', 'Personelin Adı:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::text('name', \App\Library\TurkishChar::tr_camel($personnel->name), ['class' => 'form-control', 'placeholder' => 'Ad soyad giriniz']) !!}

            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('iban') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('iban', 'IBAN No: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::text('iban', $personnel->iban, ['class' => 'form-control', 'placeholder' => 'Personelin IBAN no.sunu giriniz']) !!}

            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('staff_id') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('staff_id', 'İş Kolu: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                <select name="staff_id" class="staff-select form-control">
                    {!! $staff_options !!}
                </select>
            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('wage') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('wage', 'Günlük Ücret: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-11">
                        {!! Form::text('wage', str_replace('.', ',', $personnel->wage()->orderBy('since', 'DESC')->first()->wage), ['class' => 'form-control number', 'placeholder' => 'Personelin günlük ücretini giriniz']) !!}
                    </div>
                    <div class="col-sm-1">
                        <span class="text-left">TL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('contract') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('contract', 'İşe Giriş Belgesi:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                <input type="file" name="contract" id="contractToUpload">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-sm-2"><strong>Mevcut Giriş Belgesi: </strong></div>
            <div class="col-sm-10">
                <?php
                $my_path = '';
                $file_name = '';

                if (!empty($personnel->contract->first())) {
                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $personnel->contract->first()->file()->orderBy('created_at','DESC')->first()->path);
                    $file_name = $personnel->contract->first()->file()->orderBy('created_at', 'DESC')->first()->name;
                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                }
                ?>
                <a href="{{!empty($my_path) ? $my_path : ""}}">
                    {{!empty($file_name) ? $file_name : ""}}
                </a>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('documents', 'Ek Belgeler: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                <input type="file" name="documents[]" id="documentsToUpload" multiple>
            </div>
        </div>
    </div>

    @if(!empty($personnel->photo->first()))
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12">
                    <h4>Kayıtlı Belgeler</h4>
                </div>
            </div>
            @foreach($personnel->photo as $photo)
                <?php
                $my_path_arr = explode(DIRECTORY_SEPARATOR, $photo->file->first()->path);
                $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                if (strpos($photo->file->first()->name, 'pdf') !== false) {
                    $image = URL::to('/') . "/img/pdf.jpg";
                } elseif (strpos($photo->file->first()->name, 'doc') !== false) {
                    $image = URL::to('/') . "/img/word.png";
                } else {
                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $photo->file->first()->name;
                }
                ?>

                <a id="lb-link-{{$photo->id}}" href="{{$image}}"
                   data-toggle="lightbox" data-gallery="personnel-photos"
                   data-title = "{{$photo->file->first()->name}}"
                   data-footer="<a data-dismiss='modal' class='remove-files' href='#' onclick='removeFiles({{$photo->id}})'>Dosyayı Sil<a/>"
                   class="col-sm-4">
                    <img src="{{$image}}" class="img-responsive" style="height: 45px">
                    {{$photo->file->first()->name}}
                </a>

            @endforeach
        </div>
    @endif

    <div class="form-group pull-right">
        <button type="submit" class="btn btn-flat btn-primary" id="add-personnel">Personel
            Güncelle
        </button>
    </div>

@stop