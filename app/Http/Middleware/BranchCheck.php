<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BranchCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Check if branch_id is NOT in session
            if (!session()->has('branch_id')) {
                
                // If NOT Admin (Role ID != 1) and NOT Super Admin, auto-select their assigned branch
                if ($user->role_id != 1 && !$user->isSuperAdmin()) {
                    $branch = \App\Models\Branch::find($user->getRawOriginal('branch_id'));
                    if ($branch) {
                        session([
                            'branch_id'        => $branch->id,
                            'branch_name'      => $branch->name,
                            'branch_filter_id' => $branch->id
                        ]);
                        return $next($request);
                    }
                }

                // If Admin or branch auto-selection failed, redirect to selection page
                // Allow branch selection routes and logout route to prevent loops
                if (!$request->routeIs('branch.select') && 
                    !$request->routeIs('branch.set') &&
                    !$request->routeIs('logout')) {
                    
                    if ($request->expectsJson() || $request->ajax() || $request->wantsJson() || $request->isJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a branch first.'
                        ], 403);
                    }
                    return redirect()->route('branch.select');
                }
            }
        }

        return $next($request);
    }
}
