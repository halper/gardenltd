<?php

$personnel = \App\Personnel::all();
$subcontractor = \App\Subdetail::all();
$equipments = \App\Equipment::all();
$tags = \App\Tag::all();
$materials = \App\Material::all();
$departments = \App\Department::all();
$staff = \App\Staff::allStaff();

$user = Auth::user();
$can_add_personnel = false;
$can_add_subcontractor = false;
$can_add_staff = false;
$can_add_manufacturing = false;
$can_add_department = false;
$can_add_stock = false;
$can_add_material = false;
$can_add_submaterial = false;
$can_add_equipment = false;
$can_add_employer_docs = false;


if ($user->isAdmin()) {
    $can_add_personnel = true;
    $can_add_subcontractor = true;
    $can_add_staff = true;
    $can_add_manufacturing = true;
    $can_add_department = true;
    $can_add_stock = true;
    $can_add_material = true;
    $can_add_submaterial = true;
    $can_add_equipment = true;
    $can_add_employer_docs = true;
} else
    foreach ($user->group()->get() as $group) {
        if ($group->hasSpecialPermissionForSlug('personel')) {
            $can_add_personnel = true;
        }
        if ($group->hasSpecialPermissionForSlug('alt-yuklenici')) {
            $can_add_subcontractor = true;
        }
        if ($group->hasSpecialPermissionForSlug('is-kolu')) {
            $can_add_staff = true;
        }
        if ($group->hasSpecialPermissionForSlug('faaliyet-alani')) {
            $can_add_manufacturing = true;
        }
        if ($group->hasSpecialPermissionForSlug('departman')) {
            $can_add_department = true;
        }
        if ($group->hasSpecialPermissionForSlug('demirbas')) {
            $can_add_stock = true;
        }
        if ($group->hasSpecialPermissionForSlug('malzeme-malzeme-talep')) {
            $can_add_material = true;
        }
        if ($group->hasSpecialPermissionForSlug('baglantili-malzeme')) {
            $can_add_submaterial = true;
        }
        if ($group->hasSpecialPermissionForSlug('is-makinesi')) {
            $can_add_equipment = true;
        }
        if ($group->hasSpecialPermissionForSlug('ise-giris-belgesi-ekle')) {
            $can_add_employer_docs = true;
        }
    }

?>


@extends('landing.landing')

@section('page-specific-css')
    <link href="<?= URL::to('/'); ?>/css/select2.min.css" rel="stylesheet"/>
    <link href="<?= URL::to('/'); ?>/css/bootstrap-editable.css" rel="stylesheet"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css"/>

@stop

@section('page-specific-js')
    <script src="<?= URL::to('/'); ?>/js/bootstrap-editable.min.js" type="text/javascript"></script>
    <script>
        $.fn.editable.defaults.mode = 'inline';


        $(document).ready(function () {
            $('.inline-edit').editable({
                validate: true
            });
            $('.userDelBut').on('click', function () {
                $.post("<?= URL::to('/'); ?>/guncelle/del-personnel", {
                    id: $(this).data('id')
                }).done(function () {
                    location.reload();
                });
            });
            $('.userUndoBut').on('click', function () {
                $.post("<?= URL::to('/'); ?>/guncelle/undo-personnel", {
                    id: $(this).data('id')
                }).done(function () {
                    location.reload();
                });
            });
        });
        $('.inline-edit').on('save', function (e, params) {
            if (params.newValue.length == 0) {
                $(this).parent().closest('div.col-sm-4').remove();
                $(this).parent().parent().closest('tr').remove();
            }
        });
    </script>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    @if($can_add_personnel)
                        <li class="active"><a href="#tab_5" data-toggle="tab">Personel</a></li>
                    @endif
                    @if($can_add_subcontractor)
                        <li><a href="#tab_1" data-toggle="tab">Alt Yüklenici</a></li>
                    @endif
                    @if($can_add_staff)
                        <li><a href="#tab_2" data-toggle="tab">İş Kolu</a></li>
                    @endif
                    @if($can_add_manufacturing)
                        <li><a href="#tab_man" data-toggle="tab">Faaliyet Alanı</a></li>
                    @endif
                    @if($can_add_department)
                        <li><a href="#tab_3" data-toggle="tab">Departman</a></li>
                    @endif
                    @if($can_add_stock)
                        <li><a href="#tab_stock" data-toggle="tab">Demirbaş</a></li>
                    @endif
                    @if($can_add_material)
                        <li><a href="#tab_mat" data-toggle="tab">Malzeme</a></li>
                    @endif
                    @if($can_add_submaterial)
                        <li><a href="#tab_sub" data-toggle="tab">Bağlantılı Malzeme</a></li>
                    @endif
                    @if($can_add_equipment)
                        <li><a href="#tab_4" data-toggle="tab">İş Makinesi</a></li>
                    @endif
                    <li><a href="#tab_tag" data-toggle="tab">Etiketler</a></li>
                    <li><a href="#tab_exp" data-toggle="tab">Gider Kalemleri</a></li>

                </ul>

                <!-- /.tab-content -->
                <div class="tab-content">
                    @if($can_add_personnel)
                        <div class="tab-pane active" id="tab_5">
                            <div class="row">
                                <div class="col-sm-12 table-responsive">
                                    <table class="table table-striped table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Adı-Soyadı</th>
                                            <th>TCK No</th>
                                            <th>Detay</th>
                                            <th>Çıkış Tarihi</th>
                                            <th class="col-sm-1">Nüfus Cüzdanı</th>
                                            <th class="col-sm-1">İşe Giriş Belgesi</th>
                                            <th class="col-sm-2">Kullanıcı İşlemleri</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($personnel as $per)

                                            <?php
                                            $exit_date = $per->contract()->get()->isEmpty() || (!($per->contract()->get()->isEmpty()) && (strpos($per->contract->exit_date, '0000-00-00') !== false)) ? null : \App\Library\CarbonHelper::getTurkishDate($per->contract->exit_date);
                                            ?>
                                            <tr>
                                                <td>{{ \App\Library\TurkishChar::tr_camel($per->name) }}</td>
                                                <td>{{ $per->tck_no }}</td>
                                                <?php
                                                $pers = '';
                                                if ($per->isSitePersonnel()) {
                                                    $pers = "Garden Personeli";
                                                } else {
                                                    if (count($per->personalize) && count($per->personalize->subdetail)) {
                                                        $pers = $per->personalize->subdetail->name . " Personeli";
                                                    }
                                                }

                                                $id_path = '';
                                                $id_file_name = '';
                                                $iddoc = '-';

                                                if (count($per->iddoc) && count($per->iddoc->file()->get())) {
                                                    $id_path_arr = explode(DIRECTORY_SEPARATOR, $per->iddoc->file()->orderBy('created_at', 'DESC')->first()->path);
                                                    $id_file_name = $per->iddoc->file()->orderBy('created_at', 'DESC')->first()->name;
                                                    $id_path = "/uploads/" . $id_path_arr[sizeof($id_path_arr) - 1] . "/" . $id_file_name;
                                                    $iddoc = '<a href="' . $id_path . '">' . $id_file_name . '</a>';
                                                }


                                                $my_path = '';
                                                $file_name = '';
                                                $cont = '-';

                                                if (count($per->contract) && count($per->contract->file()->get())) {

                                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $per->contract->file()->orderBy('created_at', 'DESC')->first()->path);
                                                    $file_name = $per->contract->file()->orderBy('created_at', 'DESC')->first()->name;

                                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                                    $cont = '<a href="' . $my_path . '">' . $file_name . '</a>';
                                                }
                                                ?>

                                                <td>{{$pers}}</td>
                                                <td>{{$exit_date}}</td>
                                                <td>{!! $iddoc!!}</td>
                                                <td>{!! $cont !!}</td>

                                                <td>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <a href="{{"/guncelle/personel-duzenle/$per->id"}}"
                                                               class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                        </div>
                                                        <div class="col-sm-2 col-sm-offset-2">

                                                            <button type="button"
                                                                    class="btn btn-flat btn-danger btn-sm userDelBut"
                                                                    data-id="{{$per->id}}">Sil
                                                            </button>

                                                        </div>
                                                    </div>

                                                </td>

                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @foreach(\App\Personnel::onlyTrashed()->get() as $trashed_per)

                                            <?php
                                            $exit_date = $trashed_per->contract()->get()->isEmpty() || (!($trashed_per->contract()->get()->isEmpty()) && (strpos($trashed_per->contract->exit_date, '0000-00-00') !== false)) ? null : \App\Library\CarbonHelper::getTurkishDate($trashed_per->contract->exit_date);
                                            ?>
                                            <tr class="text-danger">
                                                <td>{{ \App\Library\TurkishChar::tr_camel($trashed_per->name) }}</td>
                                                <td>{{ $trashed_per->tck_no }}</td>
                                                <?php
                                                $detail = '';
                                                if ($trashed_per->isSitePersonnel()) {
                                                    $detail = "Garden Personeli";
                                                } else {
                                                    if (count($trashed_per->personalize) && count($trashed_per->personalize->subdetail)) {
                                                        $detail = $trashed_per->personalize->subdetail->name . " Personeli";
                                                    }
                                                }

                                                $id_path = '';
                                                $id_file_name = '';
                                                $iddoc = '-';

                                                if (count($trashed_per->iddoc) && count($trashed_per->iddoc->file()->get())) {
                                                    $id_path_arr = explode(DIRECTORY_SEPARATOR, $trashed_per->iddoc->file()->orderBy('created_at', 'DESC')->first()->path);
                                                    $id_file_name = $trashed_per->iddoc->file()->orderBy('created_at', 'DESC')->first()->name;
                                                    $id_path = "/uploads/" . $id_path_arr[sizeof($id_path_arr) - 1] . "/" . $id_file_name;
                                                    $iddoc = '<a href="' . $id_path . '">' . $id_file_name . '</a>';
                                                }


                                                $my_path = '';
                                                $file_name = '';
                                                $cont = '-';

                                                if (count($trashed_per->contract) && count($trashed_per->contract->file()->get())) {

                                                    $my_path_arr = explode(DIRECTORY_SEPARATOR, $trashed_per->contract->file()->orderBy('created_at', 'DESC')->first()->path);
                                                    $file_name = $trashed_per->contract->file()->orderBy('created_at', 'DESC')->first()->name;

                                                    $my_path = "/uploads/" . $my_path_arr[sizeof($my_path_arr) - 1] . "/" . $file_name;
                                                    $cont = '<a href="' . $my_path . '">' . $file_name . '</a>';
                                                }
                                                ?>

                                                <td>{{$detail}} <i>(Silinmiş)</i></td>
                                                <td>{{$exit_date}}</td>
                                                <td>{!! $iddoc!!}</td>
                                                <td>{!! $cont !!}</td>

                                                <td>
                                                    <button type="button"
                                                            class="btn btn-flat btn-primary btn-sm userUndoBut"
                                                            data-id="{{$trashed_per->id}}">Geri Al
                                                    </button>

                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($can_add_subcontractor)
                        <div class="tab-pane" id="tab_1">
                            <div class="row">
                                <div class="col-sm-12 table-responsive">
                                    <table class="table table-striped table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Alt Yüklenici</th>
                                            <th>Firma Yetkilisi</th>
                                            <th>Tel. No</th>
                                            <th>İşlemler</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($subcontractor as $sub)
                                            <tr>
                                                <td>{{ \App\Library\TurkishChar::tr_camel($sub->name) }}</td>
                                                <td>{{ $sub->official }}</td>
                                                <td>{{ $sub->mobilecode->code . " $sub->mobile" }}</td>

                                                <td>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <a href="{{"/guncelle/altyuklenici-duzenle/$sub->id"}}"
                                                               class="btn btn-flat btn-warning btn-sm">Düzenle</a>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <?php
                                                            echo '<button type="button" class="btn btn-flat btn-danger btn-sm subDelBut" data-id="' . $sub->id . '" data-name="' . $sub->name . '" data-toggle="modal" data-target="#deleteSubcontractorConfirm">Sil</button>';
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
                    @endif

                        @if($can_add_staff)
                    {{--tab pane--}}
                    <div class="tab-pane" id="tab_2">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                @foreach($staff as $st)
                                    <div class="col-sm-3">
                                        <a href="#" class="inline-edit" data-type="text"
                                           data-pk="{{$st->id}}"
                                           data-url="/guncelle/modify-staff"
                                           data-title="İş Kolu">{{\App\Library\TurkishChar::tr_up($st->staff)}}</a>
                                    </div>
                                    <div class="col-sm-3">
                                        {{\App\Library\TurkishChar::tr_up($st->department->department)}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                        @endif

                        @if($can_add_manufacturing)
                        {{--tab pane--}}
                    <div class="tab-pane" id="tab_man">
                        <div class="row">
                            @foreach(\App\Manufacturing::all() as $man)
                                <div class="col-sm-3">
                                    <a href="#" class="inline-edit" data-type="text"
                                       data-pk="{{$man->id}}"
                                       data-url="/guncelle/modify-manufacturing"
                                       data-title="İş Kolu">{{\App\Library\TurkishChar::tr_up($man->name)}}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                        @endif
                        @if($can_add_department)
                    <div class="tab-pane" id="tab_3">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                @foreach($departments as $department)
                                    <div class="col-sm-4">
                                        <a href="#" class="inline-edit" name="name" data-type="text"
                                           data-pk="{{$department->id}}"
                                           data-url="/guncelle/modify-department"
                                           data-title="Departman">{{\App\Library\TurkishChar::tr_up($department->department)}}</a>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                            <!-- /.tab-pane -->
                        @endif

                        @if($can_add_stock)
                    <div class="tab-pane" id="tab_stock">
                        <div class="row">
                            <div class="col-sm-12">
                                <p>Demirbaşın adını silerek ilgili demirbaş kaydını kaldırabilirsiniz.</p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Demirbaş</th>
                                    <th>Birim</th>
                                    <th>Miktar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(\App\Stock::all() as $stock)
                                    <tr>
                                        <td>
                                            <a href="#" class="inline-edit" id="stockName" data-type="text"
                                               data-pk="{{$stock->id}}"
                                               data-url="/guncelle/modify-stock"
                                               data-title="Demirbaş">{{$stock->name}}</a>
                                        </td>
                                        <td>
                                            <a href="#" class="inline-edit" id="stockUnit" data-type="text"
                                               data-pk="{{$stock->id}}"
                                               data-url="/guncelle/modify-stock"
                                               data-title="Demirbaş">{{$stock->unit}}</a>
                                        </td>
                                        <td>
                                            <a href="#" class="inline-edit" id="stockTotal" data-type="text"
                                               data-pk="{{$stock->id}}"
                                               data-url="/guncelle/modify-stock"
                                               data-title="Demirbaş">{{$stock->total}}</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                            @endif

                        @if($can_add_material)
                    <div class="tab-pane" id="tab_mat">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                @foreach($materials as $material)
                                    <div class="col-sm-4">
                                        <a href="#" class="inline-edit" name="name" data-type="text"
                                           data-pk="{{$material->id}}"
                                           data-url="/guncelle/modify-material"
                                           data-title="Malzeme">{{\App\Library\TurkishChar::tr_up($material->material)}}</a>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                        @endif

                        @if($can_add_submaterial)
                    <div class="tab-pane" id="tab_sub">
                        <h4>1. Seviye Bağlantılı Malzeme</h4>

                        <div class="row">

                            @foreach(\App\Submaterial::bare()->get() as $sm)
                                <div class="col-sm-3">
                                    <a href="#" class="inline-edit" data-type="text"
                                       data-pk="{{$sm->id}}"
                                       data-url="/guncelle/modify-submaterial"
                                       data-title="1. seviye">{{\App\Library\TurkishChar::tr_up($sm->name)}}</a>
                                </div>
                            @endforeach

                        </div>
                        <h4>2. Seviye Bağlantılı Malzeme</h4>

                        <div class="row">
                            @foreach(\App\Feature::all() as $feat)
                                <div class="col-sm-3">
                                    <a href="#" class="inline-edit" data-type="text"
                                       data-pk="{{$feat->id}}"
                                       data-url="/guncelle/modify-feature"
                                       data-title="Özellik">{{\App\Library\TurkishChar::tr_up($feat->name)}}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                        @endif

                        @if($can_add_equipment)
                    <div class="tab-pane" id="tab_4">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    @foreach($equipments as $equipment)
                                        <div class="col-sm-4">
                                            <a href="#" class="inline-edit" name="name" data-type="text"
                                               data-pk="{{$equipment->id}}"
                                               data-url="/guncelle/modify-equipment"
                                               data-title="İş Makinesi">{{\App\Library\TurkishChar::tr_up($equipment->name)}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                        @endif

                    <div class="tab-pane" id="tab_tag">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    @foreach($tags as $tag)
                                        <div class="col-sm-4">
                                            <a href="#" class="inline-edit" name="name" data-type="text"
                                               data-pk="{{$tag->id}}"
                                               data-url="/guncelle/modify-tag"
                                               data-title="Etiket">{{\App\Library\TurkishChar::tr_up($tag->name)}}</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_exp">
                        <div class="row">
                            <div class="col-xs-12 col-md-12">
                                <div class="row">
                                    @foreach(\App\Expdetail::all() as $exp)
                                        <div class="col-sm-3">
                                            <a href="#" class="inline-edit" data-type="text"
                                               data-pk="{{$exp->id}}"
                                               data-url="/guncelle/modify-expdetail"
                                               data-title="Gider Kalemi">{{\App\Library\TurkishChar::tr_up($exp->name)}}</a>
                                        </div>
                                        <div class="col-sm-3">
                                            <?php
                                            $group = 'GENEL GİDERLER';
                                            switch ($exp->group) {
                                                case(2):
                                                    $group = 'SÖZLEŞME GİDERLERİ';
                                                    break;
                                                case(3):
                                                    $group = 'SARF MALZEME GİDERLERİ';
                                                    break;
                                                case(4):
                                                    $group = 'İNŞAAT MALZEME GİDERLERİ';
                                                    break;
                                                default:
                                                    break;
                                            }

                                            ?>
                                            {{$group}}
                                        </div>
                                    @endforeach
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
                    'url' => '/guncelle/del-subcontractor',
                    'method' => 'POST',
                    'class' => 'form',
                    'id' => 'subDeleteForm',
                    'role' => 'form'
                    ]) !!}
                    <button type="submit" class="btn btn-flat btn-warning">Sil</button>
                    <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">İptal</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>


@stop