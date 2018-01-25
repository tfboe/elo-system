<?php
declare(strict_types=1);

namespace Tests\Integration;

use Tfboe\FmLib\Entity\User;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\DatabaseTestCase;

/**
 * Class UserUnauthenticatedTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserUnauthenticatedTest extends DatabaseTestCase
{
//<editor-fold desc="Public Methods">
  public function testAuthenticationError()
  {
    $this->json('GET', '/userId')->seeStatusCode(401)->seeJsonEquals(
      ["status" => 401, "message" => "Not logged in!",
        "name" => "AuthenticationException"]);
  }

  public function testCannotRecognizeExistingUsername()
  {
    $password = $this->newPassword();
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ]);
    $headers1 = $this->response->headers->all();
    if (array_key_exists("date", $headers1)) {
      unset($headers1["date"]);
    }
    $content1 = $this->response->content();

    $this->json('POST', '/login', [
      'email' => $user->getEmail() . "wrong-email",
      'password' => $password . "wrong-password"
    ]);

    $headers2 = $this->response->headers->all();
    if (array_key_exists("date", $headers2)) {
      unset($headers2["date"]);
    }
    $content2 = $this->response->content();
    self::assertEquals(json_encode($headers1), json_encode($headers2));
    self::assertEquals($content1, $content2);
  }

  public function testDoubleEmail()
  {
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/register', [
      'email' => $user->getEmail(),
      'password' => 'testPassword2'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email has already been taken."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testEmailRequiredValidation()
  {
    $this->json('POST', '/register', [
      'password' => 'testPassword'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testEmailRequiredValidationLogin()
  {
    entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'password' => 'testPassword'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidCredentials()
  {
    $password = $this->newPassword();
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    /** @noinspection PhpUnhandledExceptionInspection */
    $property = self::getProperty(User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testInvalidEmailValidation()
  {
    $this->json('POST', '/register', [
      'email' => 'invalidEmail',
      'password' => 'testPassword'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email must be a valid email address."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidEmailValidationLogin()
  {
    entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => 'invalidEmail',
      'password' => 'testPassword'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["email" => ["The email must be a valid email address."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testInvalidId()
  {
    $password = $this->newPassword();
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    /** @noinspection PhpUnhandledExceptionInspection */
    $property = self::getProperty(User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testInvalidLastConfirmedAGBVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedAGBVersion' => 'noInt',
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["confirmedAGBVersion" =>
      ["The confirmed a g b version must be an integer."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testLogin()
  {
    $password = $this->newPassword();
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeJsonEquals(['id' => $user->getId()])->seeHeader('jwt-token');
    self::assertNotNull($this->response->headers->get('jwt-token'));
  }

  public function testMultipleValidationErrors()
  {
    $this->json('POST', '/register', [
      'password' => 5
    ])->seeStatusCode(422)->seeJsonEquals(
      ["errors" => [
        "email" => ["The email field is required."],
        "password" => ["The password must be a string.", "The password must be at least 8 characters."]
      ], "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testNegativeLastConfirmedAGBVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedAGBVersion' => -1,
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["confirmedAGBVersion" =>
      ["The confirmed a g b version must be at least 0."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testNoStringPassword()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 16511233
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password must be a string."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testNoStringPasswordLogin()
  {
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => 16511233
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password must be a string."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testPasswordRequiredValidation()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testPasswordRequiredValidationLogin()
  {
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => $user->getEmail()
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => ["password" => ["The password field is required."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testRegisterUser()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword'
    ])->seeJsonStructure(['id']);
  }

  public function testRegistrationWithLastConfirmedAGBVersion()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'confirmedAGBVersion' => 5
    ])->seeJsonStructure(['id']);
    $result = json_decode($this->response->getContent(), true);
    /** @var \Tfboe\FmLib\Entity\User $user */
    /** @noinspection PhpUndefinedMethodInspection */
    $user = EntityManager::find(User::class, $result['id']);
    self::assertEquals(5, $user->getConfirmedAGBVersion());
  }

  public function testTooShortPassword()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'short'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" =>
      ["password" => ["The password must be at least 8 characters."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testTooShortPasswordLogin()
  {
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => 'testPassword']);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => 'short'
    ])->seeStatusCode(422)->seeJsonEquals(["errors" => [
      "password" => ["The password must be at least 8 characters."]],
      "message" => "The given data was invalid.", "name" => "ValidationException", "status" => 422]);
  }

  public function testWrongPassword()
  {
    $password = $this->newPassword();
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testWrongUsername()
  {
    $password = $this->newPassword();
    /** @var \Tfboe\FmLib\Entity\User $user */
    $user = entity(User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail() . "wrong-email",
      'password' => $password
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }
//</editor-fold desc="Public Methods">
}
