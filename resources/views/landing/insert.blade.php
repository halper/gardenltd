<?php
$sites = \App\Site::getSites();
$eq_json = json_encode(\App\Equipment::all());
$tag_json = json_encode(\App\Tag::all());
$staffs = \App\Staff::allStaff();
$staff_json = [];
$site_options = '<option></option>';



foreach ($sites as $site) {
    $site_options .= "<option value=\"$site->id\">" . \App\Library\TurkishChar::tr_up($site->job_name) . "</option>";
}

foreach ($staffs as $staff) {
    array_push($staff_json, ['name' => $staff->staff, 'department' => \App\Library\TurkishChar::tr_up($staff->department->department)]);
}
$staff_json = json_encode($staff_json);
$dept_json = json_encode(\App\Department::all());
$mat_json = json_encode(\App\Material::all());

$staff_options = '<option></option>';
$management_depts = new \App\Department();

foreach ($management_depts->management() as $dept) {
    $staff_options .= "<optgroup label=\"$dept->department\">";
    foreach ($dept->staff()->notGarden()->get() as $staff) {
        $staff_options .= "<option value=\"$staff->id\">" . \App\Library\TurkishChar::tr_up($staff->staff) . "</option>";
    }
}

?>


@extends('landing/landing')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/jquery.number.js" type="text/javascript"></script>
    <script>
        String.prototype.turkishToLower = function () {
            var string = this;
            var letters = {"İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç"};
            string = string.replace(/(([İIŞĞÜÇÖ]))/g, function (letter) {
                return letters[letter];
            });
            return string.toLowerCase();
        };
        $("#add-personnel").on("click", function (e) {
            e.preventDefault();
            var tckInput = $('input[name=tck_no]');
            var tck = tckInput.val();
            if (tck.length != 11) {
                tckInput.parent('div').parent().closest('div.row').append(
                        '<div class="col-sm-4">' +
                        '<span class="text-danger">TCK No giriniz!</span>' +
                        '</div>'
                );
                tckInput.parent('div').parent().closest('div.row').addClass('has-error');
                return;
            }
            var unique;
            $.ajax({
                type: 'POST',
                url: '{{"/admin/check-tck"}}',
                data: {
                    "tck_no": tck
                }
            }).success(function (response) {
                unique = (response.indexOf('unique') > -1);
                if (!unique) {
                    tckInput.parent('div').parent().closest('div.row').append(
                            '<div class="col-sm-4">' +
                            '<span class="text-danger">TCK No sistemde kayıtlı!</span>' +
                            '</div>'
                    );
                    tckInput.parent('div').parent().closest('div.row').addClass('has-error');
                }
                else {
                    $('#personnelInsertForm').submit();
                }
            });

        });
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
                var name = $scope.name.turkishToLower();
                var addToArray = true;
                for (var i = 0; i < $scope.presentEquipments.length; i++) {
                    if ($scope.presentEquipments[i].name.turkishToLower() === name) {
                        addToArray = false;
                    }
                }
                if (!addToArray) {
                    $scope.error = $scope.name + ' ekipmanlarınız arasında mevcut!';
                    $scope.name = '';

                    return;
                }
                $scope.name = $filter('trUp')($scope.name);
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
        }).controller('TagController', function ($scope, $http, $filter) {
            $scope.presentTags = {!! $tag_json!!};

            $scope.error = '';
            $scope.newTag = '';
            $scope.name = '';

            $scope.addTag = function () {
                $scope.newTag = '';
                $scope.error = '';
                if (!$('input[name="tag"]').val()) {
                    return;
                }
                var name = $scope.name.turkishToLower();
                var addToArray = true;
                for (var i = 0; i < $scope.presentTags.length; i++) {
                    if ($scope.presentTags[i].name.turkishToLower() === name) {
                        addToArray = false;
                    }
                }
                if (!addToArray) {
                    $scope.error = $scope.name + ' etiketleriniz arasında mevcut!';
                    $scope.name = '';

                    return;
                }
                $scope.name = $filter('trUp')($scope.name);
                $http.post("<?=URL::to('/');?>/admin/add-tag", {
                    'name': $scope.name
                }).success(function (response) {

                    $scope.newTag = $scope.name;
                    $scope.presentTags.push(
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
            $scope.materials = {!! $mat_json !!};

            $scope.name = '';
            $scope.department_name = '';
            $scope.material_name = '';
            $scope.dept = '';
            $scope.mat = '';
            $scope.deptError = '';
            $scope.matError = '';
            $scope.newStaff = '';
            $scope.newDept = '';
            $scope.newMat = '';

            $scope.stocks = [];
            $scope.newStock = '';
            $scope.stockError = '';
            $scope.stockName = '';
            $scope.stockUnit = '';
            $scope.stockTotal = '';

            $scope.getStocks = function () {
                $http.get("<?=URL::to('/');?>/admin/retrieve-stocks")
                        .then(function (response) {
                            $scope.stocks = response.data;
                        });
            };

            $scope.addStock = function () {
                var arrayCheck = false;
                var tempName = $scope.stockName.turkishToLower();
                $scope.stockName = $filter('trUp')($scope.stockName);
                angular.forEach($scope.stocks, function (value, index) {
                    if (value.name.turkishToLower() === tempName) {
                        $scope.stockError = $scope.stockName + " mevcut demirbaşlarınız arasında yer almakta!";
                        arrayCheck = true;
                    }
                });
                if (arrayCheck) {
                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-stock", {
                    name: $scope.stockName,
                    unit: $scope.stockUnit,
                    total: $scope.stockTotal
                }).then(function (response) {
                    $scope.stocks.push({
                        name: $scope.stockName,
                        unit: $scope.stockUnit,
                        total: $scope.stockTotal
                    });
                    $scope.stockName = '';
                    $scope.stockUnit = '';
                    $scope.stockTotal = '';
                });
            };

            $scope.manufacturings = [];
            $scope.newMan = '';
            $scope.manError = '';

            $scope.getManufacturings = function () {
                $http.get("<?=URL::to('/');?>/admin/retrieve-manufacturings")
                        .then(function (response) {
                            $scope.manufacturings = response.data;
                        });
            };

            $scope.addManufacturing = function () {
                var arrayCheck = false;
                var tempName = $scope.manufacturing_name.turkishToLower();
                $scope.manufacturing_name = $filter('trUp')($scope.manufacturing_name);
                angular.forEach($scope.manufacturings, function (value, index) {
                    if (value.turkishToLower() === tempName) {
                        $scope.manError = $scope.manufacturing_name + " mevcut faaliyet kollarınız arasında yer almakta!";
                        arrayCheck = true;
                    }
                });
                if (arrayCheck) {
                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-manufacturing", {
                    name: $scope.manufacturing_name
                }).then(function (response) {
                    $scope.manufacturings.push($scope.manufacturing_name);
                    $scope.manufacturing_name = '';
                });
            };

            $scope.addStaff = function () {
                $scope.newStaff = '';
                $scope.error = '';
                if (!$('input[name="staff"]').val() || !$('select[name="department"]').val()) {
                    if (!$('input[name="staff"]').val()) {
                        $scope.error = 'İş kolu giriniz!';
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
                    $scope.error = $scope.name + ' iş kollarınız arasında mevcut!';
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
                var name = $scope.department_name.turkishToLower();
                $scope.department_name = $filter('trUp')($scope.department_name);
                var addToArray = true;
                for (var i = 0; i < $scope.departments.length; i++) {
                    if ($scope.departments[i].department.turkishToLower() === name) {
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
            $scope.addMaterial = function () {
                $scope.newMat = '';
                $scope.matError = '';
                if (!$('input[name="material"]').val()) {
                    if (!$('input[name="material"]').val()) {
                        $scope.matError = 'Malzeme giriniz!';
                    }

                    return;
                }
                var name = $scope.material_name.turkishToLower();
                $scope.material_name = $filter('trUp')($scope.material_name);
                var addToArray = true;
                for (var i = 0; i < $scope.materials.length; i++) {
                    if ($scope.materials[i].material.turkishToLower() === $scope.material_name) {
                        addToArray = false;
                    }
                }

                if (!addToArray) {
                    $scope.error = $scope.material_name + ' malzemeleriniz arasında mevcut!';
                    $scope.material_name = '';
                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-material", {
                    material: $scope.material_name
                }).success(function (response) {


                    $scope.newMat = $scope.material_name;
                    $scope.materials.push(
                            response
                    );

                    $scope.material_name = '';
                    $scope.matError = '';
                });
            };

            $scope.expGroup = '';
            $scope.expName = '';
            $scope.expenditures = '';
            $scope.newExp = '';

            $scope.getExpenditures = function () {
                $http.get("<?=URL::to('/');?>/admin/retrieve-expdetail")
                        .then(function (response) {
                            $scope.expenditures = response.data;
                        });

            };
            $scope.getExpenditures();

            $scope.addExpenditure = function () {
                $scope.newExp = '';
                $scope.error = '';
                if (!$('input[name="type"]').val() || !$('select[name="group"]').val()) {
                    if (!$('input[name="type"]').val()) {
                        $scope.error = 'Gider adı giriniz!';
                    }
                    if (!$('select[name="group"]').val()) {
                        $scope.error = 'Gider türü seçiniz!';
                    }
                    return;
                }
                $scope.expName = $filter('trUp')($scope.expName);
                var addToArray = true;
                for (var i = 0; i < $scope.expenditures.length; i++) {
                    if ($scope.expenditures[i].name === $scope.expName) {
                        addToArray = false;
                    }
                }

                if (!addToArray) {
                    $scope.error = $scope.expName + ' gider kalemleriniz arasında mevcut!';
                    $scope.expName = '';
                    $scope.expGroup = '';

                    return;
                }
                $http.post("<?=URL::to('/');?>/admin/add-expenditure", {
                    name: $scope.expName,
                    group: $scope.expGroup
                }).then(function (response) {
                    $scope.newExp = $scope.expName;
                    $scope.expName = '';
                    $scope.expGroup = '';
                    $scope.error = '';
                    $scope.getExpenditures();
                });
            };
        }).filter('searchFor', function () {
            return function (arr, searchStr) {
                if (!searchStr) {
                    return arr;
                }
                var result = [];
                searchStr = searchStr.turkishToLower();
                angular.forEach(arr, function (item) {
                    if (item.turkishToLower().indexOf(searchStr) !== -1) {
                        result.push(item);
                    }
                });
                return result;
            };
        });

        $(document).ready(function () {

            angular.element('#addApp').scope().getManufacturings();
            angular.element('#addApp').scope().getStocks();

            $(".js-example-basic-multiple").select2({
                placeholder: "Çoklu seçim yapabilirsiniz",
                allowClear: true
            });
            $(".staff-select").select2({
                placeholder: "İş kolu seçiniz",
                allowClear: true
            });
            $('.dateRangePicker').datepicker({
                language: 'tr'
            });
            $('.number').number(true, 2, ',', '.');
        });

        $('a[href=#tab_1]').on("shown.bs.tab", function () {
            $(".city-select").select2({
                placeholder: "Şehir seçiniz",
                allowClear: true
            });
            $(".mobile-select").select2({
                placeholder: "Alan kodu",
                allowClear: true
            });

        });
    </script>

@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom" ng-app="addApp" ng-controller="StaffController" id="addApp">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_5" data-toggle="tab">Personel</a></li>
                    <li><a href="#tab_1" data-toggle="tab">Alt Yüklenici</a></li>
                    <li><a href="#tab_2" data-toggle="tab">İş Kolu</a></li>
                    <li><a href="#tab_man" data-toggle="tab">Faaliyet Alanı</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Departman</a></li>
                    <li><a href="#tab_stock" data-toggle="tab">Demirbaş</a></li>
                    <li><a href="#tab_mat" data-toggle="tab">Malzeme</a></li>
                    <li><a href="#tab_submat" data-toggle="tab">Bağlantılı Malzeme</a></li>
                    <li><a href="#tab_4" data-toggle="tab">İş Makinesi</a></li>
                    <li><a href="#tab_tag" data-toggle="tab">Etiketler</a></li>
                    <li><a href="#tab_exp" data-toggle="tab">Gider Kalemleri</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_5">
                        <div class="row">
                            <div class="col-sm-12">
                                <p>Bu alandan sadece Garden personeli ekleyebilirsiniz.
                                    Alt yüklenici personeli eklemek için ilgili şantiyenin alt yüklenici cari hesap
                                    sayfasına gidiniz.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">

                                {!! Form::open([
                                                    'url' => "/admin/add-personnel",
                                                    'method' => 'POST',
                                                    'class' => 'form',
                                                    'id' => 'personnelInsertForm',
                                                    'role' => 'form',
                                                    'files' => true
                                                    ])!!}
                                @include('landing._personnel-insert-form')

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_1">
                        <div class="row">
                            <div class="col-sm-12">

                                {!! Form::open([
                                                    'url' => "/admin/add-subcontractor",
                                                    'method' => 'POST',
                                                    'class' => 'form',
                                                    'id' => 'subcontractorInsertForm',
                                                    'role' => 'form'
                                                    ])!!}
                                @include('landing._subcontractor-insert-form')


                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-flat btn-primary">Alt Yüklenici Ekle</button>

                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>
                    </div>

                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_2">
                        @include('landing._insert-new-staff')
                    </div>
                    <!-- /.tab-pane -->
                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_man">
                        @include('landing._insert-new-manufacturing')
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        @include('landing._insert-new-dept')
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_stock">
                        @include('landing._insert-new-stock')
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_mat">
                        @include('landing._insert-new-material')
                    </div>

                    <div class="tab-pane" id="tab_submat">
                        @include('landing._insert-new-submat')
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4">
                        @include('landing._insert-new-equipment')
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_tag">
                        @include('landing._insert-new-tag')
                    </div>

                    <!-- /.tab-pane -->

                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_exp">
                        @include('landing._insert-new-expenditure')
                    </div>
                    <!-- /.tab-pane -->

                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </div>

@stop
