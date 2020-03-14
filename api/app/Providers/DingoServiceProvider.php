<?php

namespace App\Providers;

use App\Exceptions\ApiExceptionsHandler as ExceptionHandler;
use Dingo\Api\Provider\LumenServiceProvider;

class DingoServiceProvider extends LumenServiceProvider
{
    protected function registerExceptionHandler()
    {
        $this->app->singleton('api.exception', function ($app) {
            return new ExceptionHandler(
                $app['Illuminate\Contracts\Debug\ExceptionHandler'],
                $this->config('errorFormat'),
                $this->config('debug')
            );
        });
    }
}
