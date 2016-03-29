<?php
use App\Library\TurkishChar;
$expanding_arr = DB::table('modules')->select('expandable')->whereNotNull('expandable')->distinct()->get();
$expandable_arr = [];
$exp_mod_arr = [];
foreach ($expanding_arr as $expander) {
    $expander_arr = DB::table('modules')->select('id', 'name', 'slug')->where('expandable', '=', $expander->expandable)->get();
    foreach ($expander_arr as $expanding) {
        if (Auth::User()->hasAnyPermissionOnModule($expanding->id) || Auth::User()->isAdmin()) {
            array_push($expandable_arr, $expanding);
        }
    }
    if (sizeof($expandable_arr) > 0) {
        array_push($exp_mod_arr, [
                'name' => $expander->expandable,
                'modules' => $expandable_arr
        ]);
    }
}

?>
        <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

<body class="skin-blue">
<!-- header logo: style can be found in header.less -->
<div class="wrapper row-offcanvas row-offcanvas-left">
    <header class="main-header hidden-print">
        <a href="/" class="logo">
            <!-- Add the class icon to your logo image or logo icon to add the margining -->

            <span class="logo-lg"><b>Garden</b>Ltd.</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="hidden-print navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>

            </a>

            <!-- Navbar Right Menu -->
            @include('_user-menu')
        </nav>
    </header>


    <!-- Left side column. contains the logo and sidebar -->
    <aside class="left-side sidebar-offcanvas">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header"><a href="/tekil/{{$site->slug}}">{{TurkishChar::tr_up($site->job_name)}}</a></li>
                @foreach($modules->getModules() as $module)
                    @if(Auth::User()->hasAnyPermissionOnModule($module->id) || Auth::User()->isAdmin())
                        <?php
                        $addr = explode("/", $_SERVER['REQUEST_URI']);
                        $module_name = $addr[sizeof($addr) - 1];
                        if (strpos($module->icon, "ion-") !== false) {
                            $i_icon = "ion ";
                        } else {
                            $i_icon = "fa ";
                        }
                        $i_icon .= $module->icon
                        ?>

                        <li {!! strpos($module_name, $module->slug) !==false ? "class='active'" : "" !!}><a
                                    href="/tekil/{{$site->slug."/".$module->slug}}" class="menu">
                                {!! empty($module->icon) ? "" : ("<i class=\"$i_icon\"></i>")!!}
                                {{ $module->name}}
                            </a></li>
                    @endif

                @endforeach
                @if(sizeof($exp_mod_arr)>0)
                    <li class="treeview">
                        @foreach($exp_mod_arr as $exp)
                            <a href="#"  class="menu">
                                <i class="fa fa-dashboard"></i> <span>{{$exp['name']}}</span> <i
                                        class="fa fa-angle-left pull-right"></i>
                            </a>
                        <ul class="treeview-menu">
                            @foreach($exp['modules'] as $mod)
                                <li><a href="/tekil/{{$site->slug."/".$mod->slug}}"  class="menu"><i class="fa fa-circle-o"></i> {{$mod->name}}</a></li>
                            @endforeach
                        </ul>
                        @endforeach
                    </li>
                @endif

            </ul>

        </section>
        <!-- /.sidebar -->
    </aside>


    <aside class="right-side">

        <!-- Main content -->
        <section class="content">


            @if(Session::has('flash_message') || Session::has('flash_message_important') || Session::has('flash_message_error'))

                <div class="alert {{ Session::has('flash_message_error') ? 'alert-danger ' : 'alert-success ' }} fade in alert-box {{ Session::has('flash_message_important') ? 'alert-important' : '' }}">
                    @if(Session::has('flash_message_important') || Session::has('flash_message_error'))
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        @if(Session::has('flash_message_error'))
                            {!! Session::get('flash_message_error') !!}
                        @endif
                        @if(Session::has('flash_message_important'))
                            {!! Session::get('flash_message_important') !!}
                        @endif
                    @endif
                    {!! Session::get('flash_message') !!}
                </div>
            @endif
            @yield('content')
        </section>
        <!-- /.content -->

    </aside>


</div>
@include('base.js')
@yield('page-specific-js')
</body>

</html>