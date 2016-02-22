<div class="row">
    <div class="col-xs-12 col-md-12">
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Ödemeler Tablosu
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


                <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
                    <p class="text-danger alert-danger" ng-show="subError"><%subError%></p>
                    <div class="alert alert-info alert-dismissible" ng-show="showPayment">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        Ödemelerin %90'ı tamamlanmıştır!
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <strong>Tarih</strong>
                            </div>
                            <div class="col-md-6">
                                <strong>Cins</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Şekil</strong>
                            </div>
                            <div class="col-md-2">
                                <strong>Miktar</strong>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="input-group input-append date dateRangePicker" id="paymentDate">
                                        <input type="text" class="form-control" name="payment_date" ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <select class="form-control"
                                            ng-model="paymentSelect">
                                        <option value="" selected disabled>Ödeme Seçiniz</option>
                                        <option value="Malzeme">GARDEN TARAFINDAN SAĞLANAN MALZEME BEDELİ</option>
                                        <option value="İş Makinası">GARDEN TARAFINDAN SAĞLANAN İŞ MAKİNASI BEDELİ
                                        </option>
                                        <option value="Akaryakıt">GARDEN TARAFINDAN SAĞLANAN AKARYAKIT BEDELİ</option>
                                        <option value="Temizlik">GARDEN TARAFINDAN SAĞLANAN TEMİZLİK BEDELİ</option>
                                        <option value="İşçilik">ALT YÜKLENİCİ ADINA ÇALIŞTIRILAN İŞÇİLİK BEDELİ</option>
                                        <option value="Ek Ödeme">EK ÖDEME</option>
                                        <option value="Diğer">DİĞER</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="type" ng-model="method"
                                           placeholder="Ödeme şekli"/>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control number" name="price" ng-model="amount"
                                           placeholder="Miktar"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    {!! Form::label('detail', 'Açıklama: ', ['class' => 'control-label']) !!}
                                </div>
                                <div class="col-sm-10">
                                    <textarea name="detail" id="payment-detail" class="form-control" rows="3" ng-model="detail"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-offset-2 col-md-8">
                                    <button type="button" class="btn btn-primary btn-flat btn-block"
                                            ng-click="addPayment()">Güncelle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-8 col-md-6 pull-right">
                                <div class="input-group">
                                    <input type="text" style="width: 100%"
                                           name="search" ng-model="name"
                                           value=""
                                           placeholder="Tarih, ödeme cinsi, ödeme şekli veya açıklama giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" ng-hide="!payments">
                        <div class="col-sm-12">
                            <table class="table table-responsive table-extra-condensed dark-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center">Tarih</th>
                                    <th class="text-center">Ödeme Cinsi</th>
                                    <th class="text-center">Ödeme Şekli</th>
                                    <th class="text-center">Açıklama</th>
                                    <th class="text-center">Borç</th>
                                    <th class="text-center">Alacak Tutarı</th>
                                    <th class="text-center">Bakiye</th>
                                    <th class="text-center">Sil</th>
                                </tr>
                                <tr style="font-size: medium">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-right">GENEL TOPLAM</th>
                                    <th class="text-right"><%debt|numberFormatter%></th>
                                    <th class="text-right"><%claim|numberFormatter%></th>
                                    <th class="text-right"><%balance|numberFormatter%></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="payment in payments | searchFor:name track by $index">
                                    <td class="text-center"><%payment.date%></td>
                                    <td class="text-center"><%payment.type%></td>
                                    <td class="text-center"><%payment.method%></td>
                                    <td class="text-left"><%payment.detail%></td>
                                    <td class="text-right"><%payment.debt|numberFormatter%></td>
                                    <td></td>
                                    <td class="text-right"><%payment.balance|numberFormatter%></td>
                                    <td class="text-center"><a href="#!" ng-click="remove_field(payment)"><i
                                                    class="fa fa-close"></i></a>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

