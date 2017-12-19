<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20171219135252 extends AbstractMigration
{
//<editor-fold desc="Public Methods">
  /**
   * @param Schema $schema
   */
  public function down(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('ALTER TABLE relation__match_rankingA DROP FOREIGN KEY FK_C82767672ABEACD6');
    $this->addSql('ALTER TABLE relation__match_rankingB DROP FOREIGN KEY FK_512E36DD2ABEACD6');
    $this->addSql('DROP TABLE matches');
    $this->addSql('DROP TABLE relation__match_rankingA');
    $this->addSql('DROP TABLE relation__match_rankingB');
    $this->addSql('ALTER TABLE players CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    $this->addSql('ALTER TABLE tournaments CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    $this->addSql('ALTER TABLE users CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
  }

  /**
   * @param Schema $schema
   */
  public function up(Schema $schema)
  {
    $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    $this->addSql('CREATE TABLE matches (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', phase_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, match_number INT NOT NULL, game_mode SMALLINT DEFAULT NULL, team_mode SMALLINT DEFAULT NULL, organizing_mode SMALLINT DEFAULT NULL, score_mode SMALLINT DEFAULT NULL, table_id SMALLINT DEFAULT NULL, result_a INT NOT NULL, result_b INT NOT NULL, result INT NOT NULL, played TINYINT(1) NOT NULL, INDEX IDX_62615BA99091188 (phase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__match_rankingA (match_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_C82767672ABEACD6 (match_id), INDEX IDX_C827676720F64684 (ranking_id), PRIMARY KEY(match_id, ranking_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('CREATE TABLE relation__match_rankingB (match_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', ranking_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_512E36DD2ABEACD6 (match_id), INDEX IDX_512E36DD20F64684 (ranking_id), PRIMARY KEY(match_id, ranking_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BA99091188 FOREIGN KEY (phase_id) REFERENCES phases (id)');
    $this->addSql('ALTER TABLE relation__match_rankingA ADD CONSTRAINT FK_C82767672ABEACD6 FOREIGN KEY (match_id) REFERENCES matches (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_rankingA ADD CONSTRAINT FK_C827676720F64684 FOREIGN KEY (ranking_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_rankingB ADD CONSTRAINT FK_512E36DD2ABEACD6 FOREIGN KEY (match_id) REFERENCES matches (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE relation__match_rankingB ADD CONSTRAINT FK_512E36DD20F64684 FOREIGN KEY (ranking_id) REFERENCES rankings (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE tournaments CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    $this->addSql('ALTER TABLE players CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    $this->addSql('ALTER TABLE users CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
  }
//</editor-fold desc="Public Methods">
}
