<?php

Route::group(['middleware' => ['web', 'auth']], function () {

    Route::get('/', 'Modules\Blog\Http\Controllers\BlogController@index_front');

});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin/blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {
    Route::get('/', 'BlogController@index');
    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index');
        Route::get('/create', 'PostController@create');
        Route::get('/edit', 'PostController@edit');
        Route::any('/destroy', 'PostController@destroy');

        Route::post('/store', 'PostController@store');
        Route::post('/update', 'PostController@update');

        Route::get('/report', 'PostController@report');
    });

    Route::group(['prefix' => 'category'], function () {

        Route::get('/', 'CategoryController@index');
        Route::get('/show', 'CategoryController@show');
        Route::get('/create', 'CategoryController@create');
        Route::get('/edit', 'CategoryController@edit');
        Route::get('/destroy', 'CategoryController@destroy');

        Route::post('/store', 'CategoryController@store');
        Route::post('/update', 'CategoryController@update');
    });

});

/********************* Front Functions ****************************/

Route::group(['middleware' => ['web'], 'prefix' => 'blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {

    Route::get('/', 'BlogController@index_front');
    Route::get('/posts', 'PostController@index_front');
    Route::get('/posts/show', 'PostController@show_front');

});

/********************* API Functions ****************************/

Route::group(['middleware' => ['web'], 'prefix' => 'api/blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {

    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index_api');

        Route::get('/show', 'PostController@show_api');

        Route::post('/store_comment', 'PostController@store_comment');

    });

});
