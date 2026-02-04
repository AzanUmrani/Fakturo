<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiAuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddSocialUserRequest;
use App\Http\Requests\AddSubscriptionUserRequest;
use App\Http\Requests\SocialLoginUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\RemoveSocialUserRequest;
use App\Http\Resources\Api\AuthUserResource;
use App\Http\Services\UserService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HttpResponses;

    public function __construct(
        private UserService $userService,
    ) {
    }

    /**
     * @unauthenticated
     * @throws ApiAuthException
     */
    public function login(LoginUserRequest $request): AuthUserResource
    {
        return $this->userService->login($request);
    }

    /**
     * @unauthenticated
     * @param  SocialLoginUserRequest  $request
     * @return AuthUserResource
     */
    public function loginSocial(SocialLoginUserRequest $request): AuthUserResource
    {
        return $this->userService->loginSocial($request);
    }

    /**
     * @unauthenticated
     * @return AuthUserResource
     */
    public function loginGuest(): AuthUserResource
    {
        return $this->userService->loginGuest();
    }

    public function isSubscribedAndActive(): JsonResponse
    {
        return $this->userService->isSubscribedAndActive();
    }

    public function addSubscription(AddSubscriptionUserRequest $request): JsonResponse
    {
        return $this->userService->addSubscription($request);
    }

    public function addSocial(AddSocialUserRequest $request): AuthUserResource
    {
        return $this->userService->addSocial($request);
    }

    /**
     * TODO refactor
     * @param  RemoveSocialUserRequest  $request
     * @return array
     */
    public function removeSocial(RemoveSocialUserRequest $request)
    {
        $user = Auth::user();

        // check if user has email
        if (empty($user->email)) {
            return $this->error('', 'You cannot remove social account if you do not have email and password set', 400);
        }

        $user->update([
            $request->get('provider').'_token' => null,
        ]);

        return $this->success([
            'user' => $user,
        ], 'Social account removed');
    }

    /**
     * @unauthenticated
     * @param  RegisterUserRequest  $request
     * @return AuthUserResource
     */
    public function register(RegisterUserRequest $request): AuthUserResource
    {
        return $this->userService->store($request);
    }

    public function delete(): JsonResponse
    {
        $user = Auth::user();
        $user->delete();

        return response()->json([
            'message' => 'USER_DELETED',
        ]);
    }

    /**
     * TODO implement
     */
    public function logout(): string
    {
        return 'logout';
    }

}
