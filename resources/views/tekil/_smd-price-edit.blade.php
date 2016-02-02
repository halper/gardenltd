<h4>Fiyat Güncelle</h4>

<div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
    <p class="text-danger" ng-show="subError"><%subError%></p>

    <div class="form-group">
        <div class="row">
            <div class="col-md-2">
                <strong>Tarihi İtibariyle</strong>
            </div>
            <div class="col-md-8">
                <strong>Bağlantı Malzeme</strong>
            </div>
            <div class="col-md-2">
                <strong>Yeni Fiyat</strong>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <div class="input-group input-append date " id="dateRangePicker">
                        <input type="text" class="form-control" name="exp_date" ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>

                <div class="col-md-8">
                    <select class="form-control"
                            ng-options="submat as submat.name for submat in submaterials track by submat.id"
                            ng-model="submatSelected"
                            ng-change="drChange()">
                        <option value="" selected disabled>Bağlantı Malzeme</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="text" class="form-control number" name="price" ng-model="price"
                           placeholder="Yeni fiyat"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-offset-2 col-md-8">
                    <button type="button" class="btn btn-primary btn-flat btn-block" ng-click="addPrice()">Güncelle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <table class="table table-responsive table-extra-condensed dark-bordered">
                <thead>
                <tr style="font-size: smaller">
                    <th>Bağlantı Malzeme</th>
                    <th class="text-right">Fiyat</th>
                    <th class="text-center">Tarihi İtibariyle</th>
                    <th class="text-center">Sil</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="price in pricesmds track by $index">
                    <td><%price.submat%></td>
                    <td class="text-right"><%price.price|numberFormatter%></td>
                    <td class="text-center"><%price.since%></td>
                    <td class="text-center"><a href="#" ng-click="remove_field(price)"><i class="fa fa-close"></i></a>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>