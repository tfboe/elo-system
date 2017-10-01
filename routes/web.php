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
 * @apiError UnauthorizedException No token given or the given token is invalid
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

  /**
   * @api {post} /createOrUpdateTournament Create or Update a Tournament
   * @apiUse AuthenticatedRequest
   * @apiVersion 0.1.0
   * @apiDescription Creates a new tournament or if the tournament already exists updates it
   * @apiName PostCreateOrUpdateTournament
   * @apiGroup Tournament
   *
   * @apiParam {String} userIdentifier a identifier which identifies the tournament uniquely across all tournaments of
   *                                   this user
   * @apiParam {String} name the name of the tournament
   * @apiParam {String} [tournamentListId=""] the list id of which the tournament is part of
   * @apiParam {String=OFFICIAL,SPEEDBALL,CLASSIC} [gameMode] The rule mode of the tournament. All games of the
   *                                                          tournament which do not specify another game mode will use
   *                                                          this game mode.
   * @apiParam {String=ELIMINATION,QUALIFICATION} [organizingMode] The organization mode of the tournament. All games of
   *                                                               the tournament which do not specify another
   *                                                               organizing mode will use this organizing mode.
   * @apiParam {String=ONE_SET,BEST_OF_THREE,BEST_OF_FIVE} [scoreMode] The score mode of the tournament. All games of
   *                                                                   the tournament which do not specify another
   *                                                                   score mode will use this score mode.
   * @apiParam {String=DOUBLE,SINGLE,DYP} [teamMode] Specifies the team mode of the tournament. If the partners were
   *                                                 chosen randomly at some point the mode should be DYP. All games of
   *                                                 the tournament which do not specify another table will use this
   *                                                 table.
   * @apiParam {String=MULTITABLE,GARLANDO,LEONHART,TORNADO,ROBERTO_SPORT,BONZINI} [table]
   *           On which sort of table the tournament is played. Multitable should only be used if the table is not known
   *           anymore or if the game was really a multitable game, i.e. multiple sets on at least two different tables.
   *           All games of the tournament which do not specify another table will use this table.
   * @apiError ValidationException The userIdentifier or the name of the tournament are missing or one of the modes or
   *                               the given table is not in the list of valid options.
   * @apiSuccess {String} type the type of the successful operation either "create" or "update"
   */
  $router->post('createOrUpdateTournament', [
    'as' => 'createOrUpdateTournament', 'uses' => 'TournamentController@createOrUpdateTournament'
  ]);
});