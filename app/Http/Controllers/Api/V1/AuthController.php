<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Auth\AuthRequest;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    
    public function auth(AuthRequest $request)
    {
        
        $credentials = (object) $request->validated();

        $user = User::where('email', $credentials->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken(config('auth.t_token'))->accessToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

}
