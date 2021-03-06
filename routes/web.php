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

Route::post('setStockOption','ProductOptionsController@setStockOption')->name('setStockOption');
Route::post('setColorOption','ProductOptionsController@setColorOption')->name('setColorOption');

Route::post('schedule_date','ProductOptionsController@setScheduledProductionDate');

Route::get('auto_campaign','ProductOptionsController@autoCampaign');
Route::get('change_frequency','ProductOptionsController@changeFrequency')->name('change_frequency');
Route::get('get_dates','ProductOptionsController@getAutoCampaignMailingData')->name('get_dates');
Route::get('accept_campaign_terms','ProductOptionsController@acceptAutoCampaignTerms');
Route::post('save_notes','ProductOptionsController@saveNotes');
Route::post('addBinderyOption','ProductOptionsController@addBinderyOption')->name('addBinderyOption');
Route::post('removeBinderyOption','ProductOptionsController@removeBinderyOption')->name('removeBinderyOption');
Route::post('addProof','ProductOptionsController@addProof')->name('addProof');
Route::post('removeProof','ProductOptionsController@removeProof')->name('removeProof');
Route::get('addBinderyItem','ProductOptionsController@addBinderyOption')->name('addBinderyItem');
Route::post('setFinishOption','ProductOptionsController@setFinishOption')->name('setFinishOption');


