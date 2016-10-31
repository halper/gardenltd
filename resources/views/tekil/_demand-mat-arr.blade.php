<div class="table-responsive">
    <table class="table table-condensed table-bordered">
        <thead>
        <tr>
            <th> Malzeme</th>
            <th> Birim</th>
            <th class="text-right"> Miktar</th>
            @if($can_add_price)
                <th class="text-right"> Birim Fiyat</th>
            @endif
            @if($can_add_payment_type)
                <th> Ödeme Şekli</th>
            @endif
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
                @if($can_add_price)
                <td>
                    <div class="form-group">
                        {!! Form::input('text', 'price[]', null, ['class' =>
                        'form-control number text-right',
                        'placeholder' => $mat->material." malzemesinin birim fiyatını giriniz"]) !!}
                        <span></span>
                    </div>
                </td>
                @endif
                @if($can_add_payment_type)
                <td>
                    <div class="form-group">
                        {!! Form::input('text', 'payment_type[]', null, ['class' =>
                        'form-control',
                        'placeholder' => $mat->material." malzemesinin ödeme şeklini giriniz"]) !!}
                        <span></span>
                    </div>
                </td>
                    @endif
            </tr>
        @endforeach
        </tbody>
    </table>

</div>