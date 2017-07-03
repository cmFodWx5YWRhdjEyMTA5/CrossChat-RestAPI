<?php

Route::group(['prefix'=>'crosschat/api/v1'],function(){
	Route::post('nearby_places',['uses'=>'UserLocationNetworkActivity@getNearbyPlaces']);
	Route::post('testing',['uses'=>'UserLocationNetworkActivity@simpleTesting']);
	Route::post('register_user',['uses'=>'Registration@registerUser']);
	Route::post('hello',['uses'=>"UserLocationNetworkActivity@testingMethod"]);
	Route::post('login_user',['uses'=>"LoginController@login"]);
	Route::post('update_current_location',['uses'=>"UserLocationNetworkActivity@updateCurrentLocation"]);
	Route::post('interests',['uses'=>"UserLocationNetworkActivity@interestedFieldSearch"]);
	Route::post('forgot_password',['uses'=>"LoginController@forgotPassword"]);
});
Route::get('/', function () {
    return view('welcome');
});

