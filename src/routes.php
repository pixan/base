<?php

Route::post('password/email', ['uses' => 'Auth\PasswordController@postEmail']);

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

Route::group(['prefix' => 'api'], function () {
	Route::group(['prefix' => 'v1'], function () {

		Route::post('signup', ['uses' => '\Pixan\Base\UsersController@signup']);

		Route::group(['middleware' => 'oauth'], function () {
			Route::post('users/{id}/updateProfilePicture', ['uses' => '\Pixan\Base\UsersController@updateProfilePicture']);
			Route::get('users/{id}/friends', ['uses' => '\Pixan\Base\UsersController@friends']);
			Route::get('users/search/{term}', ['uses' => '\Pixan\Base\UsersController@search']);
			Route::resource('users', '\Pixan\Base\UsersController', ['except' => ['store']]);
			
			Route::post('friends/request', ['uses' => '\Pixan\Base\FriendsController@request']);
			Route::post('friends/accept', ['uses' => '\Pixan\Base\FriendsController@accept']);
			Route::post('friends/block', ['uses' => '\Pixan\Base\FriendsController@block']);
			Route::resource('friends', '\Pixan\Base\FriendsController');
		});
	});
});