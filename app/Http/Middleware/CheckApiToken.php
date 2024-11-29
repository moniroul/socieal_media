<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;

class CheckApiToken
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


        // Check if the user is authenticated via API token
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'status' => 'false',
                'error' => 'Unauthorized. Token is missing or invalid.'
            ], 401);
        }

        // Get the user's token
        $user = Auth::guard('api')->user();
        $tokenId = $user->token()->id;

        // Retrieve the token from the database
        $token = Token::find($tokenId);

        if (!$token) {
            return response()->json([
                'status' => 'false',

                'error' => 'Token not found.'
            ], 401);
        }

        // Check if the token is expired
        if (Carbon::parse($token->expires_at)->isPast()) {
            return response()->json([
                'status' => 'false',
                'error' => 'Unauthorized. Token has expired.'
            ], 401);
        }



        // Proceed with the request
        return $next($request);
    }
}
