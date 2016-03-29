<h3>Talep Görüntüle</h3>

<p>Bu sekmeden şantiyeden yapılmış tüm talepleri görüntüleyebilir ya da temin edilmemiş talepleri silebilirsiniz.</p>

<div class="row">
    <div class="col-sm-12">
        <table class="table table-responsive">
            <thead>
            <tr>
                <th>Tarih</th>
                <th>Talep No</th>
                <th>Malzeme - Miktar</th>
                <th>Bağlantı Yapılan Firma</th>
                <th>Açıklama</th>
                <th>Temin Tarihi</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $demands = $site->demand()->with('materials')->get();

            ?>
            @foreach($demands as $demand)
                <tr class="valign {{$demand->hasDelivered() ? "bg-success" : "bg-danger"}}">
                    <td>{{ \App\Library\CarbonHelper::getTurkishDate($demand->created_at) }}</td>
                    <td>{{ $demand->id }}</td>
                    <td>
                        <?php
                        $i = 0;
                        ?>
                        @foreach($demand->materials()->get() as $mat)
                            {!! $mat->material . ' - <span class="inumber">' . $mat->pivot->quantity . '</span> '. $mat->pivot->unit . (++$i != sizeof($demand->materials()->get())? '<br>':'')!!}

                        @endforeach
                    </td>

                    <td>{{$demand->firm}}</td>
                    <td>{{$demand->details}}</td>
                    <td>
                        {{\App\Library\CarbonHelper::getTurkishDate($demand->demand_date)}}
                    </td>

                    <td>
                        @if($demand->hasDelivered())
                            Temin edildi
                        @elseif($demand->approval_status == 0)
                            <div class="row">

                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <a class="btn btn-flat btn-warning btn-sm"
                                               href="{{url("/tekil/$site->slug/talep-duzenle/$demand->id")}}">
                                                Düzenle
                                            </a>
                                        </div>
                                        <div class="col-sm-4">
                                            <a class="btn btn-flat btn-primary btn-sm"
                                               href="{{url("/tekil/$site->slug/talep-sevket/$demand->id")}}">
                                                Sevket
                                            </a>
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button"
                                                    class="btn btn-flat btn-danger btn-sm subDelBut"
                                                    data-id="{{$demand->id}}"
                                                    data-name="{{\App\Library\CarbonHelper::getTurkishDate($demand->demand_date)}}"
                                                    data-toggle="modal" data-target="#deleteSubcontractorConfirm">
                                                Sil
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($demand->approval_status > 0)
                            <?php
                            $demand_status = '';
                            switch ($demand->approval_status) {
                                case 1:
                                    $demand_status = 'PM onayında';
                                    break;
                                case 2:
                                    $demand_status = 'Merkez onayında';
                                    break;
                                case 3:
                                    $demand_status = 'Onaylandı';
                                    break;
                                case 4:
                                    $demand_status = 'Reddedildi: ' . $demand->rejection->reason;
                                    break;
                            }

                            ?>
                            {{$demand_status}}
                        @endif


                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>