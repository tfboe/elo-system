<?php

namespace Database\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20211003103933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE failed_jobs');
        $this->addSql('DROP TABLE jobs');
        $this->addSql('ALTER TABLE elo_teams RENAME INDEX idx_96c222587b39d312 TO IDX_A77F36DA7B39D312');
        $this->addSql('ALTER TABLE elo_rankings RENAME INDEX idx_9d5da5e699091188 TO IDX_E492725D99091188');
        $this->addSql('ALTER TABLE elo_matches RENAME INDEX idx_62615ba99091188 TO IDX_15490AB399091188');
        $this->addSql('ALTER TABLE elo_qualificationSystems RENAME INDEX idx_bd25ae2086e2098c TO IDX_4B898E5086E2098C');
        $this->addSql('ALTER TABLE elo_qualificationSystems RENAME INDEX idx_bd25ae20a1135d66 TO IDX_4B898E50A1135D66');
        $this->addSql('ALTER TABLE elo_teamMemberships RENAME INDEX idx_5d544a1c296cd8ae TO IDX_D6FEDCD296CD8AE');
        $this->addSql('ALTER TABLE elo_teamMemberships RENAME INDEX idx_5d544a1c99e6f5df TO IDX_D6FEDCD99E6F5DF');
        $this->addSql('ALTER TABLE elo_competitions RENAME INDEX idx_a7dd463d33d1a3e7 TO IDX_80D3AE0A33D1A3E7');
        $this->addSql('ALTER TABLE elo_rankingSystemListEntry RENAME INDEX idx_e75c8e9155edec5f TO IDX_F30D210355EDEC5F');
        $this->addSql('ALTER TABLE elo_rankingSystemListEntry RENAME INDEX idx_e75c8e9199e6f5df TO IDX_F30D210399E6F5DF');
        $this->addSql('ALTER TABLE elo_phases RENAME INDEX idx_170969e57b39d312 TO IDX_148E36FD7B39D312');
        $this->addSql('ALTER TABLE elo_games RENAME INDEX idx_ff232b312abeacd6 TO IDX_CE9E3FB32ABEACD6');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges DROP FOREIGN KEY FK_96731022BF9F2E56');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges RENAME INDEX idx_96731022bf9f2e56 TO IDX_60DF3052BF9F2E56');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges RENAME INDEX idx_96731022cd8f5098 TO IDX_60DF3052CD8F5098');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges RENAME INDEX idx_9673102299e6f5df TO IDX_60DF305299E6F5DF');
        $this->addSql('ALTER TABLE elo_rankingSystemLists RENAME INDEX idx_38ac5a8dcd8f5098 TO IDX_E4AAC6D0CD8F5098');
        $this->addSql('ALTER TABLE elo_tournaments RENAME INDEX idx_e4bcfac361220ea6 TO IDX_E81567B261220EA6');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE failed_jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, connection TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, queue TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, payload LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, exception LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, failed_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE jobs (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, queue VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, payload LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, attempts TINYINT(1) NOT NULL, reserved_at INT UNSIGNED DEFAULT NULL, available_at INT UNSIGNED NOT NULL, created_at INT UNSIGNED NOT NULL, INDEX jobs_queue_index (queue), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE elo_competitions RENAME INDEX idx_80d3ae0a33d1a3e7 TO IDX_A7DD463D33D1A3E7');
        $this->addSql('ALTER TABLE elo_games RENAME INDEX idx_ce9e3fb32abeacd6 TO IDX_FF232B312ABEACD6');
        $this->addSql('ALTER TABLE elo_matches RENAME INDEX idx_15490ab399091188 TO IDX_62615BA99091188');
        $this->addSql('ALTER TABLE elo_phases RENAME INDEX idx_148e36fd7b39d312 TO IDX_170969E57B39D312');
        $this->addSql('ALTER TABLE elo_qualificationSystems RENAME INDEX idx_4b898e50a1135d66 TO IDX_BD25AE20A1135D66');
        $this->addSql('ALTER TABLE elo_qualificationSystems RENAME INDEX idx_4b898e5086e2098c TO IDX_BD25AE2086E2098C');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges ADD CONSTRAINT FK_96731022BF9F2E56 FOREIGN KEY (hierarchy_entity_id) REFERENCES tournament_hierarchy_entities (id)');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges RENAME INDEX idx_60df3052cd8f5098 TO IDX_96731022CD8F5098');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges RENAME INDEX idx_60df3052bf9f2e56 TO IDX_96731022BF9F2E56');
        $this->addSql('ALTER TABLE elo_rankingSystemChanges RENAME INDEX idx_60df305299e6f5df TO IDX_9673102299E6F5DF');
        $this->addSql('ALTER TABLE elo_rankingSystemListEntry RENAME INDEX idx_f30d210399e6f5df TO IDX_E75C8E9199E6F5DF');
        $this->addSql('ALTER TABLE elo_rankingSystemListEntry RENAME INDEX idx_f30d210355edec5f TO IDX_E75C8E9155EDEC5F');
        $this->addSql('ALTER TABLE elo_rankingSystemLists RENAME INDEX idx_e4aac6d0cd8f5098 TO IDX_38AC5A8DCD8F5098');
        $this->addSql('ALTER TABLE elo_rankings RENAME INDEX idx_e492725d99091188 TO IDX_9D5DA5E699091188');
        $this->addSql('ALTER TABLE elo_teamMemberships RENAME INDEX idx_d6fedcd99e6f5df TO IDX_5D544A1C99E6F5DF');
        $this->addSql('ALTER TABLE elo_teamMemberships RENAME INDEX idx_d6fedcd296cd8ae TO IDX_5D544A1C296CD8AE');
        $this->addSql('ALTER TABLE elo_teams RENAME INDEX idx_a77f36da7b39d312 TO IDX_96C222587B39D312');
        $this->addSql('ALTER TABLE elo_tournaments RENAME INDEX idx_e81567b261220ea6 TO IDX_E4BCFAC361220EA6');
    }
}
