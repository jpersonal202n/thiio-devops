<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Api\V1\User\UserStoreRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;

class UserController extends Controller
{
    
    public function index()
    {
        try {
            
            $users = User::query()
                ->search(request()->searcher)
                ->orderBy('id','desc')
                ->paginate(request()->per_page);

            return UserResource::collection($users)->additional(['message' => 'Users list']);

        }catch(\Exception $e) {
            return $e;
        }
    }

    public function store(UserStoreRequest $request)
    {
        try {
            
            $user = User::create($request->validated());

            $userResource = new UserResource($user);

            return $userResource->additional(['message' => 'User created successfuly' ]);

        }catch(\Exception $e) {
            return response()->json([
                'exception' => $e->getMessage(),
                'message' => 'Error to create an user'
            ]);
        }
    }

    public function show(string $userUuid)
    {
        try {
            
            $user = User::whereFindUuidFirstOrFail($userUuid);

            $userResource = new UserResource($user);

            return $userResource->additional(['message' => 'User detail' ]);

        }catch(\Exception $e) {
            return $e;
        }
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $userUuid)
    {
        try {
            
            User::whereFindUuidFirstOrFail($userUuid)->delete();

            return response()->json([
                'message' => 'User removed succesfuly'
            ]);

        }catch(\Exception $e) {
            return $e;
        }
    }

}
