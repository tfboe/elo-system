<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180112115717 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems DROP FOREIGN KEY FK_750EA8FCD07762EB');
    $this->addSql('ALTER TABLE tournaments DROP FOREIGN KEY FK_E4BCFAC3BF396750');
    $this->addSql('ALTER TABLE competitions DROP FOREIGN KEY FK_A7DD463DBF396750');
    $this->addSql('ALTER TABLE phases DROP FOREIGN KEY FK_170969E5BF396750');
    $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B31BF396750');
    $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BABF396750');
    $this->addSql('CREATE TABLE relation__competition_ranking_systems (competition_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_CE1B94147B39D312 (competition_id), INDEX IDX_CE1B9414CD8F5098 (ranking_system_id), PRIMARY KEY(competition_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__game_ranking_systems (game_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_8855591CE48FD905 (game_id), INDEX IDX_8855591CCD8F5098 (ranking_system_id), PRIMARY KEY(game_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__match_ranking_systems (match_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_936483DF2ABEACD6 (match_id), INDEX IDX_936483DFCD8F5098 (ranking_system_id), PRIMARY KEY(match_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__phase_ranking_systems (phase_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_9938BB2E99091188 (phase_id), INDEX IDX_9938BB2ECD8F5098 (ranking_system_id), PRIMARY KEY(phase_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__tournament_ranking_systems (tournament_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_576CD37433D1A3E7 (tournament_id), INDEX IDX_576CD374CD8F5098 (ranking_system_id), PRIMARY KEY(tournament_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE relation__competition_ranking_systems ADD CONSTRAINT FK_CE1B94147B39D312 FOREIGN KEY (competition_id) REFERENCES competitions (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__competition_ranking_systems ADD CONSTRAINT FK_CE1B9414CD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_ranking_systems ADD CONSTRAINT FK_8855591CCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_ranking_systems ADD CONSTRAINT FK_8855591CE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_ranking_systems ADD CONSTRAINT FK_936483DF2ABEACD6 FOREIGN KEY (match_id) REFERENCES matches (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_ranking_systems ADD CONSTRAINT FK_936483DFCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__phase_ranking_systems ADD CONSTRAINT FK_9938BB2E99091188 FOREIGN KEY (phase_id) REFERENCES phases (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__phase_ranking_systems ADD CONSTRAINT FK_9938BB2ECD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__tournament_ranking_systems ADD CONSTRAINT FK_576CD37433D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__tournament_ranking_systems ADD CONSTRAINT FK_576CD374CD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('DROP TABLE tournament_hierarchy_entities');
    $this->addSql('DROP TABLE relation__hierarchy_entities_ranking_systems');
    $this->addSql('ALTER TABLE competitions ADD game_mode SMALLINT DEFAULT NULL, ADD team_mode SMALLINT DEFAULT NULL, ADD organizing_mode SMALLINT DEFAULT NULL, ADD score_mode SMALLINT DEFAULT NULL, ADD table_id SMALLINT DEFAULT NULL, ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL, ADD start_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD end_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    $this->addSql('ALTER TABLE games ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL, ADD game_mode SMALLINT DEFAULT NULL, ADD team_mode SMALLINT DEFAULT NULL, ADD organizing_mode SMALLINT DEFAULT NULL, ADD score_mode SMALLINT DEFAULT NULL, ADD table_id SMALLINT DEFAULT NULL, ADD start_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD end_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    $this->addSql('ALTER TABLE matches ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL, ADD game_mode SMALLINT DEFAULT NULL, ADD team_mode SMALLINT DEFAULT NULL, ADD organizing_mode SMALLINT DEFAULT NULL, ADD score_mode SMALLINT DEFAULT NULL, ADD table_id SMALLINT DEFAULT NULL, ADD start_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD end_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    $this->addSql('ALTER TABLE phases ADD game_mode SMALLINT DEFAULT NULL, ADD team_mode SMALLINT DEFAULT NULL, ADD organizing_mode SMALLINT DEFAULT NULL, ADD score_mode SMALLINT DEFAULT NULL, ADD table_id SMALLINT DEFAULT NULL, ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL, ADD start_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD end_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    $this->addSql('ALTER TABLE tournaments ADD game_mode SMALLINT DEFAULT NULL, ADD team_mode SMALLINT DEFAULT NULL, ADD organizing_mode SMALLINT DEFAULT NULL, ADD score_mode SMALLINT DEFAULT NULL, ADD table_id SMALLINT DEFAULT NULL, ADD start_time DATETIME DEFAULT NULL, ADD end_time DATETIME DEFAULT NULL, ADD start_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD end_timezone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE tournament_hierarchy_entities (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, start_timezone VARCHAR(255) NOT NULL, end_timezone VARCHAR(255) NOT NULL, discriminator VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__hierarchy_entities_ranking_systems (tournament_hierarchy_entity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_750EA8FCD07762EB (tournament_hierarchy_entity_id), INDEX IDX_750EA8FCCD8F5098 (ranking_system_id), PRIMARY KEY(tournament_hierarchy_entity_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems ADD CONSTRAINT FK_750EA8FCD07762EB FOREIGN KEY (tournament_hierarchy_entity_id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__hierarchy_entities_ranking_systems ADD CONSTRAINT FK_750EA8FCCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('DROP TABLE relation__competition_ranking_systems');
    $this->addSql('DROP TABLE relation__game_ranking_systems');
    $this->addSql('DROP TABLE relation__match_ranking_systems');
    $this->addSql('DROP TABLE relation__phase_ranking_systems');
    $this->addSql('DROP TABLE relation__tournament_ranking_systems');
    $this->addSql('ALTER TABLE tournaments DROP game_mode, DROP team_mode, DROP organizing_mode, DROP score_mode, DROP table_id, DROP start_time, DROP end_time, DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE tournaments ADD CONSTRAINT FK_E4BCFAC3BF396750 FOREIGN KEY (id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE competitions DROP game_mode, DROP team_mode, DROP organizing_mode, DROP score_mode, DROP table_id, DROP start_time, DROP end_time, DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE competitions ADD CONSTRAINT FK_A7DD463DBF396750 FOREIGN KEY (id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE phases DROP game_mode, DROP team_mode, DROP organizing_mode, DROP score_mode, DROP table_id, DROP start_time, DROP end_time, DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE phases ADD CONSTRAINT FK_170969E5BF396750 FOREIGN KEY (id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE games DROP start_time, DROP end_time, DROP game_mode, DROP team_mode, DROP organizing_mode, DROP score_mode, DROP table_id, DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B31BF396750 FOREIGN KEY (id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE matches DROP start_time, DROP end_time, DROP game_mode, DROP team_mode, DROP organizing_mode, DROP score_mode, DROP table_id, DROP start_timezone, DROP end_timezone');
    $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BABF396750 FOREIGN KEY (id) REFERENCES tournament_hierarchy_entities (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
