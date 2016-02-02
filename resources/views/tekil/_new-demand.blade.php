
{!! Form::open([
'url' => "/tekil/$site->slug/add-materials",
'method' => 'POST',
'class' => 'form',
'id' => 'materialInsertForm',
'role' => 'form'
]) !!}

<div class="form-group">
    <label for="materials">Talep etmek istediğiniz malzemeleri seçiniz: </label>
    <select id="materials" name="materials[]"
            class="js-example-basic-multiple form-control"
            multiple="multiple">
        <?php
        $materials = \App\Material::all();
        ?>
        @foreach($materials as $mat)
            @if(isset($material_array) && in_array($mat->id,$material_array))
                <option value="{{$mat->id}}" selected>{{$mat->material}}</option>
            @else
                <option value="{{$mat->id}}">{{$mat->material}}</option>
            @endif
        @endforeach
    </select>
</div>
<button type="submit" class="btn btn-primary btn-flat">Malzemeleri Ekle</button>
{!! Form::close() !!}