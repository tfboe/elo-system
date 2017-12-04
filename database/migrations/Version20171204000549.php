<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171204000549 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE groups (id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', phase_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', group_number INT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, INDEX IDX_F06D397099091188 (phase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D397099091188 FOREIGN KEY (phase_id) REFERENCES phases (id)');
    $this->addSql('DROP TABLE qualificationSystems');
    $this->addSql('ALTER TABLE rankings DROP FOREIGN KEY FK_9D5DA5E699091188');
    $this->addSql('DROP INDEX IDX_9D5DA5E699091188 ON rankings');
    $this->addSql('ALTER TABLE rankings CHANGE phase_id group_id CHAR(36) DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE rankings ADD CONSTRAINT FK_9D5DA5E6FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)');
    $this->addSql('CREATE INDEX IDX_9D5DA5E6FE54D947 ON rankings (group_id)');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE rankings DROP FOREIGN KEY FK_9D5DA5E6FE54D947');
    $this->addSql('CREATE TABLE qualificationSystems (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', previous_phase_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', next_phase_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_BD25AE2086E2098C (previous_phase_id), INDEX IDX_BD25AE20A1135D66 (next_phase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE qualificationSystems ADD CONSTRAINT FK_BD25AE2086E2098C FOREIGN KEY (previous_phase_id) REFERENCES phases (id)');
    $this->addSql('ALTER TABLE qualificationSystems ADD CONSTRAINT FK_BD25AE20A1135D66 FOREIGN KEY (next_phase_id) REFERENCES phases (id)');
    $this->addSql('DROP TABLE groups');
    $this->addSql('DROP INDEX IDX_9D5DA5E6FE54D947 ON rankings');
    $this->addSql('ALTER TABLE rankings CHANGE group_id phase_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
    $this->addSql('ALTER TABLE rankings ADD CONSTRAINT FK_9D5DA5E699091188 FOREIGN KEY (phase_id) REFERENCES phases (id)');
    $this->addSql('CREATE INDEX IDX_9D5DA5E699091188 ON rankings (phase_id)');
  }
//</editor-fold desc="Public Methods">
}
