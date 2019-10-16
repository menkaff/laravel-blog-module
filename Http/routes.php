<?php

Route::group(['middleware' => ['web', 'auth' , 'permission_check']], function () {

    Route::get('/', 'Modules\Blog\Http\Controllers\BlogController@index_front');

});

Route::group(['middleware' => ['web', 'auth' , 'permission_check'], 'prefix' => 'admin/blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {
    Route::get('/', 'BlogController@index');
    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index');
        Route::get('/create', 'PostController@create');
        Route::get('/edit', 'PostController@edit');
        Route::any('/destroy', 'PostController@destroy');

        Route::post('/store', 'PostController@store');
        Route::post('/update', 'PostController@update');

    });

    Route::group(['prefix' => 'page'], function () {

        Route::get('/', 'PageController@index');
        Route::get('/create', 'PageController@create');
        Route::get('/edit', 'PageController@edit');
        Route::any('/destroy', 'PageController@destroy');

        Route::post('/store', 'PageController@store');
        Route::post('/update', 'PageController@update');

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

    Route::group(['prefix' => 'comment'], function () {

        Route::get('/', 'CommentController@index');
        Route::any('/confirm', 'CommentController@confirm');
        Route::any('/destroy', 'CommentController@destroy');

    });

});

/********************* Front Functions ****************************/

Route::group(['middleware' => ['web'], 'prefix' => 'blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {

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

/********************* API Functions ****************************/

Route::group(['middleware' => ['web'], 'prefix' => 'api/blog', 'namespace' => 'Modules\Blog\Http\Controllers'], function () {

    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index_api');

        Route::get('/show', 'PostController@show_api');

        Route::post('/store_comment', 'PostController@store_comment');

    });

});
