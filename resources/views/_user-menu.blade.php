<div class="navbar-custom-menu pull-right">
    <ul class="nav navbar-nav">
        <!-- User Account Menu -->
        <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <i class="fa {{ Auth::User()->isAdmin() ? "fa-user-plus" : "fa-user"}} "></i>
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs"><?=Auth::user()->getAttribute("name")?></span>
            </a>


            <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header">
                    <i class="fa {{ Auth::User()->isAdmin() ? "fa-user-plus" : "fa-user"}} fa-4x"></i>

                    <p>
                        <?=Auth::user()->getAttribute("name")?>

                        <small>{{ Auth::User()->isAdmin() ?
                                    "Admin" : "Kullanıcı" }}
                        </small>
                        {{-- TODO
                        Buraya kullanıcı grupları gelecek--}}
                    </p>
                </li>
                @if(Auth::User()->isAdmin())

                    <li class="user-body">
                        <div class="row">
                            <div class="col-xs-4 text-center">
                                <a href="<?=URL::to("/");?>/admin/ayarlar">Ayarlar</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="<?=URL::to("/");?>/admin/ekle">Ekle</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="<?=URL::to("/");?>/admin/guncelle">Düzenle</a>
                            </div>
                        </div>
                        <!-- /.row -->
                    </li>
                    @endif
                            <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="#" class="btn btn-default btn-flat">Bilgilerim</a>
                        </div>

                        <div class="pull-right">
                            <a href="/auth/logout" class="btn btn-default btn-flat">Çıkış</a>
                        </div>
                    </li>
            </ul>
        </li>
    </ul>
</div>