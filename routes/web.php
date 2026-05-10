<?php

use App\Business;
use Illuminate\Support\Facades\Route;

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
    $randomBusiness= Business::inRandomOrder()->first();
    return view('welcome', compact('randomBusiness'));
});

Route::get('/health', function () {
    return response('ok', 200);
});


Route::get('/businesses/{business}/review', 'ReviewController@index')->name('reviews.index');
Route::get('/businesses/review/{review}', 'ReviewController@fetch');

Route::get('/businesses/{business}/review/showcased', 'ReviewController@showcased')->name('reviews.showcased');


Route::group(['middleware' => 'auth'], function () {
    Route::get('/businesses/create', 'BusinessController@create')->name('business.create');
    Route::post('/businesses', 'BusinessController@store')->name('business.store');

    Route::post('/businesses/{business}/review', 'ReviewController@store')->name('reviews.store');
    Route::post('/businesses/{business}/images', 'BusinessImageController@store')->name('images.store');
    Route::get('/businesses/{business}/images/all', 'BusinessImageController@index')->name('images.index');

    Route::get('/businesses/{business}/images', 'BusinessImageController@create');

    Route::post('/reviews/{review}/react', 'ReviewReactionController@store')->name('reviews.react');

    Route::post('/reviews/{review}/showcase/remove', 'ReviewShowcaseController@remove')->name('reviews.remove');
    Route::post('/reviews/{review}/showcase', 'ReviewShowcaseController@store')->name('reviews.showcase');
    // Route::delete('/reviews/{review}/react', 'ReviewReactionController@delete')->name('reviews.remove');

    Route::post('/businesses/review/{review}/reply', 'ReplyController@store');


    Route::post('/profiles/{user}/avatars', 'AvatarController@store')->name('profiles.add-avatar');
    Route::delete('/profiles/{user}/avatars', 'AvatarController@delete')->name('profiles.remove-avatar');

    Route::get('/profiles/{user}', 'ProfileController@show')->name('profiles.show');
});

Route::get('/businesses', 'BusinessController@index');
Route::get('/businesses/random', 'BusinessController@random');
Route::get('/businesses/{business}', 'BusinessController@show');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
