@if(\App\Site::all()->count() > 0)
    <div ng-controller="PuantajController" id="angPuantaj">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-12">
                    <select class="form-control" ng-options="site as site.jobName for site in sites track by site.id"
                            ng-model="selected"
                            ng-change="getAccInfo()">
                        <option value="" selected disabled>Şantiye seçiniz</option>
                    </select>
                </div>
            </div>
        </div>
        <div ng-hide="!message"><p class="text-success"><% message %></p></div>
        <div ng-hide="!showRest">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label for="owner">Kasa Sahibi: </label>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" ng-options="user as user.name for user in users track by user.id"
                                ng-model="selectUser">
                            <option value="" selected disabled>Kullanıcı seçiniz</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label for="card-owner">Kart Sahibi: </label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                               name="card-owner" ng-model="cardOwner"
                               value=""
                               placeholder="Kart Sahibi"/>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <label for="period">Dönem: </label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control"
                               name="period" ng-model="period"
                               value=""
                               placeholder="Dönem"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <button class="btn btn-flat btn-primary btn-block" ng-click="saveAccount()">Kaydet</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@else
    <p>Görüntülenecek talep bulunmamaktadır.</p>
@endif