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
            {!! Form::label('pay_date', 'Tarih İtibariyle: ', ['class' => 'control-label']) !!}
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

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('breakfast', 'YEMEK ÜCRETLERİ: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-2">
            {!! Form::text('breakfast', null, ['class' => 'form-control number', 'placeholder' => 'Kahvaltı Ücreti']) !!}
        </div>
        <div class="col-sm-2 col-sm-offset-1">
            {!! Form::text('lunch', null, ['class' => 'form-control number', 'placeholder' => 'Öğle Yemeği Ücreti']) !!}
        </div>
        <div class="col-sm-2 col-sm-offset-1">
            {!! Form::text('supper', null, ['class' => 'form-control number', 'placeholder' => 'Akşam Yemeği Ücreti']) !!}
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