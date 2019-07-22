<?php

Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/user', function () {
    return Auth::user();
})->name('user');

Route::post('/photos', 'PhotoController@create')->name('photo.create');
Route::get('/photos', 'PhotoController@index')->name('photo.index');
Route::get('/photos/{id}', 'PhotoController@show')->name('photo.show');