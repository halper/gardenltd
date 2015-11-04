@extends('tekil/layout')

@section('page-specific-css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script>
        $(".js-example-basic-multiple").select2({
            placeholder: "Eklemek istediğiniz malzemeleri seçiniz",
            allowClear: true
        });


        $('#materialDemandForm').submit(function( e){
            var emptyTexts = $('#materialDemandForm .form-control').filter(function() {
                return !this.value;
            });
            if(emptyTexts.length > 0){
                e.preventDefault();

                jQuery.each( emptyTexts, function( ) {
                    $(this).next("span").text("Lütfen ilgili alanları doldurunuz!");
                    $(this).next("span").addClass('text-danger');
                    $(this).parent().closest("div").addClass('has-error');

                });
            }
        });

    </script>

@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Malzeme Ekle</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <p>Talebini yapacağınız malzemeleri aşağıdaki kutudan seçtikten sonra birim ve miktar belirteceğiniz
                        ayrı bir tablo gelecek.</p>
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/add-materials",
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'materialInsertForm',
                    'role' => 'form'
                    ]) !!}

                    <div class="form-group">
                        <label for="materials">Talep etmek istediğiniz malzemeleri seçiniz: </label>
                        <select id="materials" name="materials[]" class="js-example-basic-multiple form-control"
                                multiple="multiple">
                            <?php
                            $materials = \App\Material::all()
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

                </div>
            </div>
        </div>
    </div>

    @if(isset($material_array))
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Malzeme Talep Formu</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            {!! Form::open([
                            'url' => "/tekil/$site->slug/demand-materials",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'materialDemandForm',
                            'role' => 'form'
                            ]) !!}
                            <table class="table table-hover table-condensed">
                                <thead>
                                <tr>
                                    <th> Malzeme</th>
                                    <th> Birim</th>
                                    <th> Adet</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($material_array as $mat)
                                    <tr>
                                        <td>
                                            {{$materials->get($mat-1)->material}}
                                            <input type="hidden" name="materials[]"
                                                   value="{{$materials->get($mat-1)->id}}">
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                {!! Form::text('unit[]', null, ['class' => 'form-control', 'placeholder'
                                                => $materials->get($mat-1)->material." malzemesinin birimini giriniz"])
                                                !!}
                                                <span></span>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                {!! Form::input('number', 'quantity[]', null, ['class' =>
                                                'form-control',
                                                'placeholder' => $materials->get($mat-1)->material." malzemesinin birim cinsinden miktarını giriniz"]) !!}
                                                <span></span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary btn-flat">Talep Et</button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endif
@stop