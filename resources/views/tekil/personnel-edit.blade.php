<?php

$staff_options = '<option></option>';
$management_depts = new \App\Department();

foreach ($management_depts->management() as $dept) {
    $staff_options .= "<optgroup label=\"$dept->department\">";
    foreach ($dept->staff()->notGarden()->get() as $staff) {
        if ((int)$personnel->staff->id == (int)$staff->id)
            $staff_options .= "<option value=\"$staff->id\" selected>" . \App\Library\TurkishChar::tr_up($staff->staff) . "</option>";
        else
            $staff_options .= "<option value=\"$staff->id\">" . \App\Library\TurkishChar::tr_up($staff->staff) . "</option>";
    }
}
$wage = $personnel->wage()->get()->isEmpty() ? null : \App\Library\TurkishChar::convertToTRcurrency($personnel->wage()->orderBy('since', 'DESC')->first()->wage);
$exit_date = $personnel->contract()->get()->isEmpty() || (!($personnel->contract()->get()->isEmpty()) && (strpos($personnel->contract->exit_date, '0000-00-00') !== false)) ? null : \App\Library\CarbonHelper::getTurkishDate($personnel->contract->exit_date);
?>

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>

    <script>
        $('#dateRangePicker').datepicker({
            autoclose: true,
            language: 'tr'
        });
        $("#dateRangePicker > input").val(moment().format('DD.MM.YYYY'));
        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.data = null;
            $scope.pid = '{{$personnel->id}}';
            $scope.subError = '';
            $scope.date = moment().format('DD.MM.YYYY');
            $scope.wages = [];

            $scope.getWages = function () {
                $http.post("{{url("/tekil/$site->slug/retrieve-wages")}}", {
                    pid: $scope.pid
                }).then(function (response) {
                    $scope.wages = response.data
                });
            };

            $scope.getWages();


            $scope.addWage = function () {

                if (!$scope.date || !$scope.dailyWage) {
                    $scope.subError = 'Lütfen ilgili alanları doldurunuz: Tarih, bağlantı malzeme, fiyat!'
                }
                else {
                    $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/add-wage", {
                        since: $scope.date,
                        wage: $scope.dailyWage,
                        pid: $scope.pid
                    }).then(function (response) {
                        $scope.dailyWage = '';
                        $scope.date = moment().format('DD.MM.YYYY');
                        $scope.subError = '';
                        $scope.getWages();
                    });
                }

            };
            $scope.remove_field = function (item) {
                $http.post("<?=URL::to('/');?>/tekil/{{$site->slug}}/del-wage", {
                    id: item.id
                }).then(function () {
                    $scope.getWages();
                    if (item.since === "İlk değer") {
                        $scope.date = '01.01.1970';
                    } else {
                        $scope.date = item.since;
                    }
                    $scope.dailyWage = item.wage;
                });

            }
        }).filter('numberFormatter', function () {
            return function (data) {
                return $.number(data, 2, ',', '.');
            }
        });
        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

        function removeFiles(fid) {
            $.ajax({
                type: 'POST',
                url: '{{"/admin/delete-personnel-files"}}',
                data: {
                    "fileid": fid
                }
            }).success(function () {
                var linkID = "lb-link-" + fid;
                $('#' + linkID).remove();
            });

        }
    </script>
@stop

@extends('tekil.layout')

@section('content')

    <h2 class="page-header">
        {{\App\Library\TurkishChar::tr_camel($personnel->name)}}
    </h2>

    {!! Form::open([
    'url' => "/tekil/$site->slug/modify-personnel",
    'method' => 'POST',
    'class' => 'form',
    'id' => 'personnelModifyForm',
    'role' => 'form',
    'files' => true
    ])!!}

    {!! Form::hidden('id', $personnel->id) !!}

    <div class="form-group {{ $errors->has('tck_no') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('tck_no', 'TCK No:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::number('tck_no', $personnel->tck_no, ['class' => 'form-control', 'placeholder' => 'TCK no.su giriniz']) !!}

            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('name', 'Personelin Adı:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::text('name', \App\Library\TurkishChar::tr_camel($personnel->name), ['class' => 'form-control', 'placeholder' => 'Ad soyad giriniz']) !!}

            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('iban') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('iban', 'IBAN No: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::text('iban', $personnel->iban, ['class' => 'form-control', 'placeholder' => 'Personelin IBAN no.sunu giriniz']) !!}

            </div>
        </div>
    </div>


    <div class="form-group {{ $errors->has('exit_date') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('exit_date', 'Çıkış Tarihi: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                <div class="input-group input-append date dateRangePicker">
                    <input type="text" class="form-control" name="exit_date"
                           placeholder="Çıkış tarihi seçiniz" value="{{$exit_date}}"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group {{ $errors->has('staff_id') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('staff_id', 'İş Kolu: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-6">
                <select name="staff_id" class="staff-select form-control">
                    {!! $staff_options !!}
                </select>
            </div>
        </div>
    </div>

    @if(empty($wage))
        <div class="form-group {{ $errors->has('wage') ? 'has-error' : '' }}">
            <div class="row">
                <div class="col-sm-2">
                    {!! Form::label('wage', 'Günlük Ücret: ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm-11">
                            {!! Form::text('wage', str_replace('.', ',', $wage), ['class' => 'form-control number', 'placeholder' => 'Personelin günlük ücretini giriniz']) !!}
                        </div>
                        <div class="col-sm-1">
                            <span class="text-left">TL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="form-group {{ $errors->has('iddoc') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('iddoc', 'Nüfus Cüzdanı:* ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                <input type="file" name="iddoc" id="idToUpload">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-sm-2"><strong>Mevcut Nüfus Belgesi: </strong></div>
            <div class="col-sm-10">
                <?php
                $id_path = '';
                $id_file_name = '';

                if (count($personnel->iddoc) && count($personnel->iddoc->file()->get())) {
                    $id_path_arr = explode(DIRECTORY_SEPARATOR, $personnel->iddoc->file()->orderBy('created_at', 'DESC')->first()->path);
                    $id_file_name = $personnel->iddoc->file()->orderBy('created_at', 'DESC')->first()->name;
                    $id_path = "/uploads/" . $id_path_arr[sizeof($id_path_arr) - 1] . "/" . $id_file_name;
                }
                ?>
                <a href="{{!empty($id_path) ? $id_path : ""}}">
                    {{!empty($id_file_name) ? $id_file_name : ""}}
                </a>
            </div>
        </div>
    </div>

    @if(count($personnel->iddoc))
        <div class="form-group {{ $errors->has('contract') ? 'has-error' : '' }}">
            <div class="row">
                <div class="col-sm-2">
                    {!! Form::label('contract', 'İşe Giriş Belgesi:* ', ['class' => 'control-label']) !!}
                </div>
                <div class="col-sm-10">
                    <input type="file" name="contract" id="contractToUpload">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-sm-2"><strong>Mevcut Giriş Belgesi: </strong></div>
                <div class="col-sm-10">
                    <?php
                    $my_path = '';
                    $file_name = '';

                    if (count($personnel->contract) && count($personnel->contract->file()->get())) {
                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $personnel->contract->file()->orderBy('created_at', 'DESC')->first()->path);
                        $file_name = $personnel->contract->file()->orderBy('created_at', 'DESC')->first()->name;
                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                    }
                    ?>
                    <a href="{{!empty($my_path) ? $my_path : ""}}">
                        {{!empty($file_name) ? $file_name : ""}}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="form-group">
        <div class="row">
            <div class="col-sm-2">
                {!! Form::label('documents', 'Ek Belgeler: ', ['class' => 'control-label']) !!}
            </div>
            <div class="col-sm-10">
                <input type="file" name="documents[]" id="documentsToUpload" multiple>
            </div>
        </div>
    </div>

    @if(!empty($personnel->photo->first()))
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12">
                    <h4>Kayıtlı Belgeler</h4>
                </div>
            </div>
            @foreach($personnel->photo as $photo)
                <?php
                $my_path_arr = explode(DIRECTORY_SEPARATOR, $photo->file->path);
                $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1];
                if (strpos($photo->file->name, 'pdf') !== false) {
                    $image = URL::to('/') . "/img/pdf.jpg";
                } elseif (strpos($photo->file->name, 'doc') !== false) {
                    $image = URL::to('/') . "/img/word.png";
                } else {
                    $image = URL::to('/') . $my_path . DIRECTORY_SEPARATOR . $photo->file->name;
                }
                ?>

                <a id="lb-link-{{$photo->id}}" href="{{$image}}"
                   data-toggle="lightbox" data-gallery="personnel-photos"
                   data-title="{{$photo->file->name}}"
                   data-footer="<a data-dismiss='modal' class='remove-files' href='#' onclick='removeFiles({{$photo->id}})'>Dosyayı Sil<a/>"
                   class="col-sm-4">
                    <img src="{{$image}}" class="img-responsive" style="height: 45px">
                    {{$photo->file->name}}
                </a>

            @endforeach
        </div>
    @endif

    <div class="form-group">
        <div class="row">
            <div class="col-sm-offset-4 col-sm-4">
                <button type="submit" class="btn btn-flat btn-primary btn-block" id="add-personnel">Personel
                    Güncelle
                </button>
            </div>
        </div>
    </div>

    <div ng-app="puantajApp" ng-controller="PuantajController">

        <div class="form-group">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-append date " id="dateRangePicker">
                        <input type="text" class="form-control" name="exp_date" ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>

                <div class="col-md-4">
                    <input type="text" class="form-control number" name="price" ng-model="dailyWage"
                           placeholder="Günlük ücret"/>
                </div>


                <div class="col-md-2">
                    <button type="button" class="btn btn-primary btn-flat btn-block" ng-click="addWage()">Ekle
                    </button>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <table class="table table-responsive table-extra-condensed dark-bordered">
                    <thead>
                    <tr style="font-size: smaller">
                        <th class="text-center">Tarihi İtibariyle</th>
                        <th class="text-right">Günlük Ücret</th>
                        <th class="text-center">Sil</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="wage in wages track by $index">
                        <td><%wage.since%></td>
                        <td class="text-right"><%wage.wage|numberFormatter%></td>
                        <td class="text-center"><a href="#" ng-click="remove_field(wage)"><i
                                        class="fa fa-close"></i></a>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop

