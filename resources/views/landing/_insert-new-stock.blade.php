<div class="row">
    <div class="col-md-3" ng-show="newMan">
        <span class="text-success"><%newMan%> faaliyet alanları arasına eklendi</span>
    </div>

</div>

<div class="form-group">
    <div class="row">

        <div class="col-md-4">
            <input type="text" class="form-control"
                   name="stock" ng-model="stockName"
                   value="" autocomplete="off"
                   placeholder="Yeni demirbaş adı giriniz"/>

        </div>
        <div class="col-md-4">
            <input type="text" class="form-control"
                   name="stock" ng-model="stockUnit"
                   value="" autocomplete="off"
                   placeholder="Demirbaş birimi giriniz"/>

        </div>
        <div class="col-md-4">
            <input type="number" step="1" class="form-control"
                   name="stock" ng-model="stockTotal"
                   value="" autocomplete="off"
                   placeholder="Demibaşın miktarını giriniz"/>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-md-4 col-md-offset-4 ">
        <button type="button" ng-click="addStock()"
                class="btn btn-primary btn-flat btn-block">Ekle
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-3" ng-show="stockError != ''">
        <span class="text-danger"><%stockError%></span>
    </div>
</div>
<br>

<div class="row">
    <div class="col-md-12">
        <h3>Mevcut Demirbaş</h3>
    </div>

    <div class="col-md-12">
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
                <tr ng-repeat="stock in stocks | searchFor:stockName track by $index">
                    <td><% stock.name | trUp %></td>
                    <td><% stock.unit | trUp %></td>
                    <td><% stock.total %></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>