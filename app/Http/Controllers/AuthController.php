<?php

namespace App\Http\Controllers;

use App\Cores\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      summary="Sign in",
     *      description="Login by username, password",
     *      tags={"Auth"},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          description="Pass user credentials",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="username", type="string", example="super"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Wrong credentials response",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={}),
     *          )
     *      )
     * )
     */
    public function login(User $user, LoginRequest $request)
    {
        $data = $request->validated();
        $user = $user->firstWhere('username', $data['username']);

        if (! $user) {
            return $this->responseJson('error', 'Unauthorized. Email not found', '', 401);
        }

        if (! Auth::attempt($data)) {
            return $this->responseJson('error', 'Unauthorized.', '', 401);
        }

        $data = new LoginResource($user);

        return $this->responseJson('success', 'Login success', $data);
    }

    /**
     * @OA\Post(
     *       path="/auth/logout",
     *       summary="Log user out ",
     *       description="Endpoint to log current user out",
     *       tags={"Auth"},
     *       security={
     *           {"token": {}}
     *       },
     *
     *       @OA\Response(
     *           response=200,
     *           description="OK"
     *       ),
     *       @OA\Response(
     *           response=401,
     *           description="Unauthorized"
     *       ),
     * )
     */
    public function logout()
    {
        $user = auth()->user();
        if (! $user) {
            return $this->responseJson('error', 'Unauthorized.', '', 401);
        }

        $revoke = $user->currentAccessToken()->delete();

        /** Use below code if you want to log current user out in all devices */
        // $revoke = auth()->user()->tokens()->delete();
        return $this->responseJson(
            $revoke ? 'success' : 'error',
            $revoke ? __('Logout successfully') : __('Logout failed')
        );
    }
}