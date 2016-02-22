<p><span class="text-warning">Rapor aç</span> tuşu ile olmayan raporları oluşturur ve mevcut raporlar için kullanıcıya
    raporu güncellemesi için yetki verebilirsiniz.</p>
<p>
    <span class="text-primary">Rapor oluştur</span> tuşu ile olmayan raporları oluşturur ve ilgili kullanıcıya raporu
    güncellemesi için yetki verebilirsiniz.</p>
<div ng-controller="ReportController" id="angReport">
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-4" style="min-width: 260px">
                <div id="reportrange"
                     style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                    <span class="text-center"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <select class="form-control" ng-options="site as site.jobName for site in sites track by site.id"
                        ng-model="selected"
                        ng-change="getAccInfo()">
                    <option value="" selected disabled>Şantiye seçiniz</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-control" ng-options="user as user.name for user in users track by user.id"
                        ng-model="selectUser">
                    <option value="" selected disabled>Kullanıcı seçiniz</option>
                </select>
            </div>

        </div>
    </div>
    <div ng-hide="!message" class="row">
        <div class="row"><p class="text-success"><% message %></p></div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-flat btn-warning btn-block" ng-click="makeReportable()">Rapor Aç</button>
            </div>
            <div class="col-md-4 pull-right">
                <button class="btn btn-flat btn-primary btn-block" ng-click="createReport()">Rapor Oluştur</button>
            </div>
        </div>
    </div>

    <div class="row" ng-hide="!openReports">
        <div class="col-sm-12">
            <h4>Açık Raporlar</h4>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-8 col-md-4">
                        <div class="input-group">
                            <input type="text" style="width: 100%"
                                   name="search" ng-model="name"
                                   value=""
                                   placeholder="Tarih, şantiye veya kullanıcı giriniz"/>
                                                            <span class="input-group-addon add-on"><i
                                                                        class="fa fa-search"></i></span>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <table class="table table-responsive table-extra-condensed dark-bordered">
                <thead>
                <tr>
                    <th class="text-center">Tarih</th>
                    <th>Şantiye</th>
                    <th class="text-center">Kullanıcı</th>
                    <th class="text-center">Kapat</th>
                </tr>
                </thead>
                <tbody>
                <tr class="text-center" ng-repeat="report in openReports | searchFor:name track by $index">
                    <td><%report.date%></td>
                    <td class="text-left"><%report.site%></td>
                    <td><%report.user%></td>
                    <td><a href="#" ng-click="closeReport(report)"><i class="fa fa-close"></i></a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
