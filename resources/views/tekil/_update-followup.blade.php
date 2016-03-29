<h3>Talep Güncelle</h3>
@if($site->smdemand->count() > 0)

    <p>Bu sekmeden bağlantılı malzemelerle ilgili tüm talepleri güncelleyebilir ya da silebilirsiniz.</p>

    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Malzeme</th>
                    <th>Sözleşme Tutarı</th>
                    <th>Bağlantılı Malzemeler</th>
                    <th class="text-center">İşlemler</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $demands = $site->smdemand()->with('submaterial')->get();

                ?>
                @foreach($demands as $demand)
                    <tr class="valign">
                        <td>{{ $demand->material->material }}</td>
                        <td><span class="inumber">{{ \App\Library\TurkishChar::convertToTRcurrency($demand->contract_cost) }}</span>TL</td>
                        <td>
                            <?php
                            $i = 0;
                            ?>
                            @foreach($demand->submaterial()->get() as $mat)
                                {{$mat->name . (++$i == sizeof($demand->submaterial()->get()) ? "" : ", ")}}

                            @endforeach
                        </td>
                        <td  class="text-center">
                            <div class="row">
                                <div class="col-sm-6">
                                    <a class="btn btn-flat btn-warning btn-sm"
                                       href="{{url("/tekil/$site->slug/baglanti-malzeme-duzenle/$demand->id")}}">
                                        Düzenle
                                    </a>
                                </div>

                                <div class="col-sm-6">
                                    <button type="button"
                                            class="btn btn-flat btn-danger btn-sm subDelBut"
                                            data-id="{{$demand->id}}"
                                            data-name="{{$demand->material->material}}"
                                            data-toggle="modal" data-target="#deleteSubcontractorConfirm">
                                        Sil
                                    </button>
                                </div>

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p>Güncellenecek talep bulunmamaktadır.</p>
@endif