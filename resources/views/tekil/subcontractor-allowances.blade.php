@extends('tekil.layout')

@section('page-specific-js')
    <script src="<?=URL::to('/');?>/js/angular.min.js"></script>
    <script src="<?= URL::to('/'); ?>/js/moment.min.js" type="text/javascript"></script>
    <script>
        String.prototype.turkishToLower = function () {
            var string = this;
            var letters = {"İ": "i", "I": "ı", "Ş": "ş", "Ğ": "ğ", "Ü": "ü", "Ö": "ö", "Ç": "ç"};
            string = string.replace(/(([İIŞĞÜÇÖ]))/g, function (letter) {
                return letters[letter];
            });
            return string.toLowerCase();
        };

        var puantajApp = angular.module('puantajApp', [], function ($interpolateProvider) {
            $interpolateProvider.startSymbol('<%');
            $interpolateProvider.endSymbol('%>');
        }).controller('PuantajController', function ($scope, $http) {
            $scope.subcontractors = [];
            $http.get("<?= URL::to('/'); ?>/tekil/{{$site->slug}}/retrieve-subcontractor-allowances")
                    .then(function(response){
                        $scope.subcontractors = response.data;
                    });
        }).filter('numberFormatter', function () {
            return function (data) {
                return $.number(data, 2, ',', '.');
            }
        }).filter('filTotal', function () {
            return function (data, key) {
                if (angular.isUndefined(data))
                    return 0;
                var subTotal = 0;

                angular.forEach(data, function (v, k) {
                    if (angular.isUndefined(key))
                        subTotal += parseFloat(v);
                    else
                        subTotal += parseFloat(v[key]);
                });

                return subTotal;
            }
        });
    </script>
@endsection

@section('content')

    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Taşeron Hakedişleri
                    </h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">


                    <div ng-app="puantajApp" ng-controller="PuantajController" id="angPuantaj">
                        <p class="text-danger alert-danger" ng-show="subError"><%subError%></p>

                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-responsive table-extra-condensed dark-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center">S.N</th>
                                        <th class="text-left">Alt Yüklenici</th>
                                        <th class="text-right">Borç</th>
                                        <th class="text-right">Alacak Tutarı</th>
                                        <th class="text-right">Bakiye</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr ng-repeat="subcontractor in subcontractors track by $index">
                                        <td class="text-center"><%$index+1%></td>
                                        <td class="text-left"><%subcontractor.name%></td>
                                        <td class="text-right"><%subcontractor.debt | numberFormatter%> TL</td>
                                        <td class="text-right"><%subcontractor.claim | numberFormatter%> TL</td>
                                        <td class="text-right"><%subcontractor.balance | numberFormatter%> TL</td>
                                    </tr>
                                    <tr class="bg-warning">
                                        <td>-</td>
                                        <td style="text-align: center"><strong>GENEL TOPLAM</strong></td>
                                        <td style="text-align: right"><strong><%subcontractors | filTotal:'debt' | numberFormatter%>TL</strong>
                                        <td style="text-align: right"><strong><%subcontractors | filTotal:'claim' | numberFormatter%>TL</strong>
                                        <td style="text-align: right"><strong><%subcontractors | filTotal:'balance' | numberFormatter%>TL</strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>



@endsection