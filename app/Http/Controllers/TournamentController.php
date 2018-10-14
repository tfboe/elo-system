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
   */
  public function createOrReplaceTournament(Request $request): JsonResponse
  {
    return $this->checkAsync($request, CreateOrReplaceTournamentInterface::class);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws PreconditionFailedException
   */
  public function uploadFile(Request $request): JsonResponse
  {
    $this->validate($request, ['userIdentifier' => 'required|string', 'extension' => 'required|string']);
    if ($request->hasFile('tournamentFile')) {
      $file = $request->file('tournamentFile');
      if (!$file->isValid()) {
        throw new PreconditionFailedException("Error during file upload!");
      }
      $userId = \Auth::user()->getId();
      $destinationDir = "../storage/file-uploads/" . $userId;
      if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
      }
      $dp = fopen($destinationDir, 'r');
      if (flock($dp, LOCK_EX)) {
        $files = scandir($destinationDir);
        $beginnings = [];
        foreach ($files as $fileName) {
          $pos = strpos($fileName, ".");
          if ($pos !== false) {
            $fileName = substr($fileName, $pos);
          }
          $beginnings[$fileName] = true;
        }
        $count = 1;
        $extension = $request->get("extension");
        $prefix = $request->get("userIdentifier");
        while (array_key_exists($prefix . "-" . $count . "." . $extension, $beginnings)) {
          $count += 1;
        }
        $file->move($destinationDir, $prefix . "-" . $count . "." . $extension);
        flock($dp, LOCK_UN);    // release the lock
        return new JsonResponse(true);
      } else {
        throw new PreconditionFailedException("Couldn't move uploaded file!");
      }
    } else {
      throw new PreconditionFailedException("No file uploaded!");
    }
  }
//</editor-fold desc="Public Methods">
}
