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
 * @apiParam {string} email the unique email address of the user
 * @apiParam {string{8..}} password the password
 * @apiParam {integer{>=0}} lastConfirmedAGBVersion last confirmed AGB version
 *
 * @apiSuccess {string} id the id of the newly created user
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
 * @apiParam {string} email the email address of the user
 * @apiParam {string{8..}} password the users password
 *
 * @apiSuccess {string} id the id of the user
 * @apiHeader (Response Headers) {string} jwt-token Authorization Bearer token.
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
 * @apiHeader {string} Authorization Bearer Authorization Token
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
   * @apiSuccess {string} id the id of the user
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
   * @apiParam {string} userIdentifier a identifier which identifies the tournament uniquely across all tournaments of
   *                                   this user
   * @apiParam {string} name the name of the tournament
   * @apiParam {string} [tournamentListId=""] the list id of which the tournament is part of
   * @apiParam {string=OFFICIAL,SPEEDBALL,CLASSIC} [gameMode] The rule mode of the tournament. All games of the
   *                                                          tournament which do not specify another game mode will use
   *                                                          this game mode.
   * @apiParam {string=ELIMINATION,QUALIFICATION} [organizingMode] The organization mode of the tournament. All games of
   *                                                               the tournament which do not specify another
   *                                                               organizing mode will use this organizing mode.
   * @apiParam {string=ONE_SET,BEST_OF_THREE,BEST_OF_FIVE} [scoreMode] The score mode of the tournament. All games of
   *                                                                   the tournament which do not specify another
   *                                                                   score mode will use this score mode.
   * @apiParam {string=DOUBLE,SINGLE,DYP} [teamMode] Specifies the team mode of the tournament. If the partners were
   *                                                 chosen randomly at some point the mode should be DYP. All games of
   *                                                 the tournament which do not specify another team mode will use this
   *                                                 team mode.
   * @apiParam {string=MULTITABLE,GARLANDO,LEONHART,TORNADO,ROBERTO_SPORT,BONZINI} [table]
   *           On which sort of table the tournament is played. Multitable should only be used if the table is not known
   *           anymore or if the game was really a multitable game, i.e. multiple sets on at least two different tables.
   *           All games of the tournament which do not specify another table will use this table.
   * @apiParam {Object[]} competitions list of competitions, at least one competition must be given
   * @apiParam {string} competitions.name the name of the competition, must be unique for all competitions in a
   *                                      tournament
   * @apiParam {string=OFFICIAL,SPEEDBALL,CLASSIC} [competitions.gameMode]
   *           The rule mode of the competition. All games of the competition which do not specify another game mode
   *           will use this game mode.
   * @apiParam {string=ELIMINATION,QUALIFICATION} [competitions.organizingMode]
   *           The organization mode of the competition. All games of the competition which do not specify another
   *           organizing mode will use this organizing mode.
   * @apiParam {string=ONE_SET,BEST_OF_THREE,BEST_OF_FIVE} [competitions.scoreMode]
   *           The score mode of the competition. All games of the competition which do not specify another score mode
   *           will use this score mode.
   * @apiParam {string=DOUBLE,SINGLE,DYP} [competitions.teamMode]
   *           Specifies the team mode of the competition. If the partners were chosen randomly at some point the mode
   *           should be DYP. All games of the competition which do not specify another team mode will use this team
   *           mode.
   * @apiParam {string=MULTITABLE,GARLANDO,LEONHART,TORNADO,ROBERTO_SPORT,BONZINI} [competitions.table]
   *           On which sort of table the competition is played. Multitable should only be used if the table is not
   *           known anymore or if the game was really a multitable game, i.e. multiple sets on at least two different
   *           tables. All games of the competition which do not specify another table will use this table.
   * @apiParam {Object[]} competitions.teams list of teams which attended this competition, at least two teams must be
   *                                         given
   * @apiParam {integer{>=1}} competitions.teams.rank the rank of the team
   * @apiParam {integer{>=1}} competitions.teams.startNumber the start number of the team, this must be unique across
   *                                                         all teams of a competition
   * @apiParam {string[]} competitions.teams.players list of player ids of this team
   * @apiParam {string} [competitions.teams.name] the name of the team
   * @apiError ValidationException The userIdentifier or the name of the tournament are missing or one of the modes or
   *                               the given table is not in the list of valid options.
   * @apiSuccess {string} type the type of the successful operation either "create" or "update"
   */
  $router->post('createOrUpdateTournament', [
    'as' => 'createOrUpdateTournament', 'uses' => 'TournamentController@createOrUpdateTournament'
  ]);

  /**
   * @api {get} /searchPlayers Search for players in the database
   * @apiUse AuthenticatedRequest
   * @apiVersion 0.1.0
   * @apiDescription Searches in the database for the given players and returns possible candidates if they exist
   * @apiName GetSearchPlayers
   * @apiGroup Player
   *
   * @apiParam {Object[]} - list of players to search for
   * @apiParam {string{2..}} -.firstName the first name of the player to search
   * @apiParam {string{2..}} -.lastName the last name of the player to search
   * @apiParam {date} [-.birthday] the birthday of the player to search
   *
   * @apiError ValidationException A first name is missing or too short (at least 2 characters) or a last name is
   *                               missing or too short (at least 2 characters) or a given birthday does not represent
   *                               a valid date.
   * @apiSuccess {Object[]} - List of response results
   * @apiSuccess {array} -.search the original search array (see parameter section)
   * @apiSuccess {Object[]} -.found list of found players in the database corresponding to the given searched player
   * @apiSuccess {integer} -.found.id the id of the found player
   * @apiSuccess {string} -.found.firstName the first name of the found player
   * @apiSuccess {string} -.found.lastName the last name of the found player
   * @apiSuccess {date} -.found.birthday the birthday of the found player
   */
  $router->get('searchPlayers', [
    'as' => 'searchPlayers', 'uses' => 'PlayerController@searchPlayers'
  ]);

  /**
   * @api {post} /addPlayers Adds new players to the database
   * @apiUse AuthenticatedRequest
   * @apiVersion 0.1.0
   * @apiDescription Adds new players to the database. Checks if the players already exist and if they do returns an
   *                 error without adding any players.
   * @apiName PostAddPlayers
   * @apiGroup Player
   *
   * @apiParam {Object[]} - list of players to add
   * @apiParam {string{2..}} -.firstName the first name of the player to add
   * @apiParam {string{2..}} -.lastName the last name of the player to add
   * @apiParam {date} -.birthday the birthday of the player to add
   *
   * @apiError ValidationException A first name is missing or too short (at least 2 characters) or a last name is
   *                               missing or too short (at least 2 characters) or a birthday is missing or does not
   *                               represent a valid date.
   * @apiError PlayerAlreadyExists At least one of the given players already exist in the database. No players will be
   *                               added in this case. Resubmit with only non-existing players.
   *
   * @apiSuccess {Object[]} - List of added players
   * @apiSuccess {integer} -.id the id of the added player
   * @apiSuccess {string} -.firstName the first name of the added player
   * @apiSuccess {string} -.lastName the last name of the added player
   * @apiSuccess {date} -.birthday the birthday of the added player
   */
  $router->post('addPlayers', [
    'as' => 'addPlayers', 'uses' => 'PlayerController@addPlayers'
  ]);
});