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
// Route::get('test/generate','Admin\GenerateByLinkController@getFormGenTests')->name('crud.test.generateForm');
Route::group([
	// 'prefix'     => 'admin/',
	'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
	'namespace'  => 'Admin',
], function () { // custom admin routes
	Route::get('test/generate','GenerateByLinkController@getFormGenTests')->name('crud.test.generateForm');
	Route::post('test/generate','GenerateByLinkController@generateTest')->name('crud.test.genTest');
	Route::post('test/generates','GenerateByLinkController@generateTests')->name('crud.test.genTests');
}); // this should be the absolute last line of this file
