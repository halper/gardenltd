<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Garden İnşaat - {{$site->job_name}}</title>
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

<body class="skin-blue sidebar-mini">
<!-- header logo: style can be found in header.less -->
<header class="header">
    <a href="/" class="logo">
        <!-- Add the class icon to your logo image or logo icon to add the margining -->
        Garden Ltd.
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>

        <!-- Navbar Right Menu -->
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
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="#" class="btn btn-default btn-flat">Bilgilerim</a>
                            </div>
                            @if(Auth::User()->isAdmin())
                                <div class="col-md-4">
                                    <a href="/admin/ayarlar" class="btn btn-default btn-flat">Ayarlar</a>
                                </div>
                            @endif
                            <div class="pull-right">
                                <a href="/auth/logout" class="btn btn-default btn-flat">Çıkış</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<div class="wrapper">

    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header">{{mb_strtoupper($site->job_name, 'utf-8')}}</li>

                @foreach($modules->getModules() as $module)
                    @if(Auth::User()->hasAnyPermissionOnModule($module->id) || Auth::User()->isAdmin())
                    <?php
                    if (strpos($module->icon, "ion-") !== false) {
                        $i_icon = "ion ";
                    } else {
                        $i_icon = "fa ";
                    }
                    $i_icon .= $module->icon
                    ?>

                    <li><a href="{{$site->slug."/".$module->slug}}">
                            {!! empty($module->icon) ? "" : ("<i class=\"$i_icon\"></i>")!!}
                            {{ $module->name}}
                        </a></li>
                    @endif

                @endforeach


            </ul>

        </section>
        <!-- /.sidebar -->
    </aside>

    <div class="content-wrapper">

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

    @include('base.js')
    @yield('page-specific-js')
</div>
</body>

</html>