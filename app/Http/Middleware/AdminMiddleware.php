<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\Guard;

class AdminMiddleware
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(User $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*if($request->user()->permission < 999){
            abort(401);
        }*/
        return $next($request);
    }
}
