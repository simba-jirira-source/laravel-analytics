<?php

declare(strict_types=1);

namespace LaravelAnalytics\LaravelAnalytics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelAnalytics\LaravelAnalytics\Support\DashboardAuthorizer;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeAnalyticsDashboard
{
    public function __construct(
        protected DashboardAuthorizer $authorizer,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->authorizer->authorize($request->user());

        return $next($request);
    }
}
