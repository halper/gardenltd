@extends('tekil.layout')

@section('page-specific-css')
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>
    <link href="<?= URL::to('/'); ?>/css/dropzone.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/lightbox.css" rel="stylesheet"/>

@stop

@section('page-specific-js')
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/dropzone.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/bootstrap-datepicker.tr.js" charset="UTF-8"></script>
    <script src="<?= URL::to('/'); ?>/js/lightbox.js" type="text/javascript"></script>

    <script>
        function removeFiles(fid) {
            $.ajax({
                type: 'POST',
                url: '{{"/tekil/$site->slug/delete-subcontractor-files"}}',
                data: {
                    "fileid": fid
                }
            }).success(function () {
                var linkID = "lb-link-" + fid;
                $('#' + linkID).remove();
            });

        }

        $(document).delegate('*[data-toggle="lightbox"]', 'click', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox();
        });

        Dropzone.options.fileInsertForm = {
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, response) {
                    file.serverId = response.id;
                });
                this.on("removedfile", function (file) {
                    var name = file.name;

                    $.ajax({
                        type: 'POST',
                        url: '{{"/tekil/$site->slug/delete-subcontractor-files"}}',
                        data: {
                            "fileid": file.serverId
                        }
                    });
                });
            }
        };

        $(".js-example-basic-multiple").select2({
            placeholder: "Çoklu seçim yapabilirsiniz",
            allowClear: true
        });
        $('.dateRangePicker').datepicker({
            language: 'tr',
            autoclose: true
        });
    </script>
@stop

@section('content')
    <h2>{{$subcontractor->subdetail->name}}</h2>
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Alt Yüklenici Sözleşme Bilgileri</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Ücretler ve Oranlar</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Ek Ödemeler</a></li>
                    <li><a href="#tab_4" data-toggle="tab">Yemek Ücretleri</a></li>
                    <li><a href="#tab_5" data-toggle="tab">Ek Belgeler</a></li>
                    <li><a href="#tab_6" data-toggle="tab">Personel Ekle</a></li>

                </ul>
                <div class="tab-content">
                    <!-- /.tab-pane -->
                    <div class="tab-pane active" id="tab_1">

                        {!! Form::model($subcontractor, [
                                                                        'url' => "/tekil/$site->slug/update-subcontractor",
                                                                        'method' => 'POST',
                                                                        'class' => 'form .form-horizontal',
                                                                        'id' => 'subcontractorEditForm',
                                                                        'role' => 'form',
                                                                        'files' => true
                                                                        ])!!}
                        {!! Form::hidden('sub-id', $subcontractor->id) !!}
                        @include('tekil._subcontractor-form')

                        <div class="row">
                            <div class="col-sm-2"><strong>Sözleşme: </strong></div>
                            <div class="col-sm-10">
                                <?php
                                $my_path = '';
                                $file_name = '';

                                if (!empty($subcontractor->contract)) {
                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $subcontractor->contract->first()->file->first()->path);
                                    $file_name = $subcontractor->contract->first()->file->first()->name;
                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                }
                                ?>
                                <a href="{{!empty($my_path) ? $my_path : ""}}">
                                    {{!empty($file_name) ? $file_name : ""}}
                                </a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 col-md-offset-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-flat btn-primary btn-block">Sözleşme
                                        Detaylarını
                                        Kaydet
                                    </button>
                                </div>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                    {{--Tab pane--}}


                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-fee-form')
                            </div>
                        </div>
                    </div>


                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-cost-form')
                            </div>
                        </div>
                    </div>


                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-meal-form')
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_5">
                        <div class="row">
                            <div class="col-xs-12">
                                @include('tekil._subcontractor-files')
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="tab_6">
                        <div class="row">
                            <div class="col-xs-12">
                                {!! Form::model($subcontractor, [
                                                                        'url' => "/tekil/$site->slug/add-subcontractor-personnel",
                                                                        'method' => 'POST',
                                                                        'class' => 'form form-horizontal',
                                                                        'id' => 'subcontractorPersonnelForm',
                                                                        'role' => 'form'
                                                                        ])!!}
                                @include('landing._personnel-insert-form')
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    @if(sizeof($costs)>0)
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Yapılan Ödemeler Tablosu
                        </h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>TARİH</th>
                                    <th>MALZEME</th>
                                    <th>AKARYAKIT</th>
                                    <th>İŞÇİLİK</th>
                                    <th>İŞ MAKİNASI</th>
                                    <th>TEMİZLİK</th>
                                    <th>AÇIKLAMA</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($costs as $cost)
                                    <tr>
                                        <td>{{ \App\Library\CarbonHelper::getTurkishDate($cost->pay_date) }}</td>
                                        <td>{{ $cost->material }} TL</td>
                                        <td>{{ $cost->oil }} TL</td>
                                        <td>{{ $cost->labour }} TL</td>
                                        <td>{{ $cost->equipment }} TL</td>
                                        <td>{{ $cost->cleaning }} TL</td>
                                        <td>{{ $cost->explanation }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {!! $costs->render() !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop