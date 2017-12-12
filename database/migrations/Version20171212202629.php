<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171212202629 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE players DROP created_at, DROP updated_at');
    $this->addSql('ALTER TABLE tournaments DROP created_at, DROP updated_at');
    $this->addSql('ALTER TABLE users DROP created_at, DROP updated_at');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE tournaments ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    $this->addSql('ALTER TABLE players ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
    $this->addSql('ALTER TABLE users ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
  }
//</editor-fold desc="Public Methods">
}
