<p>Birden çok malzemeyi tek seferde eklemek için malzemeleri noktalı virgülle ayırın.</p>

<div class="row">
    <div class="col-sm-12">
        {!! Form::open([
        'url' => "/admin/add-submaterial",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'submaterialInsertForm',
                            'role' => 'form'
                            ]) !!}
        @include('landing._submaterial-insert')
        <div class="form-group">
            <div class="row">
                <div class="col-xs-12 col-md-4 col-md-offset-4">
                    <button type="submit" class="btn btn-block btn-flat btn-primary">Ekle</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <h3>Mevcut Malzemeler</h3>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Malzeme</th>
            <th>1. Seviye Bağlantı</th>
            <th>2. Seviye Bağlantı</th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Material::all() as $mat)
            <tr>
                <td>
                    <strong>{{\App\Library\TurkishChar::tr_up($mat->material)}}</strong>
                </td>
                <td>
                    @for($i = 0; $i< sizeof($mat->submaterial()->get()); $i++)
                        {{$mat->submaterial()->get()[$i]->name . ($i+1 != sizeof($mat->submaterial()->get()) ? ", " : "")}}
                    @endfor
                </td>
                <td>
                    @for($i = 0; $i< sizeof($mat->feature()->get()); $i++)
                        {{$mat->feature()->get()[$i]->name . ($i+1 != sizeof($mat->feature()->get()) ? ", " : "")}}
                    @endfor
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>
</div>