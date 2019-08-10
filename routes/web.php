<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'SearchController@index')->name('search');
Route::get('autocomplete', 'SearchController@autocomplete')->name('autocomplete');
Route::get('/show', 'SearchController@showFromSearch')->name('showFromSearch');
Route::get('/items/{id}', 'SearchController@showById')->name('showById');
Route::get('/items/{id}', 'SearchController@showById')->name('showById');
Route::get('/imports', 'SearchController@startImportView')->name('startImportView');
Route::post('/startimport', 'SearchController@startImport')->name('startImport');
