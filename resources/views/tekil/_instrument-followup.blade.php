@if($post_permission)
    <p class="text-danger alert-danger" ng-show="subError"><%subError%></p>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <strong>Tarih</strong>
            </div>
            <div class="col-md-4">
                <strong>Firma</strong>
            </div>
            <div class="col-md-4">
                <strong>İş Makinesi</strong>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group input-append date dateRangePicker" id="paymentDate">
                    <input type="text" class="form-control" name="payment_date"
                           ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                </div>
            </div>

            <div class="col-md-4">
                <input type="text" class="form-control" name="firm"
                       ng-model="firm"
                       placeholder="Firma"/>
            </div>

            <div class="col-md-4">
                <select class="form-control"
                        ng-options="equipment as equipment.name for equipment in siteEquipments track by equipment.id"
                        ng-model="siteEquipmentSelected">
                    <option value="" selected disabled>İş Makinesi</option>
                </select>
            </div>

        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <strong>Plaka</strong>
            </div>
            <div class="col-md-4">
                <strong>Yakıt Durumu</strong>
            </div>
            <div class="col-md-4">
                <strong>Yakıt</strong>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control" name="plate"
                       ng-model="plate"
                       placeholder="Plaka"/>

            </div>
            <div class="col-md-4">

                <select class="form-control"
                        ng-model="fuelStatSelect">
                    <option value="" selected disabled>Yakıt Durumu</option>
                    <option value="0">Hariç</option>
                    <option value="1">Dahil</option>
                </select>

            </div>

            <div class="col-md-4">
                <input type="text" class="form-control number" name="fuel"
                       ng-model="fuel"
                       placeholder="Yakıt"/>

            </div>

        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <strong>Birim Çalışma</strong>
            </div>
            <div class="col-md-4">
                <strong>Birim</strong>
            </div>
            <div class="col-md-4">
                <strong>Ücret</strong>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control number" name="work"
                       ng-model="work"
                       placeholder="Birim Çalışma"/>

            </div>

            <div class="col-md-4">
                <input type="text" class="form-control" name="unit"
                       ng-model="unit"
                       placeholder="Birim"/>

            </div>
            <div class="col-md-4">
                <input type="text" class="form-control number" name="fee"
                       ng-model="fee"
                       placeholder="Ücret"/>

            </div>

        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-1 col-md-1">
                <strong>Açıklama: </strong>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control" name="detail"
                       ng-model="detail"
                       placeholder="Açıklama"/>
            </div>

        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-offset-2 col-md-8">
                <button type="button" class="btn btn-primary btn-flat btn-block"
                        ng-click="addInstrument()">Ekle
                </button>
            </div>
        </div>
    </div>

    <div class="form-group" ng-hide="!instruments">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-6">
                <div class="input-group" style="padding-top: 6px;">
                    <input type="text" style="width: 100%"
                           name="search" ng-model="name"
                           value=""
                           placeholder="Tarih, firma, iş makinesi, plaka veya açıklama giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                </div>
            </div>
        </div>

    </div>

    <div class="row" ng-hide="!instruments">
        <div class="col-sm-12">
            <table class="table table-responsive table-extra-condensed dark-bordered">
                <thead>
                <tr>
                    <th class="text-center">S.N</th>
                    <th class="text-center">Tarih</th>
                    <th class="text-center">Firma</th>
                    <th class="text-center">İş Makinesi</th>
                    <th class="text-center">Plaka</th>
                    <th class="text-center">Yakıt Durumu</th>
                    <th class="text-center">Yakıt</th>
                    <th class="text-center">Birim Çalışma</th>
                    <th class="text-center">Birim</th>
                    <th class="text-center">Ücret</th>
                    <th class="text-center">Toplam</th>
                    <th class="text-center">Açıklama</th>
                    @if($post_permission)
                        <th class="text-center">Sil</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="instrument in instruments | searchFor:name:this track by $index">
                    <td class="text-center"><%$index+1%></td>
                    <td class="text-center"><%instrument.date%></td>
                    <td class="text-center"><%instrument.firm%></td>
                    <td class="text-center"><%instrument.name%></td>
                    <td class="text-center"><%instrument.plate%></td>
                    <td class="text-center"><%instrument.fuel_stat | fuelStatConverter%></td>
                    <td class="text-center"><%instrument.fuel%></td>
                    <td class="text-center"><%instrument.work | numberFormatter%></td>
                    <td class="text-center"><%instrument.unit%></td>
                    <td class="text-right"><%instrument.fee |numberFormatter%> TL</td>
                    <td class="text-right"><%instrument.total|numberFormatter%> TL</td>
                    <td class="text-center"><%instrument.detail%></td>
                    @if($post_permission)
                        <td class="text-center"><a href="#!" ng-click="remove_field(instrument)"><i
                                        class="fa fa-close"></i></a></td>
                    @endif
                </tr>
                <tr class="bg-warning">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right"><strong>GENEL TOPLAM</strong></td>
                    <td></td>
                    <td></td>

                    <td style="text-align: right"><strong><%total | numberFormatter%>TL</strong>
                    </td>
                    <td></td>
                    @if($post_permission)
                        <td></td>
                    @endif
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endif