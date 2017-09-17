<?php
declare(strict_types=1);

/**
 * Class UserUnauthenticatedTest
 */
class UserUnauthenticatedTest extends DatabaseTestCase
{
//<editor-fold desc="Public Methods">
  public function testAuthenticationError()
  {
    $this->json('GET', '/userId')->seeStatusCode(401)->seeJsonEquals(
      ["status" => 401, "message" => "Not logged in!"]);
  }

  public function testGenerateId()
  {
    /**
     * test function for generating an guid
     * @return string fixed test string
     */
    function test_com_create_guid()
    {
      return "{test-guid}";
    }

    self::assertEquals(\App\Entity\Helpers\IdGenerator::createIdFrom('test_com_create_guid'),
      'test-guid');
  }

  public function testInvalidId()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['originalPassword' => $password]);
    $property = self::getProperty(\App\Entity\User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testInvalidCredentials()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['originalPassword' => $password]);
    $property = self::getProperty(\App\Entity\User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password . "wrong-password"
    ])->seeStatusCode(401);
    self::assertNull($this->response->headers->get('jwt-token'));
  }

  public function testLogin()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['originalPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeJsonEquals(['id' => $user->getId()])->seeHeader('jwt-token');
    self::assertNotNull($this->response->headers->get('jwt-token'));
    $this->json('GET', '/userId')->seeJson(['id' => $user->getId()]);
  }

  public function testRegisterUser()
  {
    $this->json('POST', '/register', [
      'email' => 'test@user1.com',
      'password' => 'testPassword',
      'repeatedPassword' => 'testPassword'
    ])->seeJsonStructure(['id']);
  }
//</editor-fold desc="Public Methods">
}
