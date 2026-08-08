<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects authenticated users who have not completed business setup to
 * the onboarding wizard. Protect every dashboard route with this middleware.
 */
final class EnsureBusinessSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->business_id) {
            if (! $request->is('onboarding*') && ! $request->is('profile*')) {
                return redirect()->route('onboarding.business');
            }
        }

        return $next($request);
    }
}
