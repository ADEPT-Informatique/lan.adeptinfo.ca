<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $e
     *
     * @return void
     * @throws Exception
     *
     */
    public function report($e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception|Throwable $e
     *
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        return parent::render($request, $e);
    }
}
