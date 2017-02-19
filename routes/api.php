<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {
	// ничего не надо
	Route::get('categories', 'v1\CategoriesController@index');
	Route::get('categories/{id}', 'v1\CategoriesController@show');

	Route::get('countries', 'v1\CountriesController@index');
	Route::get('countries/{id}', 'v1\CountriesController@show');

	Route::get('delivers', 'v1\DeliversController@index');
	Route::get('delivers/{id}', 'v1\DeliversController@show');

	Route::get('discounts', 'v1\DiscountsController@index');
	Route::get('discounts/{id}', 'v1\DiscountsController@show');

	Route::get('plans', 'v1\PlansController@index');
	Route::get('plans/{id}', 'v1\PlansController@show');

	Route::get('tags', 'v1\TagsController@index');
	Route::get('tags/{id}', 'v1\TagsController@show');

	Route::get('products', 'v1\ProductsController@index');
	Route::get('products/{id}', 'v1\ProductsController@show');

	Route::get('users', 'v1\UsersController@index');
	Route::get('users/{id}', 'v1\UsersController@show');

	Route::post('login', 'v1\JwtAuthenticateController@login');
	Route::post('registration', 'v1\JwtAuthenticateController@registration');
	Route::post('reset', 'v1\JwtAuthenticateController@reset');
	Route::post('forgot', 'v1\JwtAuthenticateController@forgot');
	// Route::get('locations', 'v1\LocationsController@')
	Route::post('images', 'v1\ImagesController@store');
});

Route::group(['prefix' => 'v1', 'middleware' => []], function() {
	// надо токен для каких то действий
	// Route::post('images', 'v1\ImagesController@store');
});

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth', 'ability:,delete-products']], function() {
	// удаление продуктов
	Route::delete('products/{id}', 'v1\ProductsController@destroy');
});

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth', 'ability:,create-products']], function() {
	// создание продуктов
	Route::post('products', 'v1\ProductsController@store');
});

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth', 'ability:,delete-users']], function() {
	// удаление пользователей
	Route::delete('users/{id}', 'v1\UsersController@destroy');
});

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth', 'ability:,update-products']], function() {
	// обновление продуктов
	Route::put('products/{id}', 'v1\ProductsController@update');
});

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.auth', 'ability:,update-users']], function() {
	// обновление пользователей
	Route::put('users/{id}', 'v1\UsersController@update');
});

Route::group(['prefix' => 'v1', 'middleware' => 'ability:admin,'], function() {
	// работа со статическими данными: категории, страны, доставка etc.
	// добавление удаление и прочее
	// изменение ролей и пермишенов
	Route::post('roles', 'v1\JwtAuthenticateController@createRole');
	Route::post('assign-role', 'v1\JwtAuthenticateController@assignRole');

	Route::post('permissions', 'v1\JwtAuthenticateController@createPermission');
	Route::post('attach-permission', 'v1\JwtAuthenticateController@attachPermission');
});
