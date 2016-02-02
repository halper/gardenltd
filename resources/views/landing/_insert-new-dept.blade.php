<div class="row">
    <div class="col-xs-12 col-md-12">

        <div class="row">
            <div class="col-md-12">
                <div class="row">


                    <div class="col-md-1">
                        <label for="name">Departman Adı: </label>
                    </div>
                    <div class="col-md-4">

                        <input type="text" class="form-control"
                               name="department" ng-model="department_name"
                               value="" autocomplete="off"
                               placeholder="Yeni departman giriniz"/>

                    </div>


                    <div class="col-xs-12 col-md-2 ">
                        <button type="button" ng-click="addDepartment()"
                                class="btn btn-primary btn-flat btn-block">Ekle
                        </button>
                    </div>
                    <div class="col-md-3" ng-show="newDept != ''">
                        <span class="text-success"><%newDept%> departmanlar arasına eklendi</span>
                    </div>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3" ng-show="deptError != ''">
                <span class="text-danger"><%deptError%></span>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-12">
                <h3>Mevcut Departmanlar</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"
                 ng-repeat="dept in departments | filter:(department_name | trUp) track by $index">
                <span><% dept.department | trUp %></span>
            </div>
        </div>


    </div>
</div>