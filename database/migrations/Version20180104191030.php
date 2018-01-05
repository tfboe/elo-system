<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180104191030 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__tournament_ranking_systems DROP FOREIGN KEY FK_576CD374CD8F5098');
    $this->addSql('ALTER TABLE relation__competition_ranking_systems DROP FOREIGN KEY FK_CE1B9414CD8F5098');
    $this->addSql('ALTER TABLE relation__phase_ranking_systems DROP FOREIGN KEY FK_9938BB2ECD8F5098');
    $this->addSql('ALTER TABLE relation__game_ranking_systems DROP FOREIGN KEY FK_8855591CCD8F5098');
    $this->addSql('ALTER TABLE relation__match_ranking_systems DROP FOREIGN KEY FK_936483DFCD8F5098');
    $this->addSql('DROP TABLE relation__tournament_ranking_systems');
    $this->addSql('DROP TABLE relation__competition_ranking_systems');
    $this->addSql('DROP TABLE rankingSystems');
    $this->addSql('DROP TABLE relation__phase_ranking_systems');
    $this->addSql('DROP TABLE relation__game_ranking_systems');
    $this->addSql('DROP TABLE relation__match_ranking_systems');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE relation__tournament_ranking_systems (tournament_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_576CD37433D1A3E7 (tournament_id), INDEX IDX_576CD374CD8F5098 (ranking_system_id), PRIMARY KEY(tournament_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__competition_ranking_systems (competition_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_CE1B94147B39D312 (competition_id), INDEX IDX_CE1B9414CD8F5098 (ranking_system_id), PRIMARY KEY(competition_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE rankingSystems (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', service_name VARCHAR(255) NOT NULL, default_for_level SMALLINT DEFAULT NULL, automatic_instance_generation INT NOT NULL, sub_class_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__phase_ranking_systems (phase_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_9938BB2E99091188 (phase_id), INDEX IDX_9938BB2ECD8F5098 (ranking_system_id), PRIMARY KEY(phase_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__game_ranking_systems (game_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_8855591CE48FD905 (game_id), INDEX IDX_8855591CCD8F5098 (ranking_system_id), PRIMARY KEY(game_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__match_ranking_systems (match_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_system_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_936483DF2ABEACD6 (match_id), INDEX IDX_936483DFCD8F5098 (ranking_system_id), PRIMARY KEY(match_id, ranking_system_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE relation__tournament_ranking_systems ADD CONSTRAINT FK_576CD37433D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__tournament_ranking_systems ADD CONSTRAINT FK_576CD374CD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__competition_ranking_systems ADD CONSTRAINT FK_CE1B94147B39D312 FOREIGN KEY (competition_id) REFERENCES competitions (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__competition_ranking_systems ADD CONSTRAINT FK_CE1B9414CD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__phase_ranking_systems ADD CONSTRAINT FK_9938BB2E99091188 FOREIGN KEY (phase_id) REFERENCES phases (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__phase_ranking_systems ADD CONSTRAINT FK_9938BB2ECD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_ranking_systems ADD CONSTRAINT FK_8855591CE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__game_ranking_systems ADD CONSTRAINT FK_8855591CCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_ranking_systems ADD CONSTRAINT FK_936483DF2ABEACD6 FOREIGN KEY (match_id) REFERENCES matches (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_ranking_systems ADD CONSTRAINT FK_936483DFCD8F5098 FOREIGN KEY (ranking_system_id) REFERENCES rankingSystems (id) ON DELETE CASCADE');
  }
//</editor-fold desc="Public Methods">
}
