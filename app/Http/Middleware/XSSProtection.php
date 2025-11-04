<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XSSProtection
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
        $input = array_filter($request->all());

        array_walk_recursive($input, function(&$input) {
            $input = strip_tags(str_replace(array("&lt;", "&gt;"), '', $input), '<span><p><a><b><i><u><strong><br><hr><table><tr><th><td><ul><ol><li><h1><h2><h3><h4><h5><h6><del><ins><sup><sub><pre><address><img><figure><embed><iframe><video><style>');
        });

        $request->merge($input);

        return $next($request);
    }
}
