{!! Form::open([
'url' => "/tekil/$site->slug/update-payment",
'method' => 'POST',
'class' => 'form',
'id' => 'subcontractorCostForm',
'role' => 'form'
])!!}
{!! Form::hidden('subid', $subcontractor->id) !!}

<div class="form-group {{ $errors->has('payment_date') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('payment_date', 'Ödeme Tarihi: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            <div class="input-group input-append date dateRangePicker">
                {!! Form::text('payment_date', null, ['class' => 'form-control', 'placeholder' => 'Ödeme tarihini seçiniz']) !!}
                <span class="input-group-addon add-on"><span
                            class="glyphicon glyphicon-calendar"></span></span>
            </div>

        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-2">
                    <label for="name" class="control-label">
                        Yapılan Ödeme:
                    </label>
                </div>

            <div class="col-sm-10">
                <select name="name" id="payment-name" class="form-control">
                    <option value="" selected disabled>Ödeme Seçiniz</option>
                    <option value="Malzeme">GARDEN TARAFINDAN SAĞLANAN MALZEME BEDELİ</option>
                    <option value="İş Makinası">GARDEN TARAFINDAN SAĞLANAN İŞ MAKİNASI BEDELİ</option>
                    <option value="Akaryakıt">GARDEN TARAFINDAN SAĞLANAN AKARYAKIT BEDELİ</option>
                    <option value="Temizlik">GARDEN TARAFINDAN SAĞLANAN TEMİZLİK BEDELİ</option>
                    <option value="İşçilik">ALT YÜKLENİCİ ADINA ÇALIŞTIRILAN İŞÇİLİK BEDELİ</option>
                    <option value="Ek Ödeme">EK ÖDEME</option>
                </select>
            </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group {{ $errors->has('detail') ? 'has-error' : '' }}">
    <div class="row">
        <div class="col-sm-2">
            {!! Form::label('detail', 'Açıklama: ', ['class' => 'control-label']) !!}
        </div>
        <div class="col-sm-10">
            {!! Form::textarea('detail', null, ['class' => 'form-control', 'placeholder' => 'Ödeme açıklaması yazınız', 'rows' => '3']) !!}

        </div>

    </div>
</div>


<div class="form-group">
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-4">
                    <label for="amount" class="control-label">
                        Miktar:
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="form-control number" name="amount" placeholder="Bedel">
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-4">
                    <label for="method" class="control-label">
                        Ödeme Tipi:
                    </label>
                </div>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="method" placeholder="Ödeme Tipi">
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