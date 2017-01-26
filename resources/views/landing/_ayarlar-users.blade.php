<div class="row">
    <div class="col-xs-12 table-responsive">
        <table class="table table-striped table-responsive">
            <thead>
            <tr>
                <th>Adı-Soyadı</th>
                <th>E-posta Adresi</th>
                <th>Kayıt Tarihi</th>
                <th>Kullanıcı İşlemleri</th>
            </tr>
            </thead>
            <tbody>

            @foreach(\App\User::all() as $user)
                <tr>
                    <td>{{ $user->name }} {{$user->isAdmin() ? "(Admin)" : ""}}</td>
                    <td>{{ $user->email }}</td>

                    <td>
                        {{\App\Library\CarbonHelper::getTurkishDate($user->created_at)}}
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-sm-3">
                                <a href="{{"duzenle/$user->id"}}" class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                            </div>
                            <div class="col-sm-2">
                                <?php
                                echo '<button type="button" class="btn btn-flat btn-danger btn-sm userDelBut" data-id="' . $user->id . '" data-name="' . $user->name . '" data-toggle="modal" data-target="#deleteUserConfirm">Sil</button>';
                                ?>
                            </div>
                        </div>

                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <button type="button" class="btn btn-flat btn-primary pull-right" style="margin: 15px" data-toggle="modal"
            data-target="#insertUser">Yeni Kullanıcı Ekle
    </button>
</div>