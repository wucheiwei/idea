<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'article'], function () use ($router) {
    $router->get('/index', 'ArticleController@index');
    $router->get('/{id}', 'ArticleController@edit');
    $router->post('/create', 'ArticleController@create');
    $router->put('/update', 'ArticleController@update');
    $router->delete('/delete/{id}', 'ArticleController@delete');
});
$router->group(['prefix' => 'translation'], function () use ($router) {
    $router->get('/index', 'TranslationController@index');
    $router->get('/{id}', 'TranslationController@edit');
    $router->post('/create', 'TranslationController@create');
    $router->put('/update/{id}', 'TranslationController@update');
    $router->delete('/delete/{id}', 'TranslationController@delete');
});

