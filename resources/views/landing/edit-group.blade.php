<?php
use App\Site;

if (Session::has('tab')) {
    $tab = Session::get('tab');
} else {
    $tab = '';
}

?>

@extends('landing.landing')

@section('page-specific-js')

    <script>
        var master = $('#sites-master');
        var childOfMaster = $('[name=sites\\[\\]]');
        master.on('click', (function () {
                    master[0].checked ? childOfMaster.prop('checked', true) : childOfMaster.prop('checked', false);
                })
        );

        childOfMaster.change(function () {
            $('[name=sites\\[\\]]:checked').length == childOfMaster.length ? master.prop('checked', true) : master.prop('checked', false);

        });

        var modulesMaster = $('#modules-master');
        var childOfModules = $('[name=modules\\[\\]]');
        modulesMaster.on('click', (function () {
                    modulesMaster[0].checked ? childOfModules.prop('checked', true) : childOfModules.prop('checked', false);
                })
        );

        childOfModules.change(function () {
            $('[name=modules\\[\\]]:checked').length == childOfModules.length ? modulesMaster.prop('checked', true) : modulesMaster.prop('checked', false);

        });

    </script>

@stop

@section('content')

    <h2 class="page-header">
        {{$group->name}}
    </h2>

    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li {{empty($tab) ? 'class=active' : ''}}><a href="#tab_1" data-toggle="tab">Kullanıcılar</a></li>
                <li {{$tab == 2 ? 'class=active' : ''}}><a href="#tab_2" data-toggle="tab">Şantiye Erişim</a></li>
                <li {{$tab == 3 ? 'class=active' : ''}}><a href="#tab_3" data-toggle="tab">Modül İzinleri</a></li>
                <li {{$tab == 4 ? 'class=active' : ''}}><a href="#tab_4" data-toggle="tab">Özel İzinler</a></li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane {{empty($tab) ? 'active' : ''}}" id="tab_1">
                    <form action="/admin/add-users-to-group" class="form" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" value="{{$group->id}}" name="group_id">

                        <div class="row">
                            @foreach(\App\User::all() as $user)
                                @if(!$user->isAdmin())
                                    <div class="col-md-3">
                                        <label class="checkbox-inline">
                                            <input name="users[]" type="checkbox"
                                                   value="{{$user->id}}" {{$group->hasUser($user->id) ? "checked" : ""}}>{{$user->name}}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <br>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane {{$tab == 2 ? 'active' : ''}}" id="tab_2">
                    Grubun erişim sağlayabileceği şantiyeleri seçin.<br/><br/>
                    <legend>Şantiyeler</legend>
                    <label class="checkbox-inline">
                        <input type="checkbox" id="sites-master" name="sites-master">
                        Tümünü seç</label>

                    <form action="/admin/add-sites-to-group" class="form" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" value="{{$group->id}}" name="group_id">
                        <?php
                        $i = 0;
                        ?>
                        @foreach(Site::getSites() as $site)
                            <?php
                            $i_modulus = $i % 6;
                            $i = $i + 1;
                            ?>
                            {!! $i_modulus == 0 ? "
                            <div class=\"row\">" : "" !!}
                            <div class="col-md-2 col-xs-3">
                                <label class="checkbox-inline">
                                    {!! Form::checkbox('sites[]', $site->id, $group->hasSite($site->id),
                                    [
                                    'id'=>$site->slug,
                                    ])
                                    !!}{{ $site->job_name}}</label>
                            </div>
                            {!! $i_modulus == 5 ? "
                        </div>" : "" !!}
                        @endforeach
                        {!! $i_modulus != 5 ? "
                    </div>
                    " : "" !!}
                        <br/>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane {{$tab == 3 ? 'active' : ''}}" id="tab_3">
                    <form action="/admin/add-modules-to-group" class="form" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" value="{{$group->id}}" name="group_id">

                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Modül adı</th>
                                        <th>İzinler</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr><label class="checkbox-inline">
                                            <input type="checkbox" id="modules-master" name="modules-master">
                                            Tüm izinleri seç</label></tr>

                                    @foreach(App\Module::getAllModules() as $module)
                                        <tr>
                                            <td>
                                                <label class="checkbox-inline">
                                                    {{ !empty($module->expandable) ? $module->name . " (". $module->expandable . ")" : $module->name}}</label>
                                            </td>
                                            <td>
                                                <div class="col-md-3">
                                                    <label class="checkbox-inline">
                                                        {!! Form::checkbox('modules[]', $module->id."1",
                                                        $group->hasPermissionOnModule(1, $module->id),
                                                        [
                                                        'id'=>$module->slug,
                                                        ])
                                                        !!}{{ App\Permission::find(1)->definition}}</label>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="checkbox-inline">
                                                        {!! Form::checkbox('modules[]', $module->id."2",
                                                        $group->hasPermissionOnModule(2, $module->id),
                                                        [
                                                        'id'=>$module->slug,
                                                        ])
                                                        !!}{{ App\Permission::find(2)->definition}}</label>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br/>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane  {{$tab == 4 ? 'active' : ''}}" id="tab_4">
                    <?php
                    $legend_names = DB::table('special_permissions')->select('group')->distinct()->get();
                    ?>
                    <form action="/admin/add-special-permissions-to-group" class="form" method="POST">
                        {{csrf_field()}}
                        <input type="hidden" value="{{$group->id}}" name="group_id">
                        @foreach($legend_names as $legend)
                            <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <legend>{{$legend->group}}</legend>
                                </div>
                                <?php
                                $special_permissions = DB::table('special_permissions')->where('group', '=', $legend->group)->get();
                                ?>

                                @foreach($special_permissions as $special_permission)
                                    <div class="col-sm-4">
                                        <label class="checkbox-inline">
                                            <input name="special-permissions[]" type="checkbox"
                                                   value="{{$special_permission->id}}"
                                                    {{$group->hasSpecialPermission($special_permission->id) ? "checked" : ""}}>{{$special_permission->name}}
                                        </label>
                                    </div>
                                @endforeach

                            </div>
                            </div>
                        @endforeach
                            <br>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-primary btn-flat">Kaydet</button>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
                <!-- /.tab-pane -->
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
    </div>
@stop