<div class="row">
    <div class="col-xs-12 col-md-12">

        <div class="row">
            <div class="col-md-12">
                <div class="row">


                    <div class="col-md-1">
                        <label for="name">İş Kolu Adı: </label>
                    </div>
                    <div class="col-md-4">

                        <input type="text" class="form-control"
                               name="staff" ng-model="name"
                               value="" autocomplete="off"
                               placeholder="Yeni iş kolu giriniz"/>

                    </div>

                    <div class="col-md-3">

                        <select class="form-control" name="department" ng-model="dept"
                                ng-options="dept as dept.department for dept in departments">
                            <option value='' selected disabled>Departman seçiniz</option>
                        </select>

                    </div>


                    <div class="col-xs-12 col-md-2 ">
                        <button type="button" ng-click="addStaff()"
                                class="btn btn-primary btn-flat btn-block">Ekle
                        </button>
                    </div>

                    <div class="col-md-3" ng-show="newStaff != ''">
                        <span class="text-success"><%newStaff%> iş kolları arasına eklendi</span>
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
                <h3>Mevcut İş Kolları</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"
                 ng-repeat="st in staffs | filter:(name | trUp) track by $index">
                <span><% st.name | trUp %> (<%st.department%>)</span>
            </div>
        </div>
    </div>
</div>