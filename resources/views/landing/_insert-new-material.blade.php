<div class="row">
    <div class="col-xs-12 col-md-12">

        <div class="row">
            <div class="col-md-12">
                <div class="row">


                    <div class="col-md-1">
                        <label for="name">Malzeme Adı: </label>
                    </div>
                    <div class="col-md-4">

                        <input type="text" class="form-control"
                               name="material" ng-model="material_name"
                               value="" autocomplete="off"
                               placeholder="Yeni malzeme giriniz"/>

                    </div>


                    <div class="col-xs-12 col-md-2 ">
                        <button type="button" ng-click="addMaterial()"
                                class="btn btn-primary btn-flat btn-block">Ekle
                        </button>
                    </div>
                    <div class="col-md-3" ng-show="newMat != ''">
                        <span class="text-success"><%newMat%> malzemeleriniz arasına eklendi</span>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3" ng-show="matError != ''">
                <span class="text-danger"><%matError%></span>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <h3>Mevcut Malzemeler</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"
                 ng-repeat="mat in materials | filter:(material_name | trUp) track by $index">
                <span><% mat.material | trUp %></span>
            </div>
        </div>


    </div>
</div>