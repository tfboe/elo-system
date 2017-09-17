<?php
declare(strict_types=1);

/**
 * Class DatabaseTestCase
 */
abstract class DatabaseTestCase extends TestCase
{
//<editor-fold desc="Fields">
  /**
   * @var \Faker\Generator
   */
  protected $faker;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * DatabaseTestCase constructor.
   * @param string|null $name test name
   * @param array $data test data
   * @param string $dataName test data name
   */
  public function __construct($name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName);
    $this->faker = Faker\Factory::create();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Protected Methods">

  /**
   * Clears the database by truncating all tables (very time consuming)
   */
  protected function clearDatabase()
  {
    /** @var \Doctrine\DBAL\Connection $connection */
    /** @noinspection PhpUndefinedMethodInspection */
    $connection = \LaravelDoctrine\ORM\Facades\EntityManager::getConnection();
    $connection->query(sprintf('SET FOREIGN_KEY_CHECKS = 0;'));
    $tables = $connection->getSchemaManager()->listTables();
    foreach ($tables as $table) {
      $sql = sprintf('TRUNCATE TABLE %s', $table->getName());
      $connection->query($sql);
    }
    $connection->query(sprintf('SET FOREIGN_KEY_CHECKS = 1;'));
  }

  /**
   * Creates a new user
   * @return array containing the password and the user object
   */
  protected function createUser()
  {
    $password = $this->newPassword();
    /** @var \App\Entity\User $user */
    $user = entity(\App\Entity\User::class)->create(['originalPassword' => $password]);
    return [
      'password' => $password,
      'user' => $user
    ];
  }

  /**
   * Uses faker to generate a new password
   * @return string the new password
   */
  protected function newPassword()
  {
    return $this->faker->password(8, 30);
  }

  /**
   * Boot the testing helper traits.
   *
   * @param bool $clear true if clearing the database should be used instead of transactions
   * @return void
   */
  protected function setUpTraits(bool $clear = false)
  {
    parent::setUpTraits();
    if ($clear) {
      $this->clearDatabase();
      $this->workOnDatabaseSetUp();
    } else {
      $this->workOnDatabaseSetUp();
      /** @noinspection PhpUndefinedMethodInspection */
      LaravelDoctrine\ORM\Facades\EntityManager::beginTransaction();
    }

    $this->beforeApplicationDestroyed(function () use ($clear) {
      if ($clear) {
        $this->workOnDatabaseDestroy();
        $this->clearDatabase();
      } else {
        /** @noinspection PhpUndefinedMethodInspection */
        LaravelDoctrine\ORM\Facades\EntityManager::rollback();
        $this->workOnDatabaseDestroy();
      }
    });
  }

  protected function workOnDatabaseDestroy()
  {

  }

  protected function workOnDatabaseSetUp()
  {

  }
//</editor-fold desc="Protected Methods">
}