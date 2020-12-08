<?php

Route::group(['middleware' => ['web', 'auth', 'permission_check'], 'prefix' => 'admin/blog', 'namespace' => 'WEB'], function () {
    Route::get('/', 'BlogController@index');
    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index');
        Route::get('/create', 'PostController@create');
        Route::get('/edit', 'PostController@edit');
        Route::any('/delete', 'PostController@delete');

        Route::post('/store', 'PostController@store');
        Route::post('/update', 'PostController@update');

    });

    Route::group(['prefix' => 'page'], function () {

        Route::get('/', 'PageController@index');
        Route::get('/create', 'PageController@create');
        Route::get('/edit', 'PageController@edit');
        Route::any('/delete', 'PageController@delete');

        Route::post('/store', 'PageController@store');
        Route::post('/update', 'PageController@update');

    });

    Route::group(['prefix' => 'category'], function () {

        Route::get('/', 'CategoryController@index');
        Route::get('/show', 'CategoryController@show');
        Route::get('/create', 'CategoryController@create');
        Route::get('/edit', 'CategoryController@edit');
        Route::get('/delete', 'CategoryController@delete');

        Route::post('/store', 'CategoryController@store');
        Route::post('/update', 'CategoryController@update');
    });

    Route::group(['prefix' => 'comment'], function () {

        Route::get('/', 'CommentController@index');
        Route::any('/confirm', 'CommentController@confirm');
        Route::any('/delete', 'CommentController@delete');

    });

});

/********************* EndUser Functions ****************************/

Route::group(['middleware' => ['web'], 'prefix' => 'blog', 'namespace' => 'Modules\Blog\Http\Controllers\WEB'], function () {

    Route::get('/', 'BlogController@index_front');
    Route::get('/post', 'PostController@index_front');
    Route::get('/post/show', 'PostController@show_front');
    Route::get('/post/search', 'PostController@search_front');

    Route::get('/page/show', 'PageController@show_front');

    Route::get('/category', 'CategoryController@index_front');
    Route::get('/category/show', 'CategoryController@show_front');

});

Route::group(['middleware' => ['web'], 'prefix' => 'blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {

    Route::post('/comment/store', 'CommentController@store_front');

});
