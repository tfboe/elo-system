<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 8:48 PM
 */

namespace Tests\Integration;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
use App\Entity\Helpers\AutomaticInstanceGeneration;
use App\Helpers\Level;
use Tests\Helpers\LumenTestCase;

/**
 * Class TournamentTest
 * @package Tests\Integration
 */
class EnumTest extends LumenTestCase
{
//<editor-fold desc="Public Methods">
  public function testAutomaticInstanceGeneration()
  {
    self::assertEquals(0, AutomaticInstanceGeneration::OFF);
    self::assertEquals(1, AutomaticInstanceGeneration::MONTHLY);
    self::assertEquals(['OFF', 'MONTHLY'],
      AutomaticInstanceGeneration::getNames());
  }

  public function testGameMode()
  {
    self::assertEquals(0, GameMode::OFFICIAL);
    self::assertEquals(1, GameMode::SPEEDBALL);
    self::assertEquals(2, GameMode::CLASSIC);
    self::assertEquals(['OFFICIAL', 'SPEEDBALL', 'CLASSIC'], GameMode::getNames());
  }

  public function testLevels()
  {
    self::assertEquals(0, Level::GAME);
    self::assertEquals(1, Level::MATCH);
    self::assertEquals(2, Level::PHASE);
    self::assertEquals(3, Level::COMPETITION);
    self::assertEquals(4, Level::TOURNAMENT);
    self::assertEquals(['GAME', 'MATCH', 'PHASE', 'COMPETITION', 'TOURNAMENT'], Level::getNames());
  }

  public function testOrganizingMode()
  {
    self::assertEquals(0, OrganizingMode::ELIMINATION);
    self::assertEquals(1, OrganizingMode::QUALIFICATION);
    self::assertEquals(['ELIMINATION', 'QUALIFICATION'], OrganizingMode::getNames());
  }

  public function testScoreMode()
  {
    self::assertEquals(0, ScoreMode::ONE_SET);
    self::assertEquals(1, ScoreMode::BEST_OF_THREE);
    self::assertEquals(2, ScoreMode::BEST_OF_FIVE);
    self::assertEquals(['ONE_SET', 'BEST_OF_THREE', 'BEST_OF_FIVE'], ScoreMode::getNames());
  }

  public function testTable()
  {
    self::assertEquals(0, Table::MULTITABLE);
    self::assertEquals(1, Table::GARLANDO);
    self::assertEquals(2, Table::LEONHART);
    self::assertEquals(3, Table::TORNADO);
    self::assertEquals(4, Table::ROBERTO_SPORT);
    self::assertEquals(5, Table::BONZINI);
    self::assertEquals(['MULTITABLE', 'GARLANDO', 'LEONHART', 'TORNADO', 'ROBERTO_SPORT', 'BONZINI'],
      Table::getNames());
  }

  public function testTeamMode()
  {
    self::assertEquals(0, TeamMode::DOUBLE);
    self::assertEquals(1, TeamMode::SINGLE);
    self::assertEquals(2, TeamMode::DYP);
    self::assertEquals(['DOUBLE', 'SINGLE', 'DYP'], TeamMode::getNames());
  }
//</editor-fold desc="Public Methods">
}