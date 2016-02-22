<?php
use App\Library\TurkishChar;

$manufacturing_options = '';

foreach (\App\Manufacturing::all() as $manufacture) {
    if(isset($subcontractor) && $subcontractor->hasManufacture($manufacture->id)){
    $manufacturing_options .= "'<option value=\"$manufacture->id\" selected>" . TurkishChar::tr_up($manufacture->name) . "</option>'+\n";
    }
    else
    $manufacturing_options .= "'<option value=\"$manufacture->id\">" . TurkishChar::tr_up($manufacture->name) . "</option>'+\n";
}

?>

<div class="form-group" {{ $errors->has('price') ? 'has-error' : '' }}>
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('price', 'Sözleşme Bedeli: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('price', str_replace(".", ",", $subcontractor->price), ['class' => 'form-control number', 'placeholder' => 'Sözleşme Bedelini Giriniz(TL)', 'step' => 'any']) !!}
        </div>
    </div>

</div>
<div class="form-group" {{ $errors->has('additional_bid_cost') ? 'has-error' : '' }}>
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('additional_bid_cost', 'İlave Sözleşme Bedeli: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::text('additional_bid_cost', str_replace(".", ",", $subcontractor->additional_bid_cost), ['class' => 'form-control number', 'placeholder' => 'İlave Sözleşme Bedelini Giriniz(TL)', 'step' => 'any']) !!}
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
                <input type="text" class="form-control" name="contract_date"
                       placeholder="Sözleşme tarihini seçiniz" {!! isset($subcontractor) && (!($subcontractor->contract()->get()->isEmpty()) && strpos($subcontractor->contract->contract_date,"0000-00-00") === false) ? "value=\"" . \App\Library\CarbonHelper::getTurkishDate($subcontractor->contract->contract_date) . "\"" : ""!!}/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>

        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contract_start_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('contract_start_date', 'Sözleşme Başlangıç Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" class="form-control" name="contract_start_date"
                       placeholder="Sözleşme başlangıç tarihini seçiniz" {!! isset($subcontractor) && (!empty($subcontractor->contract) && strpos($subcontractor->contract->contract_start_date,"0000-00-00") === false) ? "value=\"" . \App\Library\CarbonHelper::getTurkishDate($subcontractor->contract()->first()->contract_start_date) . "\"" : ""!!}/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group {{ $errors->has('contract_end_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('contract_end_date', 'Sözleşme Bitim Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" class="form-control" name="contract_end_date"
                       placeholder="Sözleşme bitim tarihini seçiniz" {!! isset($subcontractor) && (!empty($subcontractor->contract) && strpos($subcontractor->contract->contract_end_date,"0000-00-00") === false) ? "value=\"" . \App\Library\CarbonHelper::getTurkishDate($subcontractor->contract->contract_end_date) . "\"" : ""!!}/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('exit_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('exit_date', 'Çıkış Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                <input type="text" class="form-control" name="exit_date"
                       placeholder="Çıkış tarihi seçiniz" {!! isset($subcontractor) && (!empty($subcontractor->contract) && strpos($subcontractor->contract->exit_date,"0000-00-00") === false) ? "value=\"" . \App\Library\CarbonHelper::getTurkishDate($subcontractor->contract->exit_date) . "\"" : ""!!}/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('manufacturings', 'Faaliyet Alanı: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <select name="manufacturings[]"
                    class="js-example-basic-multiple form-control"
                    multiple>

                {!! $manufacturing_options !!}
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('contract', 'Sözleşme Dosyası: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <input type="file" name="contractToUpload" id="contractToUpload">
        </div>
    </div>
</div>