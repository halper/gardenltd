{!! Form::open([
'url' => "/tekil/$site->slug/demand-submaterials",
'method' => 'POST',
'class' => 'form',
'id' => 'materialInsertForm',
'role' => 'form'
]) !!}
@if(isset($update))
    <input type="hidden" name="update" value="1">
@endif
<div class="form-group">
    <label for="submaterials">1. Seviye Bağlantı Malzeme: </label>
    <select id="submaterials" name="submaterials[]"
            class="js-example-data-array form-control" multiple="multiple">

    </select>
</div>
<div class="form-group">
    <label for="smfeatures">2. Seviye Bağlantı Yapılacak 1. Seviye Malzeme: </label>
    <select id="smfeatures" name="smfeatures[]"
            class="js-example-data-array-2 form-control" multiple="multiple">

    </select>
</div>
<div class="form-group">
    <button type="submit" class="btn btn-primary btn-flat">Malzemeleri Ekle</button>
</div>
{!! Form::close() !!}
