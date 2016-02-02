<h3>Demirbaş Görüntüle</h3>

<p>Bu sekmeden şantiyeye ait tüm demirbaşları listeleyebilir, düzenleyebilir ya da silebilirsiniz.</p>

<div class="row">
    <div class="col-sm-12">
        <table class="table table-responsive">
            <thead>
            <tr>
                <th>Demirbaş</th>
                <th>Miktar</th>
                <th>Birim</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody>
            @foreach($site->stock()->get() as $stock)
                <tr>
                    <td>{{$stock->name}}</td>
                    <td><a href="#" class="inline-edit" data-type="text"
                           data-pk="{{$stock->id}}"
                           data-url="{{"/tekil/$site->slug/modify-stock-amount"}}">{{$stock->pivot->amount}}</a></td>
                    <td>{{ $stock->unit }}</td>
                    <td>
                        <button type="button"
                                class="btn btn-flat btn-danger btn-sm subDelBut"
                                data-id="{{$stock->id}}"
                                data-name="{{$stock->name}}"
                                data-toggle="modal" data-target="#deleteSubcontractorConfirm">
                            Sil
                        </button>

                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>