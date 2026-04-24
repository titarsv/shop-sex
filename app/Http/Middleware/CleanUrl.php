<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CleanUrl
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{
		// Remove consecutive slashes from the request URI
		$cleanedUri = preg_replace('#/+#', '/', $request->getRequestUri());

		// Redirect to the cleaned URI if it has changed
		if ($request->getRequestUri() !== $cleanedUri) {
			return redirect($cleanedUri, 301);
		}

		return $next($request);
	}
}
