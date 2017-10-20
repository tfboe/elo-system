<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
//<editor-fold desc="Fields">
  /**
   * A list of the exception types that should not be reported.
   *
   * @var array
   */
  protected $dontReport = [
    ValidationException::class,
  ];
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Exception $e
   * @return \Illuminate\Http\Response
   */
  public function render($request, Exception $e)
  {
    foreach ($this->dontReport as $class) {
      if (is_a($e, $class)) {
        return parent::render($request, $e);
      }
    }
    //don't throw html exceptions always render using json
    $status_code = $this->getExceptionHTTPStatusCode($e);

    return response()->json(
      $this->getJsonMessage($e, $status_code),
      $status_code
    );
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Extracts the status code of an exception
   * @param Exception $e the exception to extract from
   * @return int|mixed the status code or 500 if no status code found
   */
  protected function getExceptionHTTPStatusCode(Exception $e)
  {
    // Not all Exceptions have a http status code
    // We will give Error 500 if none found
    return method_exists($e, 'getStatusCode') ? $e->getStatusCode() :
      ($e->getCode() != 0 ? $e->getCode() : 500);
  }

  /**
   * Extracts the status and the message from the given exception and status code
   * @param Exception $e the raised exception
   * @param string|null $statusCode the status code or null if unknown
   * @return array containing the infos status and message
   */
  protected function getJsonMessage(Exception $e, $statusCode = null)
  {
    // You may add in the code, but it's duplication
    return method_exists($e, 'getJsonMessage') ? $e->getJsonMessage() :
      ['status' => $statusCode !== null ? $statusCode : "false",
        'message' => $e->getMessage()];
  }
//</editor-fold desc="Protected Methods">
}
