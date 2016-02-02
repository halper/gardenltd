<div class="row">
    <div class="col-sm-12">
        <table class="table table-responsive">
            <thead>
            <tr>
                <th>Şantiye</th>
                <th>Tarih</th>
                <th>Talep No</th>
                <th>Malzeme - Miktar</th>
                <th>Bağlantı Yapılan Firma</th>
                <th>Açıklama</th>
                <th>Temin Tarihi</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $demands = \App\Demand::where('approval_status', '>', 0)->get();

            ?>
            @foreach($demands as $demand)
                <tr class="valign {{$demand->approval_status == 3 ? "bg-success" : "bg-danger"}}">
                    <td>{{$demand->site->job_name}}</td>
                    <td>{{ \App\Library\CarbonHelper::getTurkishDate($demand->created_at) }}</td>
                    <td>{{ $demand->id }}</td>
                    <td>
                        <?php
                        $i = 0;
                        ?>
                        @foreach($demand->materials()->get() as $mat)
                            {!! $mat->material . ' - ' . str_replace('.', ',', $mat->pivot->quantity) ." ". $mat->pivot->unit . (++$i != sizeof($demand->materials()->get())? '<br>':'')!!}

                        @endforeach
                    </td>

                    <td>{{$demand->firm}}</td>
                    <td>{{$demand->details}}</td>
                    <td>
                        {{\App\Library\CarbonHelper::getTurkishDate($demand->demand_date)}}
                    </td>

                    <td>


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


                    </td>
                    <td>
                        @if($demand->approval_status < 3)

                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <a class="btn btn-flat btn-success btn-sm"
                                           href="{{url("/admin/talep-onay/$demand->id")}}">
                                            Onayla
                                        </a>
                                    </div>
                                    <div class="col-sm-6">
                                        <button type="button"
                                                class="btn btn-flat btn-danger btn-sm demandRejectBut"
                                                data-id="{{$demand->id}}"
                                                data-name="{{\App\Library\CarbonHelper::getTurkishDate($demand->created_at)}}"
                                                data-toggle="modal" data-target="#rejectDemandConfirm">
                                            Reddet
                                        </button>

                                    </div>
                                </div>
                            </div>
                        @endif
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>