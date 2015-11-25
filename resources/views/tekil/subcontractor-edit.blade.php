@extends('tekil.layout')

@section('page-specific-css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <script>
        $(".js-example-basic-multiple").select2({
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });
    </script>
@stop

@section('content')
    <h2>{{$subcontractor->name}}</h2>
    {!! Form::model($subcontractor, [
                                                    'url' => "/tekil/$site->slug/update-subcontractor",
                                                    'method' => 'POST',
                                                    'class' => 'form .form-horizontal',
                                                    'id' => 'subcontractorEditForm',
                                                    'role' => 'form',
                                                    'files' => true
                                                    ])!!}
    {!! Form::hidden('sub-id', $subcontractor->id) !!}
    @include('tekil._subcontractor-form')

    <div class="row">
        <div class="col-sm-2"><strong>Sözleşme: </strong></div>
        <div class="col-sm-10">
            <?php
            $my_path = '';
            $file_name = '';

            if (!is_null($subcontractor->sfile)) {
                $my_path_arr = explode(DIRECTORY_SEPARATOR, $subcontractor->sfile->file->path);
                $file_name = $subcontractor->sfile->file->name;
                $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
            }
            ?>
            <a href="{{!empty($my_path) ? $my_path : ""}}">
                {{!empty($file_name) ? $file_name : ""}}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-md-offset-4">
            <div class="form-group">
                <button type="submit" class="btn btn-flat btn-primary btn-block">Şantiye Düzenle</button>
            </div>
        </div>
    </div>

    {!! Form::close() !!}

@stop