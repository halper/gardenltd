<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Auth\Guard;
use Illuminate\Http\RedirectResponse;

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
    public function __construct(Guard $auth)
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
        if(!$this->auth->user()->isAdmin()){
            return new RedirectResponse(url('/'));
        }
        return $next($request);
    }
}
