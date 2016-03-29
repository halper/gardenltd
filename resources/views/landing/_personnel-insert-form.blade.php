<?php
$staff_options = '<option></option>';
$management_depts = new \App\Department();

foreach ($management_depts->management() as $dept) {
    $staff_options .= "<optgroup label=\"$dept->department\">";
    foreach ($dept->staff()->notGarden()->get() as $staff) {
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

<div class="form-group {{ $errors->has('iban') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('iban', 'IBAN No: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-6">
            {!! Form::text('iban', null, ['class' => 'form-control', 'placeholder' => 'Personelin IBAN no.sunu giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('staff_id') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('staff_id', 'İş Kolu:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-6">
            <select name="staff_id" class="staff-select form-control">
                {!! $staff_options !!}
            </select>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('exit_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('exit_date', 'Çıkış Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-6">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" class="form-control" name="exit_date"
                       placeholder="Çıkış tarihi seçiniz" />
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
    </div>
</div>

@if(!isset($wage_exists))
    <div class="form-group {{ $errors->has('salary') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('salary', 'Maaş:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-11">
                        {!! Form::text('salary', null, ['class' => 'form-control number', 'placeholder' => 'Personelin maaşını giriniz']) !!}
                    </div>
                    <div class="col-sm-1">
                        <span class="text-left">TL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(isset($wage_exists))
<div class="form-group {{ $errors->has('wage') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('wage', 'Günlük Ücret:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-6">
            <div class="row">
            <div class="col-sm-11">
                {!! Form::text('wage', null, ['class' => 'form-control number', 'placeholder' => 'Personelin günlük ücretini giriniz']) !!}
        </div>
                <div class="col-sm-1">
                    <span class="text-left">TL</span>
                </div>
        </div>
        </div>
    </div>
</div>
@endif

<div class="form-group {{ $errors->has('iddoc') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('iddoc', 'Nüfus Cüzdanı:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <input type="file" name="iddoc" id="idToUpload">
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
{!! Form::hidden('contract_date', '0000-00-00') !!}
{!! Form::hidden('contract_start_date', '0000-00-00') !!}
{!! Form::hidden('contract_end_date', '0000-00-00') !!}

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('documents[]', 'Ek Belgeler: ', ['class' => 'control-label']) !!}
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