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
   * @param $file
   * @return false|string
   */
  private function getFileSafe($file)
  {
    // Remove anything which isn't a word, whitespace, number
    // or any of the following caracters -_~,;[]().
    // If you don't need to handle multi-byte characters
    // you can use preg_replace rather than mb_ereg_replace
    // Thanks Lukasz Rysiak!
    $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file);
    // Remove any runs of periods (thanks falstro!)
    $file = mb_ereg_replace("([\.]{2,})", '', $file);
    return $file;
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
            $fileName = substr($fileName, 0, $pos);
          }
          $beginnings[$fileName] = true;
        }
        $prefix = $this->getFileSafe(str_replace(" ", "-", $request->get("userIdentifier")));
        $count = 1;
        $extension = $request->get("extension");
        while (array_key_exists($prefix . "-" . $count, $beginnings)) {
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
