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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'v1', 'namespace' => 'V1'], function () use ($router) {
        //USER
        $router->group(['prefix' => 'user', 'namespace' => 'User'], function () use ($router) {
            $router->post('create', 'UserController@create');
            $router->post('verify', 'UserController@verification');
        });
        
        //AUTH REQUIRED
        $router->group(['middleware' => 'auth'], function () use ($router) {
        });
    });
});
