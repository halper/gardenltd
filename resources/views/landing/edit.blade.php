<?php
use App\Site;

?>

@extends('landing/landing')

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
        {{$user->name}}
    </h2>

    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Kullanıcı</a></li>
                <li><a href="#tab_2" data-toggle="tab">Şantiye Erişim</a></li>
                <li><a href="#tab_3" data-toggle="tab">Modül İzinleri</a></li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    {!! Form::model($user,
                    ['action' => ['AdminController@update', $user],
                    'method' => 'PATCH',
                    'class' => 'form',
                    'id' => 'userEditForm',
                    'role' => 'form']) !!}

                    @include('landing._user-form')

                    <button type="submit" class="btn btn-primary">Kaydet</button>

                    {!! Form::close() !!}
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    Kullanıcının erişim sağlayabileceği şantiyeleri seçin.<br/><br/>
                    <legend>Şantiyeler</legend>
                    <label class="checkbox-inline">
                        <input type="checkbox" id="sites-master" name="sites-master">
                        Tümünü seç</label>

                    {!! Form::model($user,
                    ['action' => ['AdminController@editSitePermissions', $user],
                    'method' => 'PATCH',
                    'class' => 'form',
                    'id' => 'siteUserForm',
                    'role' => 'form']) !!}
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
                                {!! Form::checkbox('sites[]', $site->id, $user->hasSite($site->id),
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
                    <button type="submit" class="btn btn-primary">Kaydet</button>

                    {!! Form::close() !!}

                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="tab_3">
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table class="table table-striped table-responsive">
                                <thead>
                                <tr>
                                    <th>Modül adı</th>
                                    <th>İzinler</th>
                                </tr>
                                </thead>
                                <tbody>
                                {!! Form::model($user,
                                ['action' => ['AdminController@editModulePermissions', $user],
                                'method' => 'PATCH',
                                'class' => 'form',
                                'id' => 'moduleUserForm',
                                'role' => 'form']) !!}
                                <tr><label class="checkbox-inline">
                                        <input type="checkbox" id="modules-master" name="modules-master">
                                        Tüm izinleri seç</label></tr>

                                @foreach(App\Module::getAllModules() as $module)
                                    <tr>
                                        <td>
                                            <label class="checkbox-inline">
                                                {{ $module->name}}</label>
                                        </td>
                                        <td>
                                            <div class="col-md-3">
                                                <label class="checkbox-inline">
                                                    {!! Form::checkbox('modules[]', $module->id."1",
                                                    $user->hasPermissionOnModule(1, $module->id),
                                                    [
                                                    'id'=>$module->slug,
                                                    ])
                                                    !!}{{ App\Permission::find(1)->definition}}</label>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="checkbox-inline">
                                                    {!! Form::checkbox('modules[]', $module->id."2",
                                                    $user->hasPermissionOnModule(2, $module->id),
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
                    <button type="submit" class="btn btn-primary">Kaydet</button>

                    {!! Form::close() !!}
                </div>
                <!-- /.tab-pane -->
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
    </div>
@stop