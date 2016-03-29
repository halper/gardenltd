<div class="row">
    <div class="col-xs-12 col-md-12">

        <div class="row">
            <div class="col-md-12">
                <div class="row">


                    <div class="col-md-1">
                        <label for="name">Gider Adı: </label>
                    </div>
                    <div class="col-md-4">

                        <input type="text" class="form-control"
                               name="type" ng-model="expName"
                               value="" autocomplete="off"
                               placeholder="Yeni gider türü giriniz"/>

                    </div>

                    <div class="col-md-3">

                        <select class="form-control" name="group" ng-model="expGroup">
                            <option value="" selected disabled>Gider Türü seçiniz</option>
                            <option value="1">GENEL GİDERLER</option>
                            <option value="2">SÖZLEŞME GİDERLERİ</option>
                            <option value="3">SARF MALZEME GİDERLERİ</option>
                            <option value="4">İNŞAAT MALZEME GİDERLERİ</option>
                        </select>

                    </div>


                    <div class="col-xs-12 col-md-2 ">
                        <button type="button" ng-click="addExpenditure()"
                                class="btn btn-primary btn-flat btn-block">Ekle
                        </button>
                    </div>

                    <div class="col-md-3" ng-show="newExp != ''">
                        <span class="text-success"><%newExp%> giderler arasına eklendi</span>
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
                <h3>Mevcut Giderler</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"
                 ng-repeat="exp in expenditures | filter:(expName | trUp) track by $index">
                <span><% exp.name | trUp %> (<%exp.group%>)</span>
            </div>
        </div>
    </div>
</div>