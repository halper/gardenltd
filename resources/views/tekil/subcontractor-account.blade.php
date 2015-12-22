<?php
use App\Library\TurkishChar;

$manufacturing_options = '';

foreach (\App\Manufacturing::all() as $manufacture) {
    $manufacturing_options .= "'<option value=\"$manufacture->id\">" . TurkishChar::tr_up($manufacture->name) . "</option>'+\n";
}

?>


@extends('tekil/layout')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script>


        $(document).ready(function () {
            $(".js-example-basic-multiple").select2({
                placeholder: "Çoklu seçim yapabilirsiniz",
                allowClear: true
            });

            $(".select2-container").width('100%');
            $(".select2-search--inline").width('99%');
        });

        $(document).on("click", ".subcontractorDelBut", function (e) {
            e.preventDefault();
            var mySubcontractorName = $(this).data('name');
            var myP = $('.modal-body .userDel');
            myP.html("<em>" + mySubcontractorName + "</em> adlı alt yükleniciyi silmek istediğinize emin misiniz?");
            $('#deleteSubcontractorConfirm').modal('show');
        });

        $('.dateRangePicker').datepicker({
            language: 'tr',
            autoclose: true
        });


    </script>

@stop
@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Alt Yüklenici Bilgileri Düzenle</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Alt Yüklenici Ekle</a></li>

                </ul>
                <div class="tab-content">

                    <!-- /.tab-pane -->
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-xs-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Alt Yüklenici</th>
                                        <th>Sözleşme Tarihi</th>
                                        <th>Sözleşme Başlangıç</th>
                                        <th>Sözleşme Bitiş</th>
                                        <th>Sözleşme Dosyası</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($site->subcontractor()->get() as $sub)
                                        <?php
                                        $contract_entry_exists = false;
                                        if (!empty($sub->contract()->first()))
                                            $contract_entry_exists = true;
                                        ?>
                                        <tr>
                                            <td>{{ $sub->subdetail->name}}</td>

                                            @if($contract_entry_exists)
                                                <td>{{(strpos($sub->contract->first()->contract_date,"0000-00-00") !== false) ? "Girilmedi" : \App\Library\CarbonHelper::getTurkishDate($sub->contract->first()->contract_date) }}</td>

                                                <td>
                                                    {{strpos($sub->contract->first()->contract_start_date,"0000-00-00") !== false ? "Girilmedi" : \App\Library\CarbonHelper::getTurkishDate($sub->contract->first()->contract_start_date)}}
                                                </td>
                                                <td>{{strpos($sub->contract->first()->contract_end_date,"0000-00-00") !== false ? "Girilmedi" : \App\Library\CarbonHelper::getTurkishDate($sub->contract->first()->contract_end_date)}}</td>
                                                <td>
                                                    <?php
                                                    $my_path = '';
                                                    $file_name = '';


                                                    if (!is_null($sub->contract->first())) {
                                                        $my_path_arr = explode(DIRECTORY_SEPARATOR, $sub->contract->first()->file->first()->path);
                                                        $file_name = $sub->contract->first()->file->first()->name;
                                                        $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                                    }
                                                    ?>
                                                    <a href="{{!empty($my_path) ? $my_path : ""}}">
                                                        {{!empty($file_name) ? $file_name : ""}}
                                                    </a></td>
                                            @else
                                                <td>Girilmedi</td>
                                                <td>Girilmedi</td>
                                                <td>Girilmedi</td>
                                                <td>Girilmedi</td>
                                            @endif
                                            <td>

                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <a href="{{"alt-yuklenici-duzenle/$sub->id"}}"
                                                           class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                    </div>
                                                    <div class="col-sm-2 col-sm-offset-1">
                                                        <?php
                                                        echo '<button type="button" class="btn btn-flat btn-danger btn-sm subcontractorDelBut" data-id="' . $sub->id . '" data-name="' . $sub->subdetail->name . '" data-toggle="modal" data-target="#deleteSubcontractorConfirm">Sil</button>';
                                                        ?>
                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-sm-12">

                                <div class="row">
                                    {!! Form::open([
                                                    'url' => "/tekil/$site->slug/add-subcontractor",
                                                    'method' => 'POST',
                                                    'class' => 'form form-horizontal',
                                                    'id' => 'subcontractorInsertForm',
                                                    'role' => 'form',
                                                    ])!!}


                                    @foreach(App\Subdetail::all() as $subcontractor)

                                        @if(!is_null($site->subcontractor()->onlyTrashed()->where('subdetail_id', $subcontractor->id)->first()))
                                            <div class="col-md-4 col-xs-6">
                                                <label class="checkbox-inline">
                                                    {!! Form::checkbox('subcontractors[]', $site->subcontractor()->onlyTrashed()->where('subdetail_id', $subcontractor->id)->first()->id, null,
                                                    [
                                                    'id'=>$site->subcontractor()->onlyTrashed()->where('subdetail_id', $subcontractor->id)->first()->id
                                                    ])
                                                    !!}{{ $subcontractor->name}}</label>
                                            </div>
                                        @elseif(!$site->hasSubcontractor($subcontractor->id))
                                            <div class="col-md-4 col-xs-6">
                                                <label class="checkbox-inline">
                                                    {!! Form::checkbox('subcontractors[]', $subcontractor->id, $site->hasSubcontractor($subcontractor->id),
                                                    [
                                                    'id'=>$subcontractor->id
                                                    ])
                                                    !!}{{ $subcontractor->name}}</label>
                                            </div>
                                        @endif

                                    @endforeach
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-md-4 col-md-offset-3">
                                        <div class="form-group">
                                            <br>
                                            <br>
                                            <button type="submit" class="btn btn-primary btn-flat btn-block">
                                                Güncelle
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>


                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
            </div>
        </div>
    </div>



    <div id="deleteSubcontractorConfirm" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Alt Yüklenici Sil</h4>
                </div>
                <div class="modal-body">
                    <p class="userDel"></p>
                </div>
                <div class="modal-footer">
                    {!! Form::open([
                    'url' => "/tekil/$site->slug/del-subcontractor",
                    'method' => 'PATCH',
                    'class' => 'form',
                    'id' => 'subcontractorDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-danger btn-flat">Sil</button>
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>



@stop