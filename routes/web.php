<?php
declare(strict_types=1);

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
/**
 * @api {post} /register Register
 * @apiVersion 0.1.0
 * @apiDescription Register a new user
 * @apiName PostRegister
 * @apiGroup User
 *
 * @apiParam {String} email the unique email address of the user
 * @apiParam {string{8..}} password the password
 * @apiParam {integer{>=0}} lastConfirmedAGBVersion last confirmed AGB version
 *
 * @apiSuccess {String} id the id of the newly created user
 * @apiError ValidationException The provided email is malformed or does already exist, or the provided password is too
 *                               short
 */
$router->post('register', [
  'as' => 'register', 'uses' => 'UserController@register'
]);

/**
 * @api {post} /login Login
 * @apiVersion 0.1.0
 * @apiDescription Logs in a user and gets his authentication token
 * @apiName PostLogin
 * @apiGroup User
 *
 * @apiParam {String} email the email address of the user
 * @apiParam {string{8..}} password the users password
 *
 * @apiSuccess {String} id the id of the user
 * @apiHeader (Response Headers) {String} jwt-token Authorization Bearer token.
 * @apiError ValidationException The provided email is malformed or does already exist, or the provided password is too
 *                               short
 */
$router->post('login', [
  'as' => 'login', 'uses' => 'UserController@login'
]);

/**
 * @apiDefine AuthenticatedRequest
 *
 * @apiVersion 0.1.0
 *
 * @apiHeader {String} Authorization Bearer Authorization Token
 * @apiError UnauthorizedException No token given ot the given token is invalid
 *
 */

$router->group(['middleware' => 'auth:api'], function () use ($router) {
  /**
   * @api {get} /userId Get User ID
   * @apiUse AuthenticatedRequest
   * @apiVersion 0.1.0
   * @apiDescription Gets the user id of the currently logged in user
   * @apiName GetUserId
   * @apiGroup User
   *
   * @apiSuccess {String} id the id of the user
   */
  $router->get('userId', [
    'as' => 'userId', 'uses' => 'UserController@userId'
  ]);
});