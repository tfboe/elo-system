<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
    AuthorizationException::class,
    HttpException::class,
    ModelNotFoundException::class,
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
    //this is a only json api!
    //if ($request->ajax() || $request->wantsJson()) {
    //don't throw html exceptions always render using json
    $status_code = $this->getExceptionHTTPStatusCode($e);
    return response()->json(
      $this->getJsonMessage($e, $status_code),
      $this->getExceptionHTTPStatusCode($e)
    );
    //}
    //return parent::render($request, $e);
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
    return [
      'status' => $statusCode !== null ? $statusCode : "false",
      'message' => $e->getMessage()
    ];
  }

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
   * Report or log an exception.
   *
   * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
   *
   * @param  \Exception $e
   * @return void
   */
  public function report(Exception $e)
  {
    parent::report($e);
  }
//</editor-fold desc="Public Methods">
}
