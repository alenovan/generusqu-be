<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return response()->error('Silahkan Login kembali', 401);
            }
            // if ($user->is_banned == 1) {
            //     return response()->error('Maaf! Akun kamu telah dibanned.', 401);
            // }
            // if ($user->revoke_mobile == 1) {
            //     return response()->error('Maaf! Akun kamu telah di non-aktifkan pada versi mobile.', 401);
            // }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->error('Token is Invalid!', 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->error('Token is Expired!', 401);
            } else {
                return response()->error('Silahkan Login kembali', 401);
            }
        }
        return $next($request);
    }
}
