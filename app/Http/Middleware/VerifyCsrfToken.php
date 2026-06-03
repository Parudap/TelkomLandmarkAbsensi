<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Add XSRF-TOKEN cookie to every response for debugging.
     */
    protected function addCookieToResponse($request, $response)
    {
        $response = parent::addCookieToResponse($request, $response);
        $response->headers->setCookie(
            cookie('XSRF-TOKEN', $request->session()->token(), 120, '/', null, false, false, false, 'Lax')
        );
        return $response;
    }
}
