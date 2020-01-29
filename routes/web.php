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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('product', 'ProductOptionsController@product')->name('product');

Route::get('preview-design','ProductOptionsController@getPreviewDesign')->name('preview-design');
Route::get('start-over','ProductOptionsController@startOver')->name('start-over');
/* Route::get('change-design/{designFileId?}/{side?}/{type?}','ProductOptionsController@changeDesign')->name('change-design'); */

Route::get('productOption', 'ProductOptionsController@index')->name('productOption');

Route::get('setStockOption','ProductOptionsController@setStockOption')->name('setStockOption');
Route::get('setColorOption','ProductOptionsController@setColorOption')->name('setColorOption');

Route::get('schedule_date','ProductOptionsController@setScheduledProductionDate');

Route::get('auto_campaign','ProductOptionsController@getAutoCampaign');
Route::get('addBinderyOption','ProductOptionsController@addBinderyOption')->name('addBinderyOption');
Route::get('removeBinderyOption','ProductOptionsController@removeBinderyOption')->name('removeBinderyOption');
Route::get('addProof','ProductOptionsController@addProof')->name('addProof');
Route::get('removeProof','ProductOptionsController@removeProof')->name('removeProof');
Route::get('addBinderyItem','ProductOptionsController@addBinderyOption')->name('addBinderyItem');
Route::get('setFinishOption','ProductOptionsController@setFinishOption')->name('setFinishOption');