<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Garden İnşaat - Şantiyeler Ana Sayfası</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @include('base.css')

            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

    <header class="main-header">
        <nav class="navbar navbar-static-top">
            <div class="container">

                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu pull-right">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <i class="fa fa-user-plus"></i>
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"><?=Auth::user()->getAttribute("name")?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">
                                    <i class="fa fa-user-plus fa-4x"></i>

                                    <p>
                                        <?=Auth::user()->getAttribute("name")?>
{{--                                        <small>{{Auth::user()->permission}}</small>--}}
                                        {{-- TODO
                                        Buraya kullanıcı grupları gelecek--}}
                                    </p>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="#" class="btn btn-default btn-flat">Bilgilerim</a>
                                    </div>
                                    <div class="col-md-4">
                                        <a href="ayarlar" class="btn btn-default btn-flat">Ayarlar</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="auth/logout" class="btn btn-default btn-flat">Çıkış</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-custom-menu -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </header>
    <!-- Full Width Column -->
    <div class="content-wrapper">
        <div class="container">


            <!-- Main content -->
            <section class="content">
                @if(Session::has('flash_message'))

                    <div class="alert alert-success fade in alert-box {{ Session::has('flash_message_important') ? 'alert-important' : '' }}">
                        @if(Session::has('flash_message_important'))
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                        @endif
                        {!! Session::get('flash_message') !!}
                    </div>
                @endif

                @yield('content')

            </section>
            <!-- /.content -->

        </div>
        <!-- /.container -->
    </div>
    <!-- /.content-wrapper -->

</div>
<!-- ./wrapper -->

<div id="addNewSite" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="şantiye eklemek için açılır form"
     aria-hidden="true">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Yeni Şantiye Ekle</h4>
            </div>
            <div class="modal-body">
                {!! Form::open([
                'url' => '/santiye/add',
                'method' => 'POST',
                'class' => 'form .form-horizontal',
                'id' => 'siteInsertForm',
                'role' => 'form'
                ])!!}
                <div class="form-group {{ $errors->has('job_name') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('job_name', 'İşin Adı: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::text('job_name', null, ['class' => 'form-control', 'placeholder' => 'İşin adını giriniz']) !!}

                        </div>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('management_name') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('management_name', 'İdarenin Adı: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::text('management_name', null, ['class' => 'form-control', 'placeholder' => 'İdarenin adını giriniz']) !!}

                        </div>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('start_date') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('start_date', 'Başlangıç Tarihi: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::text('start_date', null, ['class' => 'form-control', 'placeholder' => 'Tarihi 01.01.2000 şeklinde giriniz']) !!}

                        </div>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('contract_date') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('contract_date', 'Sözleşme Tarihi: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::text('contract_date', null, ['class' => 'form-control', 'placeholder' => 'Tarihi 01.01.2000 şeklinde giriniz']) !!}

                        </div>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('end_date') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('end_date', 'İş Bitim Tarihi: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::text('end_date', null, ['class' => 'form-control', 'placeholder' => 'Tarihi 01.01.2000 şeklinde giriniz']) !!}

                        </div>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('address', 'Adres: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::textarea('address', null,
                            ['class' => 'form-control',
                            'placeholder' => 'Şantiye adresi',
                            'rows' => '3']) !!}

                        </div>
                    </div>
                </div>

                <div class="form-group {{ $errors->has('site_chief') ? 'has-error' : '' }}">
                    <div class="row">
                        <div class="col-sm-2">
                            {!! Form::label('site_chief', 'Şantiye şefi: ', ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-10">
                            {!! Form::text('site_chief', null, ['class' => 'form-control', 'placeholder' => 'Şantiye şefini giriniz']) !!}

                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">

                <button type="submit" class="btn btn-primary">Şantiye Ekle</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                {!! Form::close() !!}
            </div>
        </div>

    </div>
</div>

<div id="deleteSiteConfirm" class="modal modal-danger fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Şantiye Sil</h4>
            </div>
            <div class="modal-body">
                <p class="siteDel"></p>
            </div>
            <div class="modal-footer">
                {!! Form::open([
                'url' => '/santiye/del',
                'method' => 'POST',
                'class' => 'form',
                'id' => 'siteDeleteForm',
                'role' => 'form'
                ]) !!}
                <button type="submit" class="btn btn-outline">Sil</button>
                <button type="button" class="btn btn-outline" data-dismiss="modal">Vazgeç</button>
                {!! Form::close() !!}
            </div>
        </div>

    </div>
</div>

@include('base.js')
        <!-- SlimScroll -->
<script src="<?= URL::to('/');?>/js/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= URL::to('/');?>/js/fastclick/fastclick.min.js"></script>

<script>
    if ($('.has-error')[0]) {
        $('#addNewSite').modal('show');

    }


    $(document).on("click", ".siteDelBut", function (e) {

        e.preventDefault();
        var mySiteId = $(this).data('id');
        var mySiteName = $(this).data('name');
        var myForm = $('.modal-footer #siteDeleteForm');
        var myP = $('.modal-body .siteDel');
        myP.html("<em>" + mySiteName + "</em> şantiyesini silmek istediğinizden emin misiniz?" +
                "<p>NOT: <span>SİLME İŞLEMİ GERİ DÖNDÜRÜLEMEZ!</span></p>");
        $('<input>').attr({
            type: 'hidden',
            name: 'siteDeleteIn',
            value: mySiteId
        }).appendTo(myForm);
        $('#deleteSiteConfirm').modal('show');
    });


</script>


</body>
</html>
