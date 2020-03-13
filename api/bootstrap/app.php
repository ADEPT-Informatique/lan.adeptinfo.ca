<?php

use Fruitcake\Cors\CorsServiceProvider;
use Fruitcake\Cors\HandleCors;

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->withFacades();
$app->withEloquent();

$app->configure('auth');
$app->configure('cors');
$app->configure('services');
$app->configure('mail');
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton('filesystem', function ($app) {
    return $app->loadComponent('filesystems', 'Illuminate\Filesystem\FilesystemServiceProvider', 'filesystem');
});

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    'cors' => HandleCors::class,
]);

$app->routeMiddleware([
    'auth'     => App\Http\Middleware\Authenticate::class,
    'language' => App\Http\Middleware\Language::class,
    'login'    => App\Http\Middleware\Login::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

// Main provider
$app->register(App\Providers\AppServiceProvider::class);
// Passport & Auth - https://github.com/dusterio/lumen-passport
$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
//Dusterio\LumenPassport\LumenPassport::routes($app);
// Lumen generator - https://github.com/flipboxstudio/lumen-generator
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
// Dingo
//$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(App\Providers\DingoServiceProvider::class);
// CORS - https://github.com/fruitcake/laravel-cors
$app->register(CorsServiceProvider::class);
// Mail
$app->register(Illuminate\Mail\MailServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/
$app['translator']->setLocale(env('APP_LOCALE', 'en'));

$app->router->group([], function () {
    require __DIR__.'/../routes/api.php';
});

return $app;
