<?php
$staff_options = '<option></option>';
$management_depts = new \App\Department();

foreach ($management_depts->management() as $dept) {
    $staff_options .= "<optgroup label=\"$dept->department\">";
    foreach ($dept->staff()->get() as $staff) {
        $staff_options .= "<option value=\"$staff->id\">" . \App\Library\TurkishChar::tr_up($staff->staff) . "</option>";
    }
}
?>

<div class="form-group {{ $errors->has('tck_no') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('tck_no', 'TCK No:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-6">
            {!! Form::number('tck_no', null, ['class' => 'form-control', 'placeholder' => 'TCK no.su giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('name', 'Personelin Adı:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-6">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Ad soyad giriniz']) !!}

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

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('documents', 'Ek Dosyalar: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <input type="file" name="documents" id="documentsToUpload" multiple>
        </div>
    </div>
</div>

<div class="form-group pull-right">
    <button type="submit" class="btn btn-flat btn-primary" id="add-personnel">Personel
        Ekle
    </button>
</div>