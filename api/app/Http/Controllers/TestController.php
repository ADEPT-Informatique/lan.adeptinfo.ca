<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint simples pour tester rapidement le bon fonctionnement de l'API.
 *
 * Class ContributionController
 */
class TestController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @link TODO
     *
     */
    public function base(Request $request)
    {
        return response()->json([
            'ok!'
        ], 200);
    }
}
