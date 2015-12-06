{!! Form::model($subcontractor->fee()->first(), [
'url' => "/tekil/$site->slug/update-fee",
'method' => 'POST',
'class' => 'form .form-horizontal',
'id' => 'subcontractorOtherForm',
'role' => 'form'
])!!}
{!! Form::hidden('subcontractor_id', $subcontractor->id) !!}

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('breakfast', 'YEMEK ÜCRETLERİ: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-2">
            {!! Form::number('breakfast', null, ['class' => 'form-control', 'placeholder' => 'Kahvaltı Ücreti', 'step' => 'any']) !!}
        </div>
        <div class="col-sm-2 col-sm-offset-1">
            {!! Form::number('lunch', null, ['class' => 'form-control', 'placeholder' => 'Öğle Yemeği Ücreti', 'step' => 'any']) !!}
        </div>
        <div class="col-sm-2 col-sm-offset-1">
            {!! Form::number('supper', null, ['class' => 'form-control', 'placeholder' => 'Akşam Yemeği Ücreti', 'step' => 'any']) !!}
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
                    {!! Form::number('shelter', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('sgk', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('allrisk', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('isg', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('contract_tax', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('kdv', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('material', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('equipment', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('oil', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('electricity', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('water', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('cleaning', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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
                    {!! Form::number('labour', null, ['class' => 'form-control', 'placeholder' => 'Tutar(TL)', 'step' => 'any']) !!}
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