<?php

Route::group(['namespace' => 'Abs\CardPkg', 'middleware' => ['web', 'auth'], 'prefix' => 'card-pkg'], function () {
	//FAQs
	Route::get('/card-types/get-list', 'CardTypeController@getCardTypeList')->name('getCardTypeList');
	Route::get('/card-type/get-form-data', 'CardTypeController@getCardTypeFormData')->name('getCardTypeFormData');
	Route::post('/card-type/save', 'CardTypeController@saveCardType')->name('saveCardType');
	Route::get('/card-type/delete', 'CardTypeController@deleteCardType')->name('deleteCardType');
});

Route::group(['namespace' => 'Abs\CardPkg', 'middleware' => ['web'], 'prefix' => 'card-pkg'], function () {
	//FAQs
	Route::get('/card-types/get', 'CardTypeController@getCardTypes')->name('getCardTypes');
});
