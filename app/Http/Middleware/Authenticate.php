<?php

namespace App\Http\Middleware;

use App\Helper\ApiResponseFormat;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        $t = (string)$request->getRequestUri();
        if (strpos($t, "/api/v1") !== false) {
            $format = new ApiResponseFormat();
            return response()->json($format->formatResponseWithPages("Parameter Authorization Kosong / Authorization Kadaluarsa",[], $format->STAT_UNAUTHORIZED()));
        } else {
            if (! $request->expectsJson()) {
                return route('login');
            }
        }

    }
}
