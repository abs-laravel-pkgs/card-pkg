<?php
Route::group(['namespace' => 'Abs\CardPkg\Api', 'middleware' => ['api']], function () {
	Route::group(['prefix' => 'card-pkg/api'], function () {
		Route::group(['middleware' => ['auth:api']], function () {
			// Route::get('taxes/get', 'TaxController@getTaxes');
		});
	});
});