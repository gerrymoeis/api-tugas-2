<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): UserResource
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return new UserResource($user);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();
        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Invalid credentials"]
            ], 401));
        }

        $user->token = Str::random(60);
        $user->save();

        return new UserResource($user);
    }

    public function get(Request $request): UserResource
    {
        return new UserResource(Auth::user());
    }

    // public function update(UserUpdateRequest $request): UserResource
    // {
    //     $user = Auth::user();
    //     $data = $request->validated();

    //     if (isset($data['password'])) {
    //         $data['password'] = Hash::make($data['password']);
    //     }
    //     $user->update($data);

    //     return new UserResource($user);
    // }

    // public function logout(Request $request): JsonResponse
    // {
    //     $user = Auth::user();
    //     $user->token = null;
    //     $user->save();

    //     return response()->json(["data" => true]);
    // }
}