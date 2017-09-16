<?php

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

/** @var \Laravel\Lumen\Routing\Router $router */
$router->post('register', [
  'as' => 'register', 'uses' => 'UserController@register'
]);
$router->post('login', [
  'as' => 'login', 'uses' => 'UserController@login'
]);

$router->group(['middleware' => 'auth:api'], function () use ($router) {
  $router->get('getUserId', [
    'as' => 'getUserId', 'uses' => 'UserController@getUserId'
  ]);
});