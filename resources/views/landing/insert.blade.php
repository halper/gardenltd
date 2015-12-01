<?php
$sites = \App\Site::getSites();
$city_options = '';
$phone_options = '';
$mobile_options = '';
$site_options = '';
$eq_json = json_encode(\App\Equipment::all());
$staffs = \App\Staff::all();
$staff_json = [];


foreach (\App\City::all() as $city) {
    $city_options .= "'<option value=\"$city->id\">" . \App\Library\TurkishChar::tr_up($city->name) . "</option>'+\n";
}

foreach (\App\AreaCode::all() as $area) {
    $phone_options .= "'<option value=\"$area->id\">$area->code</option>'+\n";
}

foreach (\App\MobileCode::all() as $mobile) {
    $mobile_options .= "'<option value=\"$mobile->id\">$mobile->code</option>'+\n";
}

foreach ($sites as $site) {
    $site_options .= "'<option value=\"$site->id\">" . \App\Library\TurkishChar::tr_up($site->job_name) . "</option>'+\n";
}

foreach ($staffs as $staff) {
    array_push($staff_json, ['name' => $staff->staff, 'department' => \App\Library\TurkishChar::tr_up($staff->department->department)]);
}
$staff_json = json_encode($staff_json);
$dept_json = json_encode(\App\Department::all());

?>


@extends('landing/landing')

@section('page-specific-css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        var addApp = angular.module('addApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('EquipmentController', function ($scope, $http, $filter) {
            $scope.presentEquipments = {!! $eq_json!!};


            $scope.error = '';
            $scope.newEquipment = '';
            $scope.name = '';

            $scope.addEquipment = function () {
                $scope.newEquipment = '';
                $scope.error = '';
                if (!$('input[name="equipment"]').val()) {
                    return;
                }
                $scope.name = $filter('trUp')($scope.name);
                var addToArray = true;
                for (var i = 0; i < $scope.presentEquipments.length; i++) {
                    if ($scope.presentEquipments[i].name === $scope.name) {
                        addToArray = false;
                    }
                }
                if (!addToArray) {
                    $scope.error = $scope.name + ' ekipmanlarınız arasında mevcut!';
                    $scope.name = '';

                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-equipment", {
                    'name': $scope.name
                }).success(function (response) {

                    $scope.newEquipment = $scope.name;
                    $scope.presentEquipments.push(
                            {name: $scope.name}
                    );

                    $scope.name = '';
                    $scope.error = '';
                });
            }
        }).filter('trUp', function () {
            return function (data) {
                if (data) {
                    data = data.replace(/ı/g, 'I');
                    data = data.replace(/i/g, 'İ');
                    return data.toUpperCase();
                }
            }
        }).controller('StaffController', function ($scope, $http, $filter) {
            $scope.staffs = {!! $staff_json!!};
            $scope.departments = {!! $dept_json !!};

            $scope.name = '';
            $scope.department_name = '';
            $scope.dept = '';
            $scope.deptError = '';
            $scope.newStaff = '';
            $scope.newDept = '';

            $scope.addStaff = function () {
                $scope.newStaff = '';
                $scope.error = '';
                if (!$('input[name="staff"]').val() || !$('select[name="department"]').val()) {
                    if (!$('input[name="staff"]').val()) {
                        $scope.error = 'Personel giriniz!';
                    }
                    if (!$('select[name="department"]').val()) {
                        $scope.error = 'Departman seçiniz!';
                    }
                    return;
                }
                $scope.name = $filter('trUp')($scope.name);
                var addToArray = true;
                for (var i = 0; i < $scope.staffs.length; i++) {
                    if ($scope.staffs[i].name === $scope.name) {
                        addToArray = false;
                    }
                }

                if (!addToArray) {
                    $scope.error = $scope.name + ' personeliniz arasında mevcut!';
                    $scope.name = '';
                    $scope.dept = '';

                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-staff", {
                    staff: $scope.name,
                    department_id: $scope.dept.id
                }).success(function (response) {


                    $scope.newStaff = $scope.name;
                    $scope.staffs.push(
                            {
                                name: $scope.name,
                                department: $scope.dept.department
                            }
                    );

                    $scope.name = '';
                    $scope.dept = '';
                    $scope.error = '';
                });
            };
            $scope.addDepartment = function () {
                $scope.newDept = '';
                $scope.deptError = '';
                if (!$('input[name="department"]').val()) {
                    if (!$('input[name="department"]').val()) {
                        $scope.deptError = 'Departman giriniz!';
                    }

                    return;
                }
                $scope.department_name = $filter('trUp')($scope.department_name);
                var addToArray = true;
                for (var i = 0; i < $scope.departments.length; i++) {
                    if ($scope.departments[i].department === $scope.department_name) {
                        addToArray = false;
                    }
                }

                if (!addToArray) {
                    $scope.error = $scope.department_name + ' departmanlarınız arasında mevcut!';
                    $scope.department_name = '';
                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-department", {
                    department: $scope.department_name
                }).success(function (response) {


                    $scope.newDept = $scope.department_name;
                    $scope.departments.push(
                            response
                    );

                    $scope.department_name = '';
                    $scope.deptError = '';
                });
            };
        });


        $(".js-example-basic-multiple").select2({
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });
        $(".city-select").select2({
            placeholder: "Şehir seçiniz",
            allowClear: true
        });
$(".mobile-select").select2({
            placeholder: "Alan kodu",
            allowClear: true
        });

        $(".js-example-basic-single").select2({
            placeholder: "Taşeronun bağlı olduğu şantiyeyi seçiniz",
            allowClear: true
        });

        $('.dateRangePicker').datepicker({
            autoclose: true,
            firstDay: 1,
            format: 'dd.mm.yyyy',
            startDate: '01.01.2010',
            endDate: '30.12.2100'
        });
    </script>

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom" ng-app="addApp" ng-controller="StaffController">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Taşeron</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Personel</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Departman</a></li>
                    <li><a href="#tab_4" data-toggle="tab">İş Makinesi</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-sm-12">

                                {!! Form::open([
                                                    'url' => "/admin/add-subcontractor",
                                                    'method' => 'POST',
                                                    'class' => 'form .form-horizontal',
                                                    'id' => 'subcontractorInsertForm',
                                                    'role' => 'form',
                                                    'files' => true
                                                    ])!!}
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('name', 'Taşeronun Adı:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Taşeronun adını giriniz']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('address', 'Açık Adresi:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::textarea('address', null, ['class' => 'form-control',
                                                                                'placeholder' => 'Taşeronun adresini giriniz',
                                                                                 'rows' => '2']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('city_id') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('city_id', 'Şehir:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            <select name="city_id"
                                                    class="city-select form-control">

                                                {!! $city_options !!}
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('official') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('official', 'Firma Yetkilisinin Adı:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('official', null, ['class' => 'form-control', 'placeholder' => 'Firma yetkilisinin adını giriniz']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('title', 'Firma Yetkilisinin Unvanı:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Firma yetkilisinin unvanını giriniz']) !!}

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('phone', 'Telefon numarası:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-2">
                                            <select name="area_code_id"
                                                    class="mobile-select form-control">

                                                {!! $phone_options !!}
                                            </select>

                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::number('phone', null, ['class' => 'form-control', 'placeholder' => 'Telefon numarası giriniz']) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('fax') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('fax', 'Fax numarası:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-2">
                                            <select name="fax_code_id"
                                                    class="mobile-select form-control">

                                                {!! $phone_options !!}
                                            </select>

                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::number('fax', null, ['class' => 'form-control', 'placeholder' => 'Fax numarası giriniz']) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('mobile', 'Cep numarası:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-2">
                                            <select name="mobile_code_id"
                                                    class="mobile-select form-control">

                                                {!! $mobile_options !!}
                                            </select>

                                        </div>
                                        <div class="col-sm-3">
                                            {!! Form::number('mobile', null, ['class' => 'form-control', 'placeholder' => 'Cep numarası giriniz']) !!}

                                        </div>
                                    </div>
                                </div>


                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('email', 'E-posta Adresi: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'E-posta adresini giriniz']) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('web') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('web', 'Web Adresi: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('web', null, ['class' => 'form-control', 'placeholder' => 'Web adresini giriniz']) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('tax_office') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('tax_office', 'Vergi Dairesi: ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('tax_office', null, ['class' => 'form-control', 'placeholder' => 'Vergi dairesini giriniz']) !!}

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('tax_number') ? 'has-error' : '' }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            {!! Form::label('tax_number', 'Vergi Numarası:* ', ['class' => 'control-label']) !!}
                                        </div>
                                        <div class="col-sm-10">
                                            {!! Form::text('tax_number', null, ['class' => 'form-control', 'placeholder' => 'Vergi numarasını giriniz']) !!}

                                        </div>
                                    </div>
                                </div>



                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-flat btn-primary">Taşeron Ekle</button>

                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>
                    </div>

                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">


                                            <div class="col-md-1">
                                                <label for="name">Personel Adı: </label>
                                            </div>
                                            <div class="col-md-4">

                                                <input type="text" class="form-control"
                                                       name="staff" ng-model="name"
                                                       value="" autocomplete="off"
                                                       placeholder="Yeni personel giriniz"/>

                                            </div>

                                            <div class="col-md-3">

                                                <select class="form-control" name="department" ng-model="dept"
                                                        ng-options="dept as dept.department for dept in departments">
                                                    {{--{!! $dept_options !!}}--}}
                                                    <option value='' selected disabled>Departman seçiniz</option>
                                                </select>

                                            </div>


                                            <div class="col-xs-12 col-md-2 ">
                                                <button type="button" ng-click="addStaff()"
                                                        class="btn btn-primary btn-flat btn-block">Ekle
                                                </button>
                                            </div>

                                            <div class="col-md-3" ng-show="newStaff != ''">
                                                <span class="text-success"><%newStaff%> personeller arasına eklendi</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3" ng-show="error != ''">
                                        <span class="text-danger"><%error%></span>
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Mevcut Personel</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"
                                         ng-repeat="st in staffs | filter:(name | trUp) track by $index">
                                        <span><% st.name | trUp %> (<%st.department%>)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">


                                            <div class="col-md-1">
                                                <label for="name">Departman Adı: </label>
                                            </div>
                                            <div class="col-md-4">

                                                <input type="text" class="form-control"
                                                       name="department" ng-model="department_name"
                                                       value="" autocomplete="off"
                                                       placeholder="Yeni departman giriniz"/>

                                            </div>


                                            <div class="col-xs-12 col-md-2 ">
                                                <button type="button" ng-click="addDepartment()"
                                                        class="btn btn-primary btn-flat btn-block">Ekle
                                                </button>
                                            </div>
                                            <div class="col-md-3" ng-show="newDept != ''">
                                                <span class="text-success"><%newDept%> departmanlar arasına eklendi</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3" ng-show="deptError != ''">
                                        <span class="text-danger"><%deptError%></span>
                                    </div>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Mevcut Departmanlar</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"
                                         ng-repeat="dept in departments | filter:(department_name | trUp) track by $index">
                                        <span><% dept.department | trUp %></span>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div ng-controller="EquipmentController">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">


                                                <div class="col-md-1">
                                                    <label for="name">Ekipman Adı: </label>
                                                </div>
                                                <div class="col-md-4">

                                                    <input type="text" class="form-control"
                                                           name="equipment" ng-model="name"
                                                           value="" autocomplete="off"/>

                                                </div>


                                                <div class="col-xs-12 col-md-2 ">
                                                    <button type="button" ng-click="addEquipment()"
                                                            class="btn btn-primary btn-flat btn-block">Ekle
                                                    </button>
                                                </div>
                                                <div class="col-md-3" ng-show="newEquipment != ''">
                                                    <span class="text-success"><%newEquipment%> ekipmanlar arasına eklendi</span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3" ng-show="error != ''">
                                            <span class="text-danger"><%error%></span>
                                        </div>

                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3>Mevcut Ekipmanlar</h3>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"
                                             ng-repeat="eq in presentEquipments | filter:(name | trUp) |orderBy:'name' track by $index">
                                            <span><% eq.name %></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->

                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </div>

@stop
