<div class="row">
    <div class="col-xs-12 col-md-12">
        <div ng-controller="TagController">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">


                        <div class="col-md-1">
                            <label for="name">Etiket Adı: </label>
                        </div>
                        <div class="col-md-4">

                            <input type="text" class="form-control"
                                   name="tag" ng-model="name"
                                   value="" autocomplete="off"/>

                        </div>


                        <div class="col-xs-12 col-md-2 ">
                            <button type="button" ng-click="addTag()"
                                    class="btn btn-primary btn-flat btn-block">Ekle
                            </button>
                        </div>
                        <div class="col-md-3" ng-show="newTag != ''">
                            <span class="text-success"><%newTag%> etiketler arasına eklendi</span>
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
                    <h3>Mevcut Etiketler</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2"
                     ng-repeat="tag in presentTags | filter:(name | trUp) |orderBy:'name' track by $index">
                    <span><% tag.name %></span>
                </div>
            </div>

        </div>
    </div>
</div>