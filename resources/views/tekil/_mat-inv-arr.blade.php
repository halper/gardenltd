@if(isset($material_array["submat"]))
    <div class="table-responsive">
        <table class="table table-condensed table-bordered">
            <thead>
            <tr>
                <th> Malzeme</th>
                <th> Birim</th>
                <th class="text-right"> Birim Fiyat</th>
                <th class="text-right"> Miktar</th>
            </tr>
            </thead>
            <tbody>
            @foreach($material_array["submat"] as $mat_arr)
                <?php
                $mat = \App\Submaterial::find($mat_arr);
                ?>
                <tr>
                    <td>
                        {{$mat->name}}
                        <input type="hidden" name="submaterials[]"
                               value="{{$mat->id}}">
                    </td>
                    <td>
                        <div class="form-group">
                            {!! Form::text('unit[]', null, ['class' => 'form-control', 'placeholder'
                            => $mat->name." malzemesinin birimini giriniz"])
                            !!}
                            <span></span>

                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            {!! Form::text('price[]', null, ['class' => 'form-control text-right number', 'placeholder'
                            => $mat->name." malzemesinin birim fiyatını giriniz"])
                            !!}
                            <span></span>

                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            {!! Form::text('quantity[]', null, ['class' =>
                            'form-control text-right number',
                            'placeholder' => $mat->name." malzemesinin birim cinsinden miktarını giriniz"]) !!}
                            <span></span>
                        </div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
@endif

@if(isset($material_array["featured"]))
    <div class="table-responsive">
        <table class="table table-condensed table-bordered">
            <thead>
            <tr>
                <th> Malzeme</th>
                <th> Birim</th>
                <th class="text-right"> Birim Fiyat</th>
                <th class="text-right"> Miktar</th>
                <th> Özellik</th>
            </tr>
            </thead>
            <tbody>
            @foreach($material_array["featured"] as $mat_arr)
                <?php
                $mat = \App\Submaterial::find($mat_arr);
                ?>
                <tr>
                    <td>
                        {{$mat->name}}
                        <input type="hidden" name="smfeatured[]"
                               value="{{$mat->id}}">
                    </td>
                    <td>
                        <div class="form-group">
                            {!! Form::text('smfeatured-unit[]', null, ['class' => 'form-control', 'placeholder'
                            => $mat->name." malzemesinin birimini giriniz"])
                            !!}
                            <span></span>

                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            {!! Form::text('smfeatured-price[]', null, ['class' => 'form-control text-right number', 'placeholder'
                            => $mat->name." malzemesinin birim fiyatını giriniz"])
                            !!}
                            <span></span>

                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            {!! Form::text('smfeatured-quantity[]', null, ['class' =>
                            'form-control text-right number',
                            'placeholder' => $mat->name." malzemesinin birim cinsinden miktarını giriniz"]) !!}
                            <span></span>
                        </div>
                    </td>
                    <td>
                        @if($mat->material->feature->isEmpty())
                        <p>İkinci seviye bağlantı yapacak özellik bulunmamaktadır.</p>
                        @else
                        @foreach($mat->material->feature()->get() as $feature)
                            <label class="checkbox-inline">
                                <input type="checkbox" name="sm-cb-{{$mat->id}}[]"
                                       value="{{$feature->id}}">
                                {{$feature->name}}
                            </label>
                        @endforeach
                            @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif