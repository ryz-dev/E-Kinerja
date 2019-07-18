<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helper\ApiResponseFormat;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];


    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $exception
     * @return Response
     */
    public function render($request, Exception $e)
    {
        if (str_contains($request->getRequestUri(), 'api/v1')) {
            $status = 500;
            $mesaage = $e->getMessage();

            if ($e instanceof AuthenticationException) {
                $status = 401;
            }
            if ($e instanceof ModelNotFoundException) {
                $status = 404;
                $mesaage = 'Not Found';
            }
            if ($e instanceof NotFoundHttpException) {
                $status = 404;
                $mesaage = 'Not Found';
            }
            if ($e instanceof AccessDeniedHttpException) {
                $status = 403;
                $mesaage = 'Access Denied';
            }
            if ($e instanceof HttpException) {
                $status = $e->getStatusCode();
            }
            if ($e instanceof QueryException) {
                $status = 500;
            }
            $response['diagnostic'] = [
                'message' => $mesaage,
                'status' => $status,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            return response()->json($response, $status);
        }
        return parent::render($request, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $getUri = (string)$request->getRequestUri();
        if (strpos($getUri, "/api/") !== false) {
            $format = new ApiResponseFormat();
            return response()->json($format->formatResponseWithPages("Parameter Authorization Kosong / Authorization Kadaluarsa", [], $format->STAT_UNAUTHORIZED()));
        } else {
            return redirect()->guest($exception->redirectTo() ?? route('login'));
        }
    }

}
