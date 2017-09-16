<?php

namespace App\Http\Controllers;

use App\Entity\User;
use App\Exceptions\AuthenticationException;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Application;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends BaseController
{
  /**
   * register action, registers a new user with email and password
   *
   * @param Request $request the http request
   *
   * @param Application $app
   * @return JsonResponse
   */
  public function register(Request $request, Application $app): JsonResponse
  {
    $userSpecification = $this->getCredentialSpecification($app);
    $userSpecification['email']['validation'] .= '|unique:App\Entity\User,email';
    $userSpecification['repeatedPassword'] = ['validation' => 'required|same:password', 'ignore' => true];
    $userSpecification['lastConfirmedAGBVersion'] = ['validation' => 'integer|min:0'];

    $this->validateBySpecification($request, $userSpecification);

    $input = $request->input();
    /** @var User $user */
    $user = $this->setFromSpecification(new User(), $userSpecification, $input);

    $this->em->persist($user);
    $this->em->flush();

    return response()->json(['id' => $user->getId()]);
  }

  /**
   * login action, checks credentials and returns token
   * @param Request $request the http request
   * @param Application $app
   * @return JsonResponse
   */
  public function login(Request $request, Application $app): JsonResponse
  {
    $userSpecification = $this->getCredentialSpecification($app);
    $userSpecification['email']['validation'] .= '|exists:App\Entity\User,email';
    $this->validateBySpecification($request, $userSpecification);


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

  public function getUserId(): JsonResponse
  {
    return response()->json(['id' => \Auth::user()->getId()]);
  }

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
      'password' => ['validation' => 'required|min:8',
        'transformer' => function ($x) use ($app) {
          return $app['hash']->make($x);
        }]
    ];
  }
}
