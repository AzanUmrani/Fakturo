<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * The available languages for the application.
     *
     * @var array
     */
    protected $languages = ['en', 'sk', 'cz', 'ua'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the locale is set in the URL
        $locale = $request->query('locale');

        // If not in URL, check if it's in the cookie
        if (!$locale) {
            $locale = $request->cookie('locale');
        }

        // If not in cookie, check if it's in the session
        if (!$locale && $request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        // If locale is valid, set it as the application locale
        if ($locale && in_array($locale, $this->languages)) {
            App::setLocale($locale);

            // Store the locale in the session for future requests
            $request->session()->put('locale', $locale);

            // Store the locale in a cookie that lasts for 1 year
            $cookie = cookie('locale', $locale, 60 * 24 * 365);

            // Get the response
            $response = $next($request);

            // Add the cookie to the response
            if (method_exists($response, 'withCookie')) {
                $response->withCookie($cookie);
            }

            return $response;
        }

        return $next($request);
    }
}
