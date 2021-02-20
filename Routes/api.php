<?php
Route::group([
    'prefix' => 'blog/v1/admin',
    'middleware' => ['auth.api_blog_admin'],
    'namespace' => 'API\Admin',
], function ($api) {
    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index');
        Route::get('/show', 'PostController@show');
        Route::any('/delete', 'PostController@delete');

        Route::post('/store', 'PostController@store');
        Route::post('/update', 'PostController@update');
    });

    Route::group(['prefix' => 'page'], function () {

        Route::get('/', 'PageController@index');
        Route::get('/show', 'PageController@show');
        Route::any('/delete', 'PageController@delete');

        Route::post('/store', 'PageController@store');
        Route::post('/update', 'PageController@update');
    });
});

Route::group([
    'prefix' => 'blog/v1/editor',
    'middleware' => ['auth.api_blog_editor'],
    'namespace' => 'API\Editor',
], function ($api) {
    Route::group(['prefix' => 'post'], function () {

        Route::get('/', 'PostController@index');
        Route::get('/show', 'PostController@show');
        Route::any('/delete', 'PostController@delete');

        Route::post('/store', 'PostController@store');
        Route::post('/update', 'PostController@update');
    });

    Route::group(['prefix' => 'page'], function () {

        Route::get('/', 'PageController@index');
        Route::get('/show', 'PageController@show');
        Route::any('/delete', 'PageController@delete');

        Route::post('/store', 'PageController@store');
        Route::post('/update', 'PageController@update');
    });

    Route::group(['prefix' => 'category'], function () {

        Route::get('/', 'CategoryController@index');
        Route::get('/show', 'CategoryController@show');
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

Route::group([
    'prefix' => 'blog/v1/end_user',
    'namespace' => 'API\EndUser',
], function ($api) {

    Route::get('/post', 'PostController@index');
    Route::get('/post/show', 'PostController@show');

    Route::get('/page', 'PageController@index');
    Route::get('/page/show', 'PageController@show');

    Route::get('/category', 'CategoryController@index');
    Route::get('/category/show', 'CategoryController@show');

    Route::post('/comment/store', 'CommentController@store');
});
