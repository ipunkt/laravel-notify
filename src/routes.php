<?php

Route::model('notification', 'Ipunkt\LaravelNotify\Models\Notification');
Route::get(
	\Config::get('laravel-notify::notify.routes.index'), array(
		'as' => 'notify.index',
		'uses' => 'Ipunkt\LaravelNotify\Controllers\NotifyController@index'
	));
Route::get(
	\Config::get('laravel-notify::notify.routes.action'), array(
		'as' => 'notify.action',
		'uses' => 'Ipunkt\LaravelNotify\Controllers\NotifyController@action'
	));