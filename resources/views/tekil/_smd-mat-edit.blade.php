@include('tekil._demand-submaterials', ['update' => 'true'])

{!! Form::open([
                        'url' => "/tekil/$site->slug/update-smdemand",
                        'method' => 'POST',
                        'class' => 'form',
                        'id' => 'submaterialDemandForm',
                        'role' => 'form'
                        ]) !!}
<input type="hidden" name="smdid" value="{{$smdemand->id}}">
<div class="table-responsive">
    <table class="table table-condensed table-bordered">
        <thead>
        <tr>
            <th> Malzeme</th>
            <th> Birim</th>
            <th class="text-right"> Miktar</th>
            <th class="text-center"> Malzeme Çıkar</th>
        </tr>
        </thead>
        <tbody>
        @foreach($smdemand->submaterial()->get() as $mat)
            <tr id="tr-sm-{{$mat->id}}">
                <td>
                    {{$mat->name}}
                    <input type="hidden" name="submaterials[]"
                           value="{{$mat->id}}">
                </td>
                <td>
                    <div class="form-group">
                        {!! Form::text('unit[]', $mat->pivot->unit, ['class' => 'form-control', 'placeholder'
                        => $mat->name." malzemesinin birimini giriniz"])
                        !!}
                        <span></span>

                    </div>
                </td>

                <td>
                    <div class="form-group">
                        {!! Form::text('quantity[]', \App\Library\TurkishChar::convertToTRcurrency($mat->pivot->quantity), ['class' =>
                        'form-control number text-right',
                        'placeholder' => $mat->name." malzemesinin birim cinsinden miktarını giriniz"]) !!}
                        <span></span>
                    </div>
                </td>
                <td class="text-center">
                    <a href="#" class="btn btn-flat btn-danger btn-approve">Malzemeyi Çıkar</a>

                    <div class="row hidden">
                        <div class="col-sm-6">
                            <a href="#" class="text-danger btn-remove-sm" data-id="{{$mat->id}}"><i
                                        class="fa fa-check"></i>Evet                             </a>

                        </div>
                        <div class="col-sm-6">
                            <a href="#" class="text-primary btn-cancel-sm"><i
                                        class="fa fa-times"></i>Hayır</a>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>

@include('tekil._mat-inv-arr')

<div class="form-group">
    <div class="row">
        <div class="col-sm-2">
            <label for="firm">Sözleşme Tutarı: </label>
        </div>
        <div class="col-sm-10">
            {!! Form::text('contract_cost', \App\Library\TurkishChar::convertToTRcurrency($smdemand->contract_cost), ['class' => 'form-control number', 'placeholder'
            => "Sözleşme tutarını giriniz"])
            !!}
            <span></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
    </div>
</div>

{!! Form::close() !!}