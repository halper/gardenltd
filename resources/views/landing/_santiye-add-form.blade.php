<?php

$city_options = '<option value="" selected disabled>Şehir Seçiniz</option>';

foreach (\App\City::all() as $city) {
    if (!isset($site) && $city->id == $site->city->id)
        $city_options .= "<option value=\"$city->id\" selected>" . \App\Library\TurkishChar::tr_up($city->name) . "</option>";
    elseif (isset($site) && $city->id == $site->city->id)
        $city_options .= "<option value=\"$city->id\" selected>" . \App\Library\TurkishChar::tr_up($city->name) . "</option>";
    else
        $city_options .= "<option value=\"$city->id\">" . \App\Library\TurkishChar::tr_up($city->name) . "</option>";
}
$job_name = isset($santiye) ? null : $site->job_name;
$code = isset($santiye) ? null : $site->code;
$management_name = isset($santiye) ? null : $site->management_name;
$employer = isset($santiye) ? null : $site->employer;
$start_date = isset($santiye) ? null : \App\Library\CarbonHelper::getTurkishDate($site->start_date);
$contract_date = isset($santiye) ? null : \App\Library\CarbonHelper::getTurkishDate($site->contract_date);
$end_date = isset($santiye) ? null : \App\Library\CarbonHelper::getTurkishDate($site->end_date);
$address = isset($santiye) ? null : $site->address;
$isg = isset($santiye) ? null : empty($site->isg) ? null : $site->isg;
$contract_worth = isset($santiye) ? null : empty($site->contract_worth) ? null : \App\Library\TurkishChar::convertToTRcurrency($site->contract_worth);
$extra_cost = isset($santiye) ? null : empty($site->extra_cost) ? null : \App\Library\TurkishChar::convertToTRcurrency($site->extra_cost);

$site_chief = isset($santiye) ? null : $site->site_chief;
$main_contractor = isset($santiye) ? null : $site->main_contractor;
$building_control = isset($santiye) ? null : $site->building_control;
?>

<div class="form-group {{ $errors->has('job_name') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('job_name', 'İşin Adı: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('job_name', $job_name, ['class' => 'form-control', 'placeholder' => 'İşin adını giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('code', 'Şantiye Kodu: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('code', $code, ['class' => 'form-control', 'placeholder' =>
            'Şantiye kodu giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('management_name') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('management_name', 'İdarenin Adı: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('management_name', $management_name, ['class' => 'form-control', 'placeholder' =>
            'İdarenin adını giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('employer') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('employer', 'İşverenin Adı: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('employer', $employer, ['class' => 'form-control', 'placeholder' =>
            'İşverenin adını giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('building_control') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('building_control', 'Yapı Denetim: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('building_control', $building_control, ['class' => 'form-control', 'placeholder' =>
            'Yapı Denetim firması giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('main_contractor') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('main_contractor', 'Ana Yüklenici: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('main_contractor', $main_contractor, ['class' => 'form-control', 'placeholder' =>
            'Ana yüklenicinin adını giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('isg') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('isg', 'İSG: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('isg', $isg, ['class' => 'form-control', 'placeholder' =>
            'İSG\'nin adını giriniz']) !!}

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('start_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('start_date', 'Başlangıç Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" value="{{$start_date}}" class="form-control" name="start_date" placeholder="Başlangıç tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contract_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('contract_date', 'Sözleşme Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" value="{{$contract_date}}" class="form-control" name="contract_date" placeholder="Sözleşme tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('end_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('end_date', 'İş Bitim Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" value="{{$end_date}}" class="form-control" name="end_date" placeholder="İş bitim tarihini seçiniz"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contract_worth') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('contract_worth', 'İhale Bedeli: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('contract_worth', $contract_worth,
            ['class' => 'form-control number',
            'placeholder' => 'İhale bedelini giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('extra_cost') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('extra_cost', 'İş Artış: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('extra_cost', $extra_cost,
            ['class' => 'form-control number',
            'placeholder' => 'İş artış bedelini giriniz']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('city_id') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('city_id', 'Şehir: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <select name="city_id"
                    class="city-select form-control">

                {!! $city_options !!}
            </select>

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('address', 'Adres: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::textarea('address', $address,
            ['class' => 'form-control',
            'placeholder' => 'Şantiye adresi',
            'rows' => '3']) !!}

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('site_chief') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('site_chief', 'Şantiye şefi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('site_chief', $site_chief, ['class' => 'form-control', 'placeholder' => 'Şantiye şefini giriniz']) !!}

        </div>
    </div>
</div>