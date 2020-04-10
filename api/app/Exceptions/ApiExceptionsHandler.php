<?php

namespace App\Exceptions;

use Dingo\Api\Exception\Handler as DingoHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Gestionnaire d'exceptions.
 * Quand une exception est lancée par l'application, elle est transformée en réponse HTTP ici.
 *
 * Class ApiExceptionsHandler
 */
class ApiExceptionsHandler extends DingoHandler
{
    public function handle($e)
    {
        $message = null;
        $status = null;
        switch (true) {
            case $e instanceof BadRequestHttpException:
                $status = Response::HTTP_BAD_REQUEST;
                $message = json_decode($e->getMessage());
                break;
            case $e instanceof MethodNotAllowedHttpException:
                $status = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = trans('error.405');
                break;
            case $e instanceof NotFoundHttpException:
                $status = Response::HTTP_NOT_FOUND;
                $message = trans('error.404');
                break;
            case $e instanceof AuthorizationException:
                $status = Response::HTTP_FORBIDDEN;
                $message = $e->getMessage();
                break;
            default:
                // Détails d'une erreur 500 uniquement si l'application est en mode debug (défini dans .env)
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
                if (env('APP_DEBUG')) {
                    $message = $e->getMessage();
                }
        }

        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
        ], $status);
    }
}
