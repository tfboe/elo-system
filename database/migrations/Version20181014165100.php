<?php

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema as Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20181014165100 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('DROP TABLE jobs');
    $this->addSql('DROP TABLE failed_jobs');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, queue VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, payload LONGTEXT NOT NULL COLLATE utf8_unicode_ci, attempts TINYINT(1) NOT NULL, reserved_at INT UNSIGNED DEFAULT NULL, available_at INT UNSIGNED NOT NULL, created_at INT UNSIGNED NOT NULL, INDEX jobs_queue_index (queue), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE failed_jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, `connection` TEXT NOT NULL COLLATE utf8_unicode_ci, queue TEXT NOT NULL COLLATE utf8_unicode_ci, payload LONGTEXT NOT NULL COLLATE utf8_unicode_ci, exception LONGTEXT NOT NULL COLLATE utf8_unicode_ci, failed_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
  }
//</editor-fold desc="Public Methods">
}
