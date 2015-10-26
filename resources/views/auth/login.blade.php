<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Garden İnşaat Otomasyon Sistemi</title>

    @include('base.css')
    <link href="<?= URL::to('/'); ?>/css/login.css" rel="stylesheet" type="text/css"/>

</head>

<body>

<!-- **********************************************************************************************************************************************************
MAIN CONTENT
*********************************************************************************************************************************************************** -->

<div id="login-page">
    <div class="container">
        <div class="row center-block">

            <div class="box box-primary col-sm-12 top-margin">
                <div class="box-header">
                    <h2 class="box-title with-border" style="color: #27ae60; font-weight: bold">GARDEN İNŞAAT <br>
                        <small>Raporlama, Analiz ve Yönetim Sistemi</small>
                    </h2>
                </div>
                <!-- form start -->
                <form role="form" method="POST" action="/auth/login">
                    {!! csrf_field() !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">E-posta adresiniz</label>
                            <input type="email" name="email" class="form-control" id="exampleInputEmail1"
                                   placeholder="E-posta" value="{{ old('email') }}">

                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Şifreniz</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Şifre">

                        </div>


                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Beni hatırla
                            </label>
                        </div>


                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" style="display: block; width: 100%;" class="btn btn-primary btn-flat btn">
                            Giriş
                        </button>
                    </div>
                </form>

            </div>
        </div>


    </div>

    <div class="login-logo">
        <div class="container">
            <div class="col-md-offset-2">
                <div class="login-logo-header"><img src="<?= URL::to('/'); ?>/img/bottom.jpg"></div>

            </div>
        </div>
    </div>
</div>

    <!-- js placed at the end of the document so the pages load faster -->
    @include('base.js')

            <!--BACKSTRETCH-->
    <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/jquery-backstretch/2.0.4/jquery.backstretch.min.js"></script>
    <script>
        $.backstretch("<?= URL::to('/');?>/img/bg-blue.png", {speed: 500});
    </script>


</body>
</html>
