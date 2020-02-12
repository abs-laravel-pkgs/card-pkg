app.config(['$routeProvider', function($routeProvider) {

    $routeProvider.
    when('/card-types', {
        template: '<card-types></card-types>',
        title: 'Card Types',
    });
}]);

app.component('cardTypes', {
    templateUrl: card_type_list_template_url,
    controller: function($http, $location, HelperService, $scope, $routeParams, $rootScope, $location) {
        $scope.loading = true;
        var self = this;
        self.hasPermission = HelperService.hasPermission;
        $http({
            url: laravel_routes['getCardTypes'],
            method: 'GET',
        }).then(function(response) {
            self.card_types = response.data.card_types;
            $rootScope.loading = false;
        });
        $rootScope.loading = false;
    }
});