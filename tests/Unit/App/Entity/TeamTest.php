<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Competition;
use App\Entity\Player;
use App\Entity\Team;
use App\Exceptions\ValueNotSet;
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class TeamTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testCompetition()
  {
    $team = $this->team();
    $competition = new Competition();
    $team->setStartNumber(1);
    $team->setCompetition($competition);
    self::assertEquals($competition, $team->getCompetition());
    self::assertEquals(1, $team->getCompetition()->getTeams()->count());
    self::assertEquals($team, $team->getCompetition()->getTeams()[$team->getStartNumber()]);
  }

  public function testCompetitionException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property competition of the class " . Team::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    $team->getCompetition();
  }

  public function testConstructor()
  {
    $team = $this->team();
    self::assertInstanceOf(Team::class, $team);
    self::assertInstanceOf(Collection::class, $team->getPlayers());
    self::assertEquals(0, $team->getPlayers()->count());
    self::assertEquals("", $team->getName());
  }

  public function testId()
  {
    $team = $this->team();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($team);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $team->getId());
  }

  public function testIdException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Team::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $team->getId();
  }

  public function testName()
  {
    $team = $this->team();
    $team->setName("Name");
    self::assertEquals("Name", $team->getName());
  }

  public function testPlayers()
  {
    $team = $this->team();
    $player = new Player();
    $team->getPlayers()->add($player);
    self::assertEquals(1, $team->getPlayers()->count());
    self::assertEquals($player, $team->getPlayers()->first());
  }

  public function testRank()
  {
    $team = $this->team();
    $team->setRank(1);
    self::assertEquals(1, $team->getRank());
  }

  public function testRankException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property rank of the class " . Team::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    $team->getRank();
  }

  public function testStartNumber()
  {
    $team = $this->team();
    $team->setStartNumber(1);
    self::assertEquals(1, $team->getStartNumber());
  }

  public function testStartNumberException()
  {
    $team = $this->team();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property startNumber of the class " . Team::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    $team->getStartNumber();
  }

//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Team a new team
   */
  private function team(): Team
  {
    return new Team();
  }
//</editor-fold desc="Private Methods">
}