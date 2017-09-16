<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

class AuthenticatedTestCase extends DatabaseTestCase
{
//<editor-fold desc="Fields">
  /**
   * Authentication token if logged in
   * @var string
   */
  protected $token;

  /**
   * User corresponding to authentication token if logged in
   * @var \App\Entity\User
   */
  protected $user;
//</editor-fold desc="Fields">

//<editor-fold desc="Protected Methods">
  protected function json_auth(string $method, string $uri, array $data = [], array $headers = [])
  {
    $headers['Authorization'] = 'Bearer ' . $this->token;
    return $this->json($method, $uri, $data, $headers);
  }

  protected function workOnDatabaseDestroy()
  {
    $connection = \LaravelDoctrine\ORM\Facades\EntityManager::getConnection();
    $sql = sprintf('TRUNCATE TABLE %s', "users");
    $connection->query($sql);
    parent::workOnDatabaseDestroy();
  }

  protected function workOnDatabaseSetUp()
  {
    parent::workOnDatabaseSetUp();
    $password = $this->newPassword();
    $this->user = entity(\App\Entity\User::class)->create(['unhashedPassword' => $password]);
    $this->token = \Auth::attempt(['email' => $this->user->getEmail(), 'password' => $password]);
  }
//</editor-fold desc="Protected Methods">
}