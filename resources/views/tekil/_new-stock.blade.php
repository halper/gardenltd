<h3>Demirbaş Ekle</h3>

<p>Demirbaşları seçtikten sonra miktarlarını belirteceğiniz bir tablo ayrıca gelecektir.</p>
{!! Form::open([
'url' => "/tekil/$site->slug/preregister-stocks",
'method' => 'POST',
'class' => 'form',
'id' => 'stocksInsertForm',
'role' => 'form'
]) !!}
<div class="form-group">
    <label for="stocks">Demirbaş: </label>
    <select id="stocks" name="stocks[]"
            class="js-example-data-array form-control" multiple>
    </select>
</div>
<div class="form-group">
    <button type="submit" class="btn btn-primary btn-flat">Demirbaşları Ekle</button>
</div>
{!! Form::close() !!}
