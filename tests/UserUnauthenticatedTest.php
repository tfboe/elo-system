<?php

class UserUnauthenticatedTest extends DatabaseTestCase
{
//<editor-fold desc="Public Methods">
  public function testAuthenticationError()
  {
    $this->json('GET', '/getUserId')->seeStatusCode(401)->seeJsonEquals(
      ["status" => 401, "message" => "Not logged in!"]);
  }

  public function testGenerateId()
  {
    function test_com_create_guid()
    {
      return "{test-guid}";
    }

    $this->assertEquals(\App\Entity\Helpers\IdGenerator::create_id_from('test_com_create_guid'),
      'test-guid');
  }

  public function testInvalidPassword()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['unhashedPassword' => $password]);
    $property = self::getProperty(\App\Entity\User::class, 'id');
    $property->setValue($user, "\x84invalid");
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeStatusCode(401);
    $this->assertNull($this->response->headers->get('jwt-token'));
  }

  public function testLogin()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['unhashedPassword' => $password]);
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->seeJsonEquals(['id' => $user->getId()])->seeHeader('jwt-token');
    $this->assertNotNull($this->response->headers->get('jwt-token'));
    $this->json('GET', '/getUserId')->seeJson(['id' => $user->getId()]);
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

//<editor-fold desc="Private Methods">
  private function loginUser(\App\Entity\User $user, $password)
  {
    $this->json('POST', '/login', [
      'email' => $user->getEmail(),
      'password' => $password
    ])->response->headers->get('jwt-token');
  }
//</editor-fold desc="Private Methods">
}
