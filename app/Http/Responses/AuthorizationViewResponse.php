<?php

namespace App\Http\Responses;

use Laravel\Passport\Contracts\AuthorizationViewResponse as AuthorizationViewResponseContract;

class AuthorizationViewResponse implements AuthorizationViewResponseContract
{
    protected $parameters = [];

    public function withParameters(array $parameters = []): static
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function toResponse($request)
    {
        return response()->view('authorize', array_merge([
            'client' => $request->client,
            'user' => $request->user(),
            'scopes' => $request->scopes,
            'request' => $request,
        ], $this->parameters));
    }
}
