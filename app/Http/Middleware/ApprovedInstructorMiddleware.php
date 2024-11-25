<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Enums\SuperInstructor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedInstructorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $superInstructor = auth('web')->user()?->super_instructors;

        if ($superInstructor && $superInstructor->value == SuperInstructor::YES->value) {
            return $next($request);
        }
        
        if (!instructorStatus() || instructorStatus() != UserStatus::APPROVED->value) {
            return redirect()->route('become-instructor');
        }

        return $next($request);
    }
}
