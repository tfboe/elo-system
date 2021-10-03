<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20211003105937 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE elo_users ADD rights SMALLINT NOT NULL');
        $this->addSql('UPDATE elo_users SET rights = IF(admin = 1, 9, IF(activated = 1, 3, 0)) WHERE 1');
        $this->addSql('ALTER TABLE elo_users DROP activated, DROP admin');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE elo_users ADD activated TINYINT(1) NOT NULL, ADD admin TINYINT(1) NOT NULL');
        $this->addSql('UPDATE elo_users SET activated = IF(rights > 0, 1, 0), admin = IF(rights >= 9, 1, 0) WHERE 1');
        $this->addSql('ALTER TABLE elo_users DROP rights');

    }
}
