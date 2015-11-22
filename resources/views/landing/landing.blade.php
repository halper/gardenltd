<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Garden İnşaat - Şantiyeler Ana Sayfası</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    @include('base.css')
    @yield('page-specific-css')

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

    <header class="header">
        <nav class="navbar navbar-static-top">
            <div class="container">

                <!-- Navbar Right Menu -->
                @include('_user-menu')
                <!-- /.navbar-custom-menu -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </header>

    <?php
    $actual_link = "$_SERVER[REQUEST_URI]";
    $tmp = explode("/", $actual_link);


    ?>
            <!-- Full Width Column -->
    <div class="content-wrapper" style="margin-left: 0">

        <div class="container">
            <section class="content-header">
                <h1>
                    <?php
                    $page_header = end($tmp);
                    if (strpos($page_header, "-")) {
                        $page_header = str_replace("-", " ", $page_header);
                        $page_header = ucwords($page_header);
                    } else {
                        $page_header = ucfirst($page_header);
                    }
                    $current_path = "";
                    if (str_contains("duzenle", $tmp)) {
                        $page_header = "Kullanıcı Bilgileri Düzenle";
                    }
                    ?>
                    {{$page_header}}
                </h1>
                <ol class="breadcrumb">
                    @foreach($tmp as $bread_li)

                        <li class="{{strcmp($bread_li, (string)end($tmp)) == 0 ? "active" : ""}}">
                            @if(strcmp($bread_li, (string)end($tmp)) == 0)
                                {{$bread_li == "" ? "Ana sayfa" : ucwords($bread_li)}}
                            @else
                                <a href="{{$bread_li == "" ? "/" : $current_path.$bread_li}}">{{$bread_li == "" ? "Ana sayfa" : ucwords($bread_li)}}</a>
                            @endif
                        </li>
                        <?php

                        $current_path .= $bread_li . "/";
                        ?>
                    @endforeach
                </ol>
            </section>

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


@include('base.js')
        <!-- SlimScroll -->
<script src="<?= URL::to('/');?>/js/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?= URL::to('/');?>/js/fastclick/fastclick.min.js"></script>

@yield('page-specific-js')


</body>
</html>
