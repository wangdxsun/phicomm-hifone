<?php

namespace Hifone\Exceptions;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
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
        HifoneException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e) && env('APP_DEBUG') === false) {
            $guzzle = new Client();
            $guzzle->post('http://192.168.61.98:8080/IMServer/qy/send', [
                'form_params' => [
                    'domain' => request()->fullUrl(),
                    'errorTrack' => $e->getTraceAsString(),
                    'fileName' => $e->getFile(),
                    'line' => $e->getLine(),
                    'msg' => $e->getMessage(),
                    'time' => time(),
                    'toUser' => env('WX_ALERT', 'FX008135|FX008759|FX008747')
                ]
            ]);
        }
        parent::report($e);
    }

    public function render($request, Exception $e)
    {
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            return $e->getResponse();
        }

        if ($request->ajax() || $request->wantsJson() || $request->isApi()) {
            return new JsonResponse(['msg' => $e->getMessage(), 'code' => $e->getCode() ?: 400], $e->getCode() ?: 400);
        } elseif ($this->isHttpException($e)) {
            return $this->toIlluminateResponse($this->renderHttpException($e), $e);
        } else {
            return back()->withErrors($e->getMessage());
        }
    }
}
