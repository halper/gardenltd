<div class="form-group">
    <div class="row">
        <div class="col-sm-4">
            <label for="material">Malzeme Cinsi:</label>
        </div>
        <div class="col-sm-8">
            <select class="form-control" name="material" id="material">
                <option value="" selected disabled>Malzeme Seçiniz</option>
                @foreach(\App\Material::all() as $mat)
                    <option value="{{$mat->id}}">{{$mat->material}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <label for="name">Bağlantılı Malzeme Adı:</label>
        </div>
        <div class="col-sm-12">
            {!! Form::textarea('name', null, ['class' => 'form-control', 'rows' => '3']) !!}
        </div>
    </div>
</div>

<legend>Seviye Seçiniz</legend>
<div class="form-group">
    <div class="row">
        <div class="col-xs-6 col-sm-3">
            <label class="radio-inline">
                <input class="radio" name="is_sm" type="radio" value="1">1. Seviye</label>
        </div>
        <div class="col-xs-6 col-sm-3">
            <label class="radio-inline">
                <input class="radio" name="is_sm" type="radio" value="0">2. Seviye</label>
        </div>
    </div>
</div>