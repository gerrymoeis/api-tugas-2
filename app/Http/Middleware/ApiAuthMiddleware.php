<?php
namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil token dari header Authorization
        $token = $request->header('Authorization');
        if (!$token) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Authorization token required"]
            ], 401));
        }

        // Hapus prefix 'Bearer ' dari token
        $token = str_replace('Bearer ', '', $token);
        $user = User::where('token', $token)->first();

        // Validasi token
        if (!$user) {
            throw new HttpResponseException(response([
                "errors" => ["message" => "Invalid token"]
            ], 401));
        }

        // Simpan pengguna ke dalam request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}