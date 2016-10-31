@extends('tekil.layout')

@section('page-specific-js')

    <script src="<?= URL::to('/'); ?>/js/jquery.flot.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/jquery.flot.resize.min.js" type="text/javascript"></script>
    <script src="<?= URL::to('/'); ?>/js/jquery.flot.pie.min.js" type="text/javascript"></script>

    <script>

        function labelFormatter(label, series) {
            return '<div class="text-center" style="font-size:13px; padding:2px; color: #fff; font-weight: 600;">'
                    + "<br/>"
                    + Math.round(series.percent) + "%</div>";
        }
        function drawChart(donutData) {

            $.plot("#contract-status-chart", donutData, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.5,
                        label: {
                            show: true,
                            radius: 7 / 9,
                            formatter: labelFormatter,
                            threshold: 0.1
                        }

                    }
                },
                legend: {
                    show: true
                }
            });
        }
        $.get("<?=URL::to('/');?>{!! "/tekil/$site->slug/retrieve-summary" !!}", function (data) {
            console.log(data);
            $('.genel-gider').text($.number(data.genel, 2, ',', '.') + ' TL');
            $('.sozlesme-gider').text($.number(data.sozlesme, 2, ',', '.') + ' TL');
            $('.sarf-gider').text($.number(data.sarf, 2, ',', '.') + ' TL');
            $('.insaat-gider').text($.number(data.insaat, 2, ',', '.') + ' TL');
            $('.taseron-gider').text($.number(data.taseron, 2, ',', '.') + ' TL');
            $('.iscilik-gider').text($.number(data.iscilik, 2, ',', '.') + ' TL');
            $('.personel-gider').text($.number(data.personel, 2, ',', '.') + ' TL');
            $('.toplam-gider').text($.number(data.total, 2, ',', '.') + ' TL');
            $('.kdv18-gider').text($.number(data.kdv18, 2, ',', '.') + ' TL');
            $('.kdv8-gider').text($.number(data.kdv8, 2, ',', '.') + ' TL');
            $('.kdv1-gider').text($.number(data.kdv1, 2, ',', '.') + ' TL');
            $('.kdv0-gider').text($.number(data.kdv0, 2, ',', '.') + ' TL');
            $('.genel-toplam').text($.number(data.grand, 2, ',', '.') + ' TL');

            $('.genel-yuzde').text($.number(data.genel_yuzde, 2, ',', '.') + '%');
            $('.sozlesme-yuzde').text($.number(data.sozlesme_yuzde, 2, ',', '.') + '%');
            $('.sarf-yuzde').text($.number(data.sarf_yuzde, 2, ',', '.') + '%');
            $('.insaat-yuzde').text($.number(data.insaat_yuzde, 2, ',', '.') + '%');
            $('.taseron-yuzde').text($.number(data.taseron_yuzde, 2, ',', '.') + '%');
            $('.iscilik-yuzde').text($.number(data.iscilik_yuzde, 2, ',', '.') + '%');
            $('.personel-yuzde').text($.number(data.personel_yuzde, 2, ',', '.') + '%');
            $('.toplam-yuzde').text($.number('100', 2, ',', '.') + '%');

            var donutData = [
                {label: "GENEL GİDERLER", data: data.genel, color: "#006dcc"},
                {label: "SÖZLEŞME GİDERLERİ", data: data.sozlesme, color: "#16a085"},
                {label: "MUHTELİF SARF MALZEME", data: data.sarf, color: "#c0392b"},
                {label: "İNŞAAT ANA MALZEME", data: data.insaat, color: "#2980b9"},
                {label: "TAŞERON HAKEDİŞLERİ", data: data.taseron, color: "#8e44ad"},
                {label: "ŞANTİYE PERSONEL MAAŞ", data: data.personel, color: "#7f8c8d"},
                {label: "ŞANTİYE İŞÇİLİK GİDERLERİ", data: data.iscilik, color: "#f39c12"}

            ];

            drawChart(donutData);

        });
    </script>

@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">İcmal
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
                    <div class="row">
                        <div class="col-sm-5">
                            <table class="table table-responsive table-extra-condensed">
                                <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-left">Gider Kalemi</th>
                                    <th class="text-right">Masraf</th>
                                    <th class="text-right">Yüzde</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr style="background-color: #006dcc; color: #fff">
                                    <td class="text-center">01</td>
                                    <td class="text-left">GENEL GİDERLER</td>
                                    <td class="genel-gider text-right"></td>
                                    <td class="genel-yuzde text-right"></td>
                                </tr>
                                <tr style="background-color: #16a085; color: #fff">
                                    <td class="text-center">02</td>
                                    <td class="text-left">SÖZLEŞME GİDERLERİ</td>
                                    <td class="sozlesme-gider text-right"></td>
                                    <td class="sozlesme-yuzde text-right"></td>
                                </tr>
                                <tr style="background-color: #c0392b; color: #fff">
                                    <td class="text-center">03</td>
                                    <td class="text-left">MUHTELİF SARF MALZEME</td>
                                    <td class="sarf-gider text-right"></td>
                                    <td class="sarf-yuzde text-right"></td>
                                </tr>
                                <tr style="background-color: #2980b9; color: #fff">
                                    <td class="text-center">04</td>
                                    <td class="text-left">İNŞAAT ANA MALZEME</td>
                                    <td class="insaat-gider text-right"></td>
                                    <td class="insaat-yuzde text-right"></td>
                                </tr>
                                <tr style="background-color: #8e44ad; color: #fff">
                                    <td class="text-center">05</td>
                                    <td class="text-left">TAŞERON HAKEDİŞLERİ</td>
                                    <td class="taseron-gider text-right"></td>
                                    <td class="taseron-yuzde text-right"></td>
                                </tr>
                                <tr style="background-color: #7f8c8d; color: #fff">
                                    <td class="text-center">06</td>
                                    <td class="text-left">ŞANTİYE PERSONEL MAAŞ</td>
                                    <td class="personel-gider text-right"></td>
                                    <td class="personel-yuzde text-right"></td>
                                </tr>
                                <tr style="background-color: #f39c12; color: #fff">
                                    <td class="text-center">07</td>
                                    <td class="text-left">ŞANTİYE İŞÇİLİK GİDERLERİ</td>
                                    <td class="iscilik-gider text-right"></td>
                                    <td class="iscilik-yuzde text-right"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td class="text-center"></td>
                                    <td class="text-left"><strong>TOPLAM</strong></td>
                                    <td class="toplam-gider text-right"></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="text-center">08</td>
                                    <td class="text-left">KDV %18</td>
                                    <td class="kdv18-gider text-right"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-center">09</td>
                                    <td class="text-left">KDV %8</td>
                                    <td class="kdv8-gider text-right"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-center">10</td>
                                    <td class="text-left">KDV %1</td>
                                    <td class="kdv1-gider text-right"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-center">11</td>
                                    <td class="text-left">KDV %0</td>
                                    <td class="kdv0-gider text-right"></td>
                                    <td></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td class="text-center"></td>
                                    <td class="text-left"><strong>GENEL MALİYET TOPLAMI</strong></td>
                                    <td class="genel-toplam text-right"></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-7">
                            <div id="contract-status-chart" style="height: 300px; padding: 0px; position: relative;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection