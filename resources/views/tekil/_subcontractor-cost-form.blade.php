{!! Form::open([
'url' => "/tekil/$site->slug/update-cost",
'method' => 'POST',
'class' => 'form',
'id' => 'subcontractorCostForm',
'role' => 'form'
])!!}
{!! Form::hidden('subcontractor_id', $subcontractor->id) !!}

<div class="form-group {{ $errors->has('pay_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('pay_date', 'Ödeme Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                {!! Form::text('pay_date', null, ['class' => 'form-control', 'placeholder' => 'Ödeme tarihini seçiniz']) !!}
                <span class="input-group-addon add-on"><span
                            class="glyphicon glyphicon-calendar"></span></span>
            </div>

        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('explanation') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('explanation', 'Açıklama: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::textarea('explanation', null, ['class' => 'form-control', 'placeholder' => 'Ödeme açıklaması yazınız', 'rows' => '3']) !!}

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
                    {!! Form::text('material', null, ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
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
                    {!! Form::text('equipment', null, ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
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
                    {!! Form::text('oil', null, ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
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
                    {!! Form::text('cleaning', null, ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
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
                    {!! Form::text('labour', null, ['class' => 'form-control number', 'placeholder' => 'Tutar(TL)']) !!}
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