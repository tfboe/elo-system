<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\User;
use App\Exceptions\AuthenticationException;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends BaseController
{
//<editor-fold desc="Public Methods">
  /**
   * login action, checks credentials and returns token
   * @param Request $request the http request
   * @param Application $app
   * @return JsonResponse
   * @throws AuthenticationException wrong credentials or errors during creating a token
   */
  public function login(Request $request, Application $app): JsonResponse
  {
    $this->validateBySpecification($request, $this->getCredentialSpecification($app));


    // grab credentials from the request
    $credentials = $request->only('email', 'password');

    $token = null;
    try {
      // attempt to verify the credentials and create a token for the user
      $token = \Auth::attempt($credentials);
      if (!$token) {
        throw new AuthenticationException('invalid credentials');
      }
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      throw new AuthenticationException('could not create token');
    }
    $user = $request->user();
    return response()->json(['id' => $user->getId()], 200, ['jwt-token' => $token]);
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * register action, registers a new user with email and password
   *
   * @param Request $request the http request
   * @param Application $app
   * @return JsonResponse
   */
  public function register(Request $request, Application $app): JsonResponse
  {
    $userSpecification = $this->getCredentialSpecification($app);
    $userSpecification['email']['validation'] .= '|unique:App\Entity\User,email';
    $userSpecification['confirmedAGBVersion'] = ['validation' => 'integer|min:0'];

    $this->validateBySpecification($request, $userSpecification);

    $input = $request->input();
    /** @var User $user */
    $user = $this->setFromSpecification(new User(), $userSpecification, $input);

    $this->entityManager->persist($user); //sets the user id
    $this->entityManager->flush();

    /** @noinspection PhpUnhandledExceptionInspection */
    return response()->json(['id' => $user->getId()]);
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @return JsonResponse
   */
  public function userId(): JsonResponse
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    return response()->json(['id' => \Auth::user()->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets the specification for the login credentials
   * @param Application $app
   * @return array
   */
  private function getCredentialSpecification(Application $app)
  {
    /** @var Hasher $hasher */
    return [
      'email' => ['validation' => 'required|email'],
      'password' => ['validation' => 'required|string|min:8',
        'transformer' => function ($value) use ($app) {
          return $app['hash']->make($value);
        }]
    ];
  }
//</editor-fold desc="Private Methods">
}
