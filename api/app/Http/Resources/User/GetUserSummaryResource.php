<?php

namespace App\Http\Resources\User;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class GetUserSummaryResource extends Resource
{
    protected $requestCount;

    public function __construct(Authenticatable $resource, int $permissions)
    {
        $this->requestCount = $permissions;
        parent::__construct($resource);
    }

    /**
     * Transformer la ressource en tableau.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'request_count' => intval($this->requestCount),
        ];
    }
}
