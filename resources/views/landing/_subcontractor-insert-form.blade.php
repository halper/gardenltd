<?php

$city_options = '<option></option>';
$phone_options = '<option></option>';
$fax_options = '<option></option>';
$mobile_options = '<option></option>';
if (!isset($subdetail)) {
    $subdetail = null;
}

foreach (\App\City::all() as $city) {
    if (!is_null($subdetail) && $city->id == $subdetail->city->id)
        $city_options .= "<option value=\"$city->id\" selected>" . \App\Library\TurkishChar::tr_up($city->name) . "</option>";
    else
        $city_options .= "<option value=\"$city->id\">" . \App\Library\TurkishChar::tr_up($city->name) . "</option>";
}

foreach (\App\AreaCode::all() as $area) {
    if (!is_null($subdetail) && $area->id == $subdetail->areacode->id)
        $phone_options .= "<option value=\"$area->id\" selected>$area->code</option>";
    else
        $phone_options .= "<option value=\"$area->id\">$area->code</option>";

    if (!is_null($subdetail) && $area->id == $subdetail->faxcode->id)
        $fax_options .= "<option value=\"$area->id\" selected>$area->code</option>";
    else
        $fax_options .= "<option value=\"$area->id\">$area->code</option>";
}

foreach (\App\MobileCode::all() as $mobile) {
    if (!is_null($subdetail) && $mobile->id == $subdetail->mobilecode->id)
        $mobile_options .= "<option value=\"$mobile->id\" selected>$mobile->code</option>";
    else
        $mobile_options .= "<option value=\"$mobile->id\">$mobile->code</option>";
}


?>

<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('name', 'Alt Yüklenicinin Adı:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('name', is_null($subdetail) ? null : $subdetail->name, ['class' => 'form-control', 'placeholder' => 'Alt yüklenicinin adını giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('address', 'Açık Adresi:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::textarea('address', is_null($subdetail) ? null : $subdetail->address, ['class' => 'form-control',
                                                'placeholder' => 'Alt yüklenicinin adresini giriniz',
                                                 'rows' => '2']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('city_id') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('city_id', 'Şehir:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <select name="city_id"
                    class="city-select form-control">

                {!! $city_options !!}
            </select>

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('official') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('official', 'Firma Yetkilisinin Adı:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('official', is_null($subdetail) ? null : $subdetail->official, ['class' => 'form-control', 'placeholder' => 'Firma yetkilisinin adını giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('title', 'Firma Yetkilisinin Unvanı:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('title', is_null($subdetail) ? null : $subdetail->title, ['class' => 'form-control', 'placeholder' => 'Firma yetkilisinin unvanını giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('phone', 'Telefon numarası:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-2">
            <select name="area_code_id"
                    class="mobile-select form-control">

                {!! $phone_options !!}
            </select>

        </div>
        <div class="col-sm-3">
            {!! Form::number('phone', is_null($subdetail) ? null : $subdetail->phone, ['class' => 'form-control', 'placeholder' => 'Telefon numarası giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('fax') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('fax', 'Fax numarası:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-2">
            <select name="fax_code_id"
                    class="mobile-select form-control">

                {!! $fax_options !!}
            </select>

        </div>
        <div class="col-sm-3">
            {!! Form::number('fax', is_null($subdetail) ? null : $subdetail->fax, ['class' => 'form-control', 'placeholder' => 'Fax numarası giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('mobile', 'Cep numarası:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-2">
            <select name="mobile_code_id"
                    class="mobile-select form-control">

                {!! $mobile_options !!}
            </select>

        </div>
        <div class="col-sm-3">
            {!! Form::number('mobile', is_null($subdetail) ? null : $subdetail->mobile, ['class' => 'form-control', 'placeholder' => 'Cep numarası giriniz']) !!}

        </div>
    </div>
</div>


<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('email', 'E-posta Adresi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::email('email', is_null($subdetail) ? null : $subdetail->email, ['class' => 'form-control', 'placeholder' => 'E-posta adresini giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('web') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('web', 'Web Adresi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('web', is_null($subdetail) ? null : $subdetail->web, ['class' => 'form-control', 'placeholder' => 'Web adresini giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('tax_office') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('tax_office', 'Vergi Dairesi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('tax_office', is_null($subdetail) ? null : $subdetail->tax_office, ['class' => 'form-control', 'placeholder' => 'Vergi dairesini giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('tax_number') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('tax_number', 'Vergi Numarası:* ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('tax_number', is_null($subdetail) ? null : $subdetail->tax_number, ['class' => 'form-control', 'placeholder' => 'Vergi numarasını giriniz']) !!}

        </div>
    </div>
</div>