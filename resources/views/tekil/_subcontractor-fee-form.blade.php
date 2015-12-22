<?php
$fee = $subcontractor->fee()->first();
?>

{!! Form::open([
'url' => "/tekil/$site->slug/update-fee",
'method' => 'POST',
'class' => 'form',
'id' => 'subcontractorOtherForm',
'role' => 'form'
])!!}
{!! Form::hidden('subcontractor_id', $subcontractor->id) !!}

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('has_meal', 'YEMEK: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-4">
            <label class="radio-inline">
            {!! Form::Radio('has_meal', 1, !empty($fee->has_meal)) !!}
            Var
            </label>
        </div>
<div class="col-sm-4">
            <label class="radio-inline">
            {!! Form::Radio('has_meal', 0,  empty($fee->has_meal)) !!}
            Yok
            </label>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('shelter', 'BARINMA YERİ ÜCRETİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('shelter', is_null($fee) ? null : str_replace(".", ",", $fee->shelter), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('sgk', 'SGK TUTARI: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('sgk', is_null($fee) ? null : str_replace(".", ",", $fee->sgk), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('allrisk', 'ALL-RİSK SİGORTA TUTARI: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('allrisk', is_null($fee) ? null : str_replace(".", ",", $fee->allrisk), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('isg', 'İSG TUTARI: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('isg', is_null($fee) ? null : str_replace(".", ",", $fee->isg), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('contract_tax', 'SÖZLEŞME/DAMGA VERGİSİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('contract_tax', is_null($fee) ? null : str_replace(".", ",", $fee->contract_tax), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('kdv', 'KDV TEVKİFAT BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('kdv', is_null($fee) ? null : str_replace(".", ",", $fee->kdv), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('material', 'GARDEN TARAFINDAN SAĞLANAN MALZEME BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('material', is_null($fee) ? null : str_replace(".", ",", $fee->material), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('equipment', 'GARDEN TARAFINDAN SAĞLANAN İŞ MAKİNASI BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('equipment', is_null($fee) ? null : str_replace(".", ",", $fee->equipment), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('oil', 'GARDEN TARAFINDAN SAĞLANAN AKARYAKIT BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('oil', is_null($fee) ? null : str_replace(".", ",", $fee->oil), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('electricity', 'GARDEN TARAFINDAN SAĞLANAN ELEKTRİK BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('electricity', is_null($fee) ? null : str_replace(".", ",", $fee->electricity), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('water', 'GARDEN TARAFINDAN SAĞLANAN SU TÜKETİM BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('water', is_null($fee) ? null : str_replace(".", ",", $fee->water), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('cleaning', 'TAŞERON ADINA YAPILAN TEMİZLİK BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('cleaning', is_null($fee) ? null : str_replace(".", ",", $fee->cleaning), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('labour', 'TAŞERON ADINA ÇALIŞTIRILAN İŞÇİLİK BEDELİ: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::text('labour', is_null($fee) ? null : str_replace(".", ",", $fee->labour), ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 col-md-offset-4">
        <div class="form-group">
            <button type="submit" class="btn btn-flat btn-primary btn-block">Ücretleri
                Kaydet
            </button>
        </div>
    </div>
</div>

{!! Form::close() !!}