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
                <th>Giriş Tarihi</th>
                <th>Çıkış Tarihi</th>
                <th>Açıklama</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody>
            @foreach(DB::table('site_stock')->select('stock_id', 'id', 'amount', 'entry_date', 'exit_date', 'site_detail')->where('site_id', '=', $site->id)->get() as $stock)
                <?php
                $detail = $stock->site_detail;
                $entry = \App\Library\CarbonHelper::getTurkishDate($stock->entry_date);
                $exit = \App\Library\CarbonHelper::getTurkishDate($stock->exit_date);
                $id = $stock->id;
                $amount = $stock->amount;
                $stock = \App\Stock::find($stock->stock_id);

                ?>
                <tr id="tr-st-{{$id}}">
                    <td>{{$stock->name}}</td>
                    <td><a href="#" class="inline-edit" data-type="text"
                           data-pk="{{$id}}"
                           data-url="{{"/tekil/$site->slug/modify-stock-amount"}}">{{$amount}}</a></td>
                    <td>{{ $stock->unit }}</td>
                    <td>
                        <a href="#" data-type="combodate" data-value="{{$entry}}" data-format="YYYY-MM-DD"
                           data-viewformat="DD.MM.YYYY" data-template="DD / MMM / YYYY" data-pk="{{$id}}"
                           data-title="Gün seçiniz" class="dob editable editable-click"
                        data-url="{{"/tekil/$site->slug/modify-stock-entry-date"}}">{{$entry}}</a>
                        </td>
                    <td>
                        <a href="#" data-type="combodate" data-value="{{$entry}}" data-format="YYYY-MM-DD"
                           data-viewformat="DD.MM.YYYY" data-template="DD / MMM / YYYY" data-pk="{{$id}}"
                           data-title="Gün seçiniz" class="dob editable editable-click"
                           data-url="{{"/tekil/$site->slug/modify-stock-exit-date"}}">{{$exit}}</a>
                        </td>
                    <td><a href="#" class="inline-edit" data-type="text"
                           data-pk="{{$id}}"
                           data-url="{{"/tekil/$site->slug/modify-stock-site-detail"}}">{{$detail}}</a></td>
                    <td>
                        <a href="#" class="btn btn-flat btn-danger btn-sm btn-approve"
                                data-id="{{$id}}">
                            Sil
                        </a>

                        <div class="row" style="display: none;">
                            <div class="col-sm-6">
                                <a href="#" class="text-danger btn-remove-sm" data-id="{{$id}}"><i class="fa fa-check"></i>Evet                             </a>

                            </div>
                            <div class="col-sm-6">
                                <a href="#" class="text-primary btn-cancel-sm"><i class="fa fa-times"></i>Hayır</a>
                            </div>
                        </div>

                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>