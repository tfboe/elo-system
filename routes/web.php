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
 * @apiParam {integer{>=0}} confirmedAGBVersion confirmed AGB version
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
   * @api {post} /createOrReplaceTournament Create or Replace a Tournament
   * @apiUse AuthenticatedRequest
   * @apiVersion 0.1.0
   * @apiDescription Creates a new tournament or if the tournament already exists replaces it
   * @apiName PostCreateOrReplaceTournament
   * @apiGroup Tournament
   *
   * @apiParam {string} userIdentifier a identifier which identifies the tournament uniquely across all tournaments of
   *                                   this user
   * @apiParam {string} name the name of the tournament
   * @apiParam {string} [tournamentListId="''"] the list id of which the tournament is part of
   * @apiParam {string} [startTime] the start time of the tournament in the format 'YYYY-MM-DD HH:MM:SS e'
   *                                where e is a timezone (for example Europe/Vienna)
   * @apiParam {string} [endTime] the end time of the tournament in the format 'YYYY-MM-DD HH:MM:SS e'
   *                              where e is a timezone (for example Europe/Vienna)
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
   * @apiParam {string} [competitions.startTime] the start time of the competition in the format 'YYYY-MM-DD HH:MM:SS e'
   *                                             where e is a timezone (for example Europe/Vienna)
   * @apiParam {string} [competitions.endTime] the end time of the competition in the format 'YYYY-MM-DD HH:MM:SS e'
   *                                           where e is a timezone (for example Europe/Vienna)
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
   * @apiParam {string} [competitions.teams.name="''"] the name of the team
   * @apiParam {Object[]} competitions.phases list of phases of this competition
   * @apiParam {integer{>=1}} competitions.phases.phaseNumber the number of the phase
   * @apiParam {string} [competitions.phases.name="''"] the name of the phase
   * @apiParam {string} [competitions.phases.startTime] the start time of the phase in the format
   *                                                    'YYYY-MM-DD HH:MM:SS e' where e is a timezone
   *                                                    (for example Europe/Vienna)
   * @apiParam {string} [competitions.phases.endTime] the end time of the match in the format
   *                                                  'YYYY-MM-DD HH:MM:SS e' where e is a timezone
   *                                                  (for example Europe/Vienna)
   * @apiParam {string[]} [competitions.phases.nextPhaseNumbers=[]] the list of next phase numbers (direct successor
   *                                                                phases)
   * @apiParam {string=OFFICIAL,SPEEDBALL,CLASSIC} [competitions.phases.gameMode]
   *           The rule mode of the phase. All games of the phase which do not specify another game mode
   *           will use this game mode.
   * @apiParam {string=ELIMINATION,QUALIFICATION} [competitions.phases.organizingMode]
   *           The organization mode of the phase. All games of the phase which do not specify another
   *           organizing mode will use this organizing mode.
   * @apiParam {string=ONE_SET,BEST_OF_THREE,BEST_OF_FIVE} [competitions.phases.scoreMode]
   *           The score mode of the phase. All games of the phase which do not specify another score mode
   *           will use this score mode.
   * @apiParam {string=DOUBLE,SINGLE,DYP} [competitions.phases.teamMode]
   *           Specifies the team mode of the phase. If the partners were chosen randomly at some point the mode
   *           should be DYP. All games of the phase which do not specify another team mode will use this team
   *           mode.
   * @apiParam {string=MULTITABLE,GARLANDO,LEONHART,TORNADO,ROBERTO_SPORT,BONZINI} [competitions.phases.table]
   *           On which sort of table the phase is played. Multitable should only be used if the table is not
   *           known anymore or if the game was really a multitable game, i.e. multiple sets on at least two different
   *           tables. All games of the phase which do not specify another table will use this table.
   * @apiParam {Object[]{>=2}} competitions.phases.rankings list of rankings of the phase
   * @apiParam {integer{>=1}} competitions.phases.rankings.rank the rank of the ranking
   * @apiParam {integer{>=1}} competitions.phases.rankings.uniqueRank the unique rank of the ranking
   * @apiParam {integer[]} competitions.phases.rankings.teamStartNumbers list of the start numbers of the teams
   *                                                                            corresponding to this ranking
   * @apiParam {string} [competitions.phases.rankings.name] the name of the ranking
   * @apiParam {Object[]{>=1}} competitions.phases.matches list of matches of the phase
   * @apiParam {integer{>=1}} competitions.phases.matches.matchNumber match number which is unique in this phase
   * @apiParam {integer[]{>=1}} competitions.phases.matches.rankingsAUniqueRanks list of the unique ranks for each
   *                                                                             ranking playing for team A in this
   *                                                                             match
   * @apiParam {integer[]{>=1}} competitions.phases.matches.rankingsBUniqueRanks list of the unique ranks for each
   *                                                                             ranking playing for team B in this
   *                                                                             match
   * @apiParam {integer{>=0}} competitions.phases.matches.resultA the points of team A for this match
   * @apiParam {integer{>=0}} competitions.phases.matches.resultB the points of team B for this match
   * @apiParam {string=TEAM_A_WINS,TEAM_B_WINS,DRAW,NOT_YET_FINISHED,NULLED} competitions.phases.matches.result
   *           the result of the match
   * @apiParam {boolean} competitions.phases.matches.played indicates if this match was played or forfeit
   * @apiParam {string} [competitions.phases.matches.startTime] the start time of the match in the format
   *                                                            'YYYY-MM-DD HH:MM:SS e' where e is a timezone
   *                                                            (for example Europe/Vienna)
   * @apiParam {string} [competitions.phases.matches.endTime] the end time of the match in the format
   *                                                          'YYYY-MM-DD HH:MM:SS e' where e is a timezone
   *                                                          (for example Europe/Vienna)
   * @apiParam {string=OFFICIAL,SPEEDBALL,CLASSIC} [competitions.phases.matches.gameMode]
   *           The rule mode of the match. All games of the match which do not specify another game mode
   *           will use this game mode.
   * @apiParam {string=ELIMINATION,QUALIFICATION} [competitions.phases.matches.organizingMode]
   *           The organization mode of the match. All games of the match which do not specify another
   *           organizing mode will use this organizing mode.
   * @apiParam {string=ONE_SET,BEST_OF_THREE,BEST_OF_FIVE} [competitions.phases.matches.scoreMode]
   *           The score mode of the match. All games of the match which do not specify another score mode
   *           will use this score mode.
   * @apiParam {string=DOUBLE,SINGLE,DYP} [competitions.phases.matches.teamMode]
   *           Specifies the team mode of the match. If the partners were chosen randomly at some point the mode
   *           should be DYP. All games of the match which do not specify another team mode will use this team
   *           mode.
   * @apiParam {string=MULTITABLE,GARLANDO,LEONHART,TORNADO,ROBERTO_SPORT,BONZINI} [competitions.phases.matches.table]
   *           On which sort of table the match is played. Multitable should only be used if the table is not
   *           known anymore or if the game was really a multitable game, i.e. multiple sets on at least two different
   *           tables. All games of the match which do not specify another table will use this table.
   * @apiParam {Object[]{>=1}} competitions.phases.matches.games list of games of the match
   * @apiParam {integer{>=1}} competitions.phases.matches.games.gameNumber game number which is unique in this match
   * @apiParam {integer[]{>=1}} competitions.phases.matches.games.playersA list of the player ids for each player
   *                                                                       playing for team A in this game
   * @apiParam {integer[]{>=1}} competitions.phases.matches.games.playersB list of the player ids for each player
   *                                                                       playing for team A in this game
   * @apiParam {integer{>=0}} competitions.phases.matches.games.resultA the points of team A for this game
   * @apiParam {integer{>=0}} competitions.phases.matches.games.resultB the points of team B for this game
   * @apiParam {string=TEAM_A_WINS,TEAM_B_WINS,DRAW,NOT_YET_FINISHED,NULLED} competitions.phases.matches.games.result
   *           the result of the game
   * @apiParam {boolean} competitions.phases.matches.games.played indicates if this game was played or forfeit
   * @apiParam {string} [competitions.phases.matches.games.startTime] the start time of the game in the format
   *                                                                  'YYYY-MM-DD HH:MM:SS e' where e is a timezone
   *                                                                  (for example Europe/Vienna)
   * @apiParam {string} [competitions.phases.matches.games.endTime] the end time of the game in the format
   *                                                                'YYYY-MM-DD HH:MM:SS e' where e is a timezone
   *                                                                (for example Europe/Vienna)
   * @apiParam {string=OFFICIAL,SPEEDBALL,CLASSIC} [competitions.phases.matches.games.gameMode]
   *           The rule mode of the game.
   * @apiParam {string=ELIMINATION,QUALIFICATION} [competitions.phases.matches.games.organizingMode]
   *           The organization mode of the game.
   * @apiParam {string=ONE_SET,BEST_OF_THREE,BEST_OF_FIVE} [competitions.phases.matches.games.scoreMode]
   *           The score mode of the game.
   * @apiParam {string=DOUBLE,SINGLE,DYP} [competitions.phases.matches.games.teamMode]
   *           Specifies the team mode of the game. If the partners were chosen randomly at some point the mode
   *           should be DYP.
   * @apiParam
   * {string=MULTITABLE,GARLANDO,LEONHART,TORNADO,ROBERTO_SPORT,BONZINI} [competitions.phases.matches.games.table]
   *           On which sort of table the game is played. Multitable should only be used if the table is not
   *           known anymore or if the game was really a multitable game, i.e. multiple sets on at least two different
   *           tables.
   * @apiError ValidationException The userIdentifier or the name of the tournament are missing or one of the modes or
   *                               the given table is not in the list of valid options.
   * @apiError DuplicateException Two competitions have the same name or a team start number is occurring twice or
   *                              a player is specified twice for a team or two phases of a competition have the same
   *                              phase number or a unique rank is occurring twice or
   *                              a team start number is specified twice for a ranking or a match number is occurring
   *                              twice in a phase or a unique rank is specified twice for the two teams in a match in a
   *                              phase or a game number is occurring twice or a player id is specified twice for the
   *                              players of a game.
   * @apiError ReferenceException A referenced phase number in the next phases array does not exist or a referenced team
   *                              start number in the team values does not exist or a referenced unique rank in the
   *                              rankings lists of a match does not exist in the rankings of the corresponding phase or
   *                              a player of a team is not in the players lists of this team.
   * @apiError UnorderedPhaseNumberException A successor phase has a higher phase number than a predecessor phase
   * @apiSuccess {string} type the type of the successful operation either "create" or "replace"
   */
  $router->post('createOrReplaceTournament', [
    'as' => 'createOrReplaceTournament', 'uses' => 'TournamentController@createOrReplaceTournament'
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