@if($site->smdemand->count() > 0)
    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
        <div class="form-group">
            <div class="row">
            <div class="col-sm-12">
                <select class="form-control" ng-options="mat as mat.matName for mat in materials track by mat.id"
                        ng-model="selected"
                        ng-change="init()">
                    <option value="" selected disabled>Malzeme seçiniz</option>
                </select>
            </div>
        </div>
        </div>
        <div ng-hide="!showRest">
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-4">
                        <div class="input-group">
                            <input type="text" style="width: 100%"
                                   name="search" ng-model="name"
                                   value=""
                                   placeholder="Tarih, irsaliye no veya malzeme cinsi giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4>Yeni Harcama Ekle</h4>

                    <p class="text-danger"><%subError%></p>


                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="input-group input-append date " id="dateRangePicker">
                                    <input type="text" class="form-control" name="exp_date" ng-model="date"/>
                                        <span class="input-group-addon add-on"><span
                                                    class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <select class="form-control"
                                        ng-options="submat as submat.name for submat in submaterials track by submat.id"
                                        ng-model="submatSelected"
                                        ng-change="drChange()">
                                    <option value="" selected disabled>Bağlantı Malzeme</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" class="form-control"
                                       name="no" ng-model="bill[0]"
                                       value=""
                                       placeholder="İrsaliye No"/>
                            </div>

                            <div class="col-md-2">
                                <input type="text" class="form-control number"
                                       name="quantity" ng-model="quantity[0]"
                                       value=""
                                       placeholder="Miktar"/>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control"
                                       name="detail" ng-model="detail[0]"
                                       value=""
                                       placeholder="Açıklama"/>
                            </div>
                        </div>
                    </div>


                    <div add-input>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="ordered" ng-model="orderedIrsaliye"> İrsaliyeleri sıralı ekle
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" ng-click="addExpense()"
                                            class="btn btn-primary btn-flat btn-block btn-sm">Kaydet
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                            class="btn btn-success btn-flat btn-block btn-sm">Satır Ekle
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <input style="width: 100%;" type="number" step="1" ng-model="inputSize" placeholder="Ek satır">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-responsive table-extra-condensed dark-bordered">
                        <thead>
                        <tr style="font-size: smaller">
                            <th></th>
                            <th ng-repeat="submat in submaterials track by $index" class="text-right"><%submat.name%>
                                (<%submat.price|numberFormatter%>)
                            </th>
                            <th class="text-right">SÖZLEŞME TUTARI: <%contract_cost|numberFormatter%></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-right">TOPLAM BAĞLANTIDAN KALAN</td>
                            <td class="text-right" ng-repeat="submat in submaterials track by $index"><%submat.quantity|numberFormatter%>
                            </td>
                            <td class="text-right"><%remaining|numberFormatter%></td>
                        </tr>
                        <tr>
                            <td class="text-right">TOPLAM HARCAMA</td>
                            <td class="text-right" ng-repeat="subspent in submatSpent track by $index">
                                <%subspent|numberFormatter%>
                            </td>
                            <td class="text-right"><%total|numberFormatter%></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-primary">
                        <div class="box-body">
                            <!-- Loading (remove the following to stop the loading)-->
                            <div class="overlay" ng-show="loading">
                                <i class="fa fa-refresh fa-spin"></i>
                            </div>
                            <!-- end loading -->


                            <div ng-hide="loading">
                                <div class="row">

                                    <div class="col-md-12" style="overflow: auto">
                                        <table class="table table-responsive table-extra-condensed dark-bordered">
                                            <thead>
                                            <tr style="font-size: smaller">
                                                <th>SIRA</th>
                                                <th>SEVK TARİHİ</th>
                                                <th>İRSALİYE NO</th>
                                                <th>AÇIKLAMA</th>
                                                <th>CİNS</th>
                                                <th class="text-right">MİKTAR</th>
                                                <th class="text-right">HARCAMA</th>
                                                <th class="text-center">SİL</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr ng-repeat="expense in expenses | searchFor:name track by $index">

                                                <td><% $index+1 %></td>
                                                <td><% expense.date%></td>
                                                <td><%expense.bill%></td>
                                                <td><%expense.detail%></td>
                                                <td><%expense.subname%> (<%expense.price | numberFormatter%>)</td>
                                                <td class="text-right"><%expense.quantity|numberFormatter%></td>
                                                <td class="text-right"><%expense.spent|numberFormatter%></td>
                                                <td class="text-center"><a href="#" ng-click="remove_field(expense)"><i
                                                                class="fa fa-close"></i></a>
                                                </td>

                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->

                    </div>

                </div>
            </div>


        </div>
    </div>
@else
    <p>Görüntülenecek talep bulunmamaktadır.</p>
@endif