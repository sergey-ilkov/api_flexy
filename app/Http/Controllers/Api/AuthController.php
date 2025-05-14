<?php

namespace App\Http\Controllers\Api;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //

    // public function getUser(Request $request)
    // {


    //     $data = [
    //         'user' => auth('sanctum')->user(),
    //     ];

    //     return response()->json($data);
    // }

    public function login(Request $request)
    {
        $authenticate = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if (Auth::attempt($authenticate)) {
            $request->user()->tokens()->delete();

            $data = $this->generateTokens($request->user());

            return response()->json($data);
        }


        $error = [
            "result" => false,
            'error' => 'Authorization error. Incorrect email or password.',
        ];

        return response()->json($error);
    }

    public function refreshToken(Request $request)
    {

        $request->validate([
            'refresh_token' => 'required|string',
        ]);


        // ? not header Authorization Bearer token example

        $refresh_token = explode('|', $request->refresh_token)[1];
        $token = PersonalAccessToken::where('token', hash('sha256', $refresh_token))->first();

        if ($token) {

            $user = $token->tokenable;
            $tokenExpires = $token->expires_at;
            $user->tokens()->delete();

            if (Carbon::parse($tokenExpires)->getTimestamp() > Carbon::now()->getTimestamp()) {

                $data = $this->generateTokens($user);

                return response()->json($data);
            }



            return response()->json(['error' => 'Token lifetime expired']);
        }


        return response()->json(['error' => 'Not tokens']);




        // ? auth header Authorization Bearer token api route
        // Route::middleware('auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value)->group(function () {
        //     Route::post('/refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');
        // });

        $user = auth('sanctum')->user();
        $user->tokens()->delete();

        $data = $this->generateTokens($user);
        return response()->json($data);
    }

    public function logout(Request $request)
    {

        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
            return response('ok', 200);
            // return response('ok', 200)->header('Content-Type', 'text/plain');
        }
        return response()->json(['error' => 'Authorization error']);
    }

    public function generateTokens($user)
    {
        $accesToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], Carbon::now()->addMinutes(config('sanctum.ac_expiration')));
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], Carbon::now()->addMinutes(config('sanctum.rt_expiration')));;


        $data = [
            'result' => true,
            'message' => [
                'access_token' => $accesToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
            ],
            'error' => null,
        ];

        return $data;
    }
}
