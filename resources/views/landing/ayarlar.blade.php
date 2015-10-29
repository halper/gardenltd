@extends('landing/landing')

@section('page-specific-js')
    <script>
        $(document).on("click", ".userDelBut", function (e) {

            e.preventDefault();
            var myUserId = $(this).data('id');
            var myUserName = $(this).data('name');
            var myForm = $('.modal-footer #userDeleteForm');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + myUserName + "</em> adlı kullanıcıyı silmek istediğinize emin misiniz?");
            $('<input>').attr({
                type: 'hidden',
                name: 'userDeleteIn',
                value: myUserId
            }).appendTo(myForm);
            $('#deleteUserConfirm').modal('show');
        });

        if ($('.has-error')[0]) {
            $('#insertUser').modal('show');
        }

    </script>
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Adı-Soyadı</th>
                    <th>E-posta Adresi</th>
                    <th>Kayıt Tarihi</th>
                    <th>Kullanıcı İşlemleri</th>
                </tr>
                </thead>
                <tbody>

                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }} {{$user->isAdmin() ? "(Admin)" : ""}}</td>
                        <td>{{ $user->email }}</td>

                        <td>
                            <?php
                            $date = strtotime($user->getAttribute("created_at"));
                            $date_format = date('d.m.Y', $date);
                            echo $date_format;
                            ?>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-sm-3">
                                    <a href="{{"duzenle/$user->id"}}" class="btn btn-warning btn-sm">Düzenle</a>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    echo '<button type="button" class="btn btn-danger btn-sm userDelBut" data-id="' . $user->id . '" data-name="' . $user->name . '" data-toggle="modal" data-target="#deleteUserConfirm">Sil</button>';
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
        <button type="button" class="btn btn-primary pull-right" style="margin: 15px" data-toggle="modal"
                data-target="#insertUser">Yeni Kullanıcı Ekle
        </button>
    </div>

    <div id="deleteUserConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => '/admin/del-user',
                    'method' => 'PATCH',
                    'class' => 'form',
                    'id' => 'userDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-warning">Sil</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

    <div id="insertUser" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Yeni Kullanıcı Ekle</h4>
                </div>
                <div class="modal-body">
                    {!! Form::model($users, [
                    'url' => '/admin/add-user',
                    'method' => 'POST',
                    'class' => 'form .form-horizontal',
                    'id' => 'userInsertForm',
                    'role' => 'form'
                    ]) !!}
                    @include('landing._user-form')


                </div>
                <div class="modal-footer">

                    <button type="submit" class="btn btn-primary">Kullanıcı ekle</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>

@stop

