<?php

namespace Lune\Http;

use Closure;

/**
 * HTTP Middleware.
 */
interface Middleware {
    /**
     * Handle the request and return a response, or call the next middleware.
     *
     * @param \Lune\Http\Request $request
     * @param \Close $next
     * @return \Lune\Http\Response
     */
    public function handle(Request $request, Closure $next): Response;
}
