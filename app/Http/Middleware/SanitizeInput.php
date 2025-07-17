<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isJson() || $request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $sanitized = Purifier::clean($request->all(), ['HTML.Allowed' => '']);
            $request->merge($sanitized);
        }

        return $next($request);
    }
}
