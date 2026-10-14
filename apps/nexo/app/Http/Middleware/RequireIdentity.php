<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireIdentity
{
    public function handle(Request $r, Closure $next)
    {
        abort_unless($r->user(), 401, 'Debes iniciar sesión.');

        return $next($r);
    }
}
