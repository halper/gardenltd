<div class="row">
    <div class="col-xs-12 col-md-12">
        <div ng-controller="EquipmentController">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">


                        <div class="col-md-1">
                            <label for="name">Ekipman Adı: </label>
                        </div>
                        <div class="col-md-4">

                            <input type="text" class="form-control"
                                   name="equipment" ng-model="name"
                                   value="" autocomplete="off"/>

                        </div>


                        <div class="col-xs-12 col-md-2 ">
                            <button type="button" ng-click="addEquipment()"
                                    class="btn btn-primary btn-flat btn-block">Ekle
                            </button>
                        </div>
                        <div class="col-md-3" ng-show="newEquipment != ''">
                            <span class="text-success"><%newEquipment%> ekipmanlar arasına eklendi</span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3" ng-show="error != ''">
                    <span class="text-danger"><%error%></span>
                </div>

            </div>
            <br>

            <div class="row">
                <div class="col-md-12">
                    <h3>Mevcut Ekipmanlar</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2"
                     ng-repeat="eq in presentEquipments | filter:(name | trUp) |orderBy:'name' track by $index">
                    <span><% eq.name %></span>
                </div>
            </div>

        </div>
    </div>
</div>