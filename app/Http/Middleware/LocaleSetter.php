<?php

namespace App\Http\Middleware;

use App\Providers\LocalesServiceProvider;
use Carbon\Carbon;
use Closure;
use Cookie;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class LocaleSetter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Getting the preferred language
        $code = LocalesServiceProvider::getUserPreferredLocale($request);
        LocalesServiceProvider::setLocale($code);



        $langPath = app()->langPath() . '/' . App::getLocale();

        if (!file_exists($langPath . '.json')) {
            $langPath = app()->langPath() . '/pt';
            LocalesServiceProvider::setLocale('pt');
        }

        session()->put('app_translations', file_get_contents($langPath . '.json'));

        return $next($request);
    }
}
