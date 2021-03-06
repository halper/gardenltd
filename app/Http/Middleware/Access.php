<?php

namespace App\Http\Middleware;

use App\Site;
use Closure;
use Illuminate\Auth\Guard;
use Illuminate\Http\RedirectResponse;

class Access
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
        $tmp = explode("/",$request->url());
        $site_slug = $tmp[4];
        $site_id = Site::slugToId($site_slug);
        $user = $this->auth->user();
        $group_permission = false;
        foreach($user->group()->get() as $user_group){
            if($user_group->hasSite($site_id)){
                $group_permission = true;
                break;
            }
        }
        if(!$user->hasSite($site_id) &&
            !$group_permission &&
            !$user->isAdmin()){
            return new RedirectResponse(url('/'));
        }
        return $next($request);
    }
}
