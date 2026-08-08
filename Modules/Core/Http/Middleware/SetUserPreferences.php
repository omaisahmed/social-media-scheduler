<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Support\Timezone;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the user's preferred locale and timezone for the request.
 *
 * The timezone is the business's workspace timezone so that scheduled
 * times and any other wall-clock displays are consistent for the whole
 * team. Without this, every request would fall back to the app default.
 */
final class SetUserPreferences
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if ($user->locale) {
                app()->setLocale($user->locale);
            }

            $timezone = Timezone::for((int) ($user->business_id ?? 0));

            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
