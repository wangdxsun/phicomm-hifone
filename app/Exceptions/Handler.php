<?php

namespace Hifone\Exceptions;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        return parent::render($request, $e);
    }
}
