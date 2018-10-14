<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\AsyncServices\CreateOrReplaceTournamentInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tfboe\FmLib\Exceptions\PreconditionFailedException;

/**
 * Class TournamentController
 * @package App\Http\Controllers
 */
class TournamentController extends AsyncableController
{
  /**
   * creates or replaces an existing tournament
   *
   * @param Request $request the http request
   * @return JsonResponse
   * @throws PreconditionFailedException
   */
  public function createOrReplaceTournament(Request $request): JsonResponse
  {

    if ($request->hasFile('tournamentFile')) {
      $file = $request->file('tournamentFile');
      if (!$file->isValid()) {
        throw new PreconditionFailedException("Error during file upload!");
      }
      $this->validate($request, ['userIdentifier' => 'required|string']);
      $userId = \Auth::user()->getId();
      $destinationDir = "../storage/file-uploads/" . $userId;
      mkdir($destinationDir, 0777, true);
      $dp = fopen($destinationDir, 'r');
      if (flock($dp, LOCK_EX)) {
        $files = scandir($destinationDir);
        $beginnings = [];
        foreach ($files as $file) {
          $pos = strpos($file, ".");
          if ($pos !== false) {
            $file = substr($file, $pos);
          }
          $beginnings[$file] = true;
        }
        $count = 1;
        $extension = $file->getExtension();
        $prefix = $request->get("userIdentifier");
        while (array_key_exists($prefix . "-" . $count . "." . $extension, $beginnings)) {
          $count += 1;
        }
        $file->move($destinationDir, $prefix . "-" . $count . "." . $extension);
        flock($dp, LOCK_UN);    // release the lock
      } else {
        throw new \Exception("Couldn't move uploaded file!");
      }
    }
    return $this->checkAsync($request, CreateOrReplaceTournamentInterface::class);
  }
//</editor-fold desc="Public Methods">
}
