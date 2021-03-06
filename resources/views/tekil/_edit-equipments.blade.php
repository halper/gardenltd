<div class="row">
    <div class="col-md-12">
        <h1>Şantiye İş Makinelerini Düzenle</h1>

        @if($post_permission)
            <div class="row">
                <div class="col-md-12">
                    <p>Aşağıdan şantiyeye ait iş makinelerini seçebilir ya da çıkarabilirsiniz.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="select_all" class="checkbox-inline">
                        <input type="checkbox" id="select_all">Tümünü Seç
                    </label>
                </div>
            </div>
            <div class="row">
                {!! Form::open([

                            'url' => "/tekil/$site->slug/edit-equipments",
                            'method' => 'POST',
                            'class' => 'form',
                            'id' => 'editEquipmentForm',
                            'role' => 'form']) !!}


                @foreach(App\Equipment::all() as $eq)

                    <div class="col-md-3 col-xs-4">
                        <label class="checkbox-inline">
                            {!! Form::checkbox('equipments[]', $eq->id, $site->hasEquipment($eq->id),
                            [
                            'id'=>$eq->id,
                            ])
                            !!}{{ $eq->name}}</label>
                    </div>

                @endforeach
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-4 col-md-offset-3">
                    <div class="form-group">
                        <br>
                        <br>
                        <button type="submit" class="btn btn-primary btn-flat btn-block">
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
        @else
            <p>Bu sayfayı düzenlemek için yetkiniz bulunmamaktadır!</p>
        @endif
    </div>
</div>