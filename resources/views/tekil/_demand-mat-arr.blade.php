<div class="table-responsive">
    <table class="table table-condensed table-bordered">
        <thead>
        <tr>
            <th> Malzeme</th>
            <th> Birim</th>
            <th class="text-right"> Miktar</th>
            <th class="text-right"> Birim Fiyat</th>
            <th > Ödeme Şekli</th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Material::find($material_array) as $mat)
            <tr>
                <td>
                    {{$mat->material}}
                    <input type="hidden" name="materials[]"
                           value="{{$mat->id}}">
                </td>
                <td>
                    <div class="form-group">
                        {!! Form::text('unit[]', null, ['class' => 'form-control', 'placeholder'
                        => $mat->material." malzemesinin birimini giriniz"])
                        !!}
                        <span></span>

                    </div>
                </td>
                <td>
                    <div class="form-group">
                        {!! Form::input('text', 'quantity[]', null, ['class' =>
                        'form-control number text-right',
                        'placeholder' => $mat->material." malzemesinin birim cinsinden miktarını giriniz"]) !!}
                        <span></span>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        {!! Form::input('text', 'price[]', null, ['class' =>
                        'form-control number text-right',
                        'placeholder' => $mat->material." malzemesinin birim fiyatını giriniz"]) !!}
                        <span></span>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        {!! Form::input('text', 'payment_type[]', null, ['class' =>
                        'form-control',
                        'placeholder' => $mat->material." malzemesinin ödeme şeklini giriniz"]) !!}
                        <span></span>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>