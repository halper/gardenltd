<div class="row">
    <div class="col-xs-12 col-md-12">

        <div class="row">
            <div class="col-md-12">
                <div class="row">


                    <div class="col-md-1">
                        <label for="name">Faaliyet Alanı Adı: </label>
                    </div>

                    <div class="col-md-4">

                        <input type="text" class="form-control"
                               name="manufacturing" ng-model="manufacturing_name"
                               value="" autocomplete="off"
                               placeholder="Yeni faaliyet alanı giriniz"/>

                    </div>


                    <div class="col-xs-12 col-md-2 ">
                        <button type="button" ng-click="addManufacturing()"
                                class="btn btn-primary btn-flat btn-block">Ekle
                        </button>
                    </div>
                    <div class="col-md-3" ng-show="newMan">
                        <span class="text-success"><%newMan%> faaliyet alanları arasına eklendi</span>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3" ng-show="manError != ''">
                <span class="text-danger"><%manError%></span>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <h3>Mevcut Faaliyet Alanları</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"
                 ng-repeat="man in manufacturings | searchFor:manufacturing_name track by $index">
                <span><% man | trUp %></span>
            </div>
        </div>


    </div>
</div>