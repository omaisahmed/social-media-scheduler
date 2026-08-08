<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

/**
 * Places the authenticated user's primary business id into the Laravel
 * context bag so the global business scope used by all domain models can
 * read it cheaply.
 */
final class SetBusinessContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            Context::add('user_id', $user->getKey());
            Context::add('business_id', $user->business_id);
        }

        return $next($request);
    }
}
