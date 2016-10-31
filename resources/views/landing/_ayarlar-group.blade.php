<p>İlgili grubu silmek için, grup adına tıkladıktan sonra grup adını silip onaylamanız gerekmektedir.</p>
<div class="row">
    <div class="col-xs-12 table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Grup Adı</th>
                <th>Kullanıcılar</th>
                <th>Düzenle</th>
            </tr>
            </thead>
            <tbody>

            @foreach(\App\Group::all() as $group)
                <tr>
                    <td>
                        <a href="#" class="inline-edit" data-type="text"
                           data-pk="{{$group->id}}"
                           data-url="/admin/modify-group"
                           data-title="Grup Adı">{{$group->name}}</a>
                        </td>
                    <td>
                    @foreach($group->user()->get() as $guser)
                        {{$guser->name . ", "}}
                        @endforeach
                    </td>

                    <td>
                        <a href="{{"grup-duzenle/$group->id"}}" class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <button type="button" class="btn btn-flat btn-primary pull-right" style="margin: 15px" data-toggle="modal"
            data-target="#insertGroup">Yeni Grup Ekle
    </button>
</div>