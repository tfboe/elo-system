<?php
declare(strict_types=1);

namespace App\Policies;

use App\Entity\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TournamentPolicy
 * @package App\Policies
 */
class TournamentPolicy
{
  use HandlesAuthorization;

//<editor-fold desc="Public Methods">

  /**
   * Determine whether the user can create new tournaments.
   *
   * @param  User $user
   * @return mixed
   */
  public function create(User $user): bool
  {
    return $user->canCreateTournaments();
  }

  /**
   * Determine whether the user can view tournaments (and rankings).
   *
   * @param  User $user
   * @return mixed
   */
  public function view(User $user): bool
  {
    return $user->canRead();
  }
//</editor-fold desc="Public Methods">
}
