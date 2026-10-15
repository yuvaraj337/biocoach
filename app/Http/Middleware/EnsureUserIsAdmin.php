<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! ($user->is_admin ?? false) && !in_array($user->role ?? '', ['admin', 'super_admin', 'teacher', 'moderator'], true))) {
            abort(403, 'Administrative access is required.');
        }

        // Restrict sensitive management routes strictly to Super Admin and Admin
        $sensitiveRoutes = ['admin.users.', 'admin.payment-settings.', 'admin.audit.'];
        $currentRoute = $request->route()?->getName() ?? '';
        foreach ($sensitiveRoutes as $prefix) {
            if (str_starts_with($currentRoute, $prefix)) {
                if (!($user->is_admin ?? false) && !in_array($user->role, ['admin', 'super_admin'], true)) {
                    abort(403, 'Unauthorized. Super Admin or Admin role required.');
                }
            }
        }

        return $next($request);
    }
}
