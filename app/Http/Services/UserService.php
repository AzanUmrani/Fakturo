<?php

namespace App\Http\Services;

use App\Exceptions\ApiAuthException;
use App\Http\Requests\AddSocialUserRequest;
use App\Http\Requests\AddSubscriptionUserRequest;
use App\Http\Requests\SocialLoginUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\Api\AuthUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserService
{

    public function store(RegisterUserRequest $request): AuthUserResource
    {
        $newUser = User::create([
            'uuid' => Uuid::uuid4()->toString(),
            'name' => '',
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'apple_token' => $request->get('apple_token') ?? null,
            'apple_extra' => $request->get('apple_extra') ?? null,
            'google_token' => $request->get('google_token') ?? null,
            'google_extra' => $request->get('google_extra') ?? null,
            'facebook_token' => $request->get('google_token') ?? null,
            'facebook_extra' => $request->get('facebook_extra') ?? null,
            'invoice_count' => 0,
        ]);

        return AuthUserResource::make(
            ['token' => $newUser->createToken("Email, Password")->plainTextToken, 'user' => $newUser]
        );
    }

    /**
     * @throws ApiAuthException
     */
    public function login(LoginUserRequest $request): AuthUserResource
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            throw ApiAuthException::badCredentials();
        }

        $currentUser = Auth::user();

        return AuthUserResource::make(
            ['token' => $currentUser->createToken("Email, Password")->plainTextToken, 'user' => $currentUser]
        );
    }

    public function loginSocial(SocialLoginUserRequest $request): AuthUserResource
    {
        return (new SocialAuthService())->handleSocialRequest($request->get('token'), $request->get('extra'), $request->get('provider'));
    }

    public function loginGuest(): AuthUserResource
    {
        $uuid = Uuid::uuid4()->toString();

        /* user */
        $newUserData = [
            'uuid' => Uuid::uuid4()->toString(),
            'name' => 'GUEST',
            'email' => null,
            'password' => Hash::make($uuid),
        ];
        $newUser = User::create($newUserData);

        /* company */

        return AuthUserResource::make(
            ['token' => $newUser->createToken('GUEST')->plainTextToken, 'user' => $newUser, 'type' => 'GUEST']
        );
    }

    public function isSubscribedAndActive(): JsonResponse
    {
        $currentUser = Auth::user();

        return response()->json([
            'message' => $currentUser->isSubscribedAndActive() ? 'SUBSCRIPTION_ACTIVE' : 'SUBSCRIPTION_INACTIVE',
        ]);
    }

    public function addSubscription(AddSubscriptionUserRequest $request): JsonResponse
    {
        $currentUser = Auth::user();
        $currentUserSubscription = $currentUser->subscription;

        $revenueCatPurchaseData = $request->get('revenueCatPurchaseData');

        // update ?
        if ($currentUserSubscription) {
            $currentUserSubscription->update([
                'revenue_cat_purchase_data' => $revenueCatPurchaseData,
            ]);

            return response()->json([
                'message' => 'SUBSCRIPTION_UPDATED',
            ]);
        }

        // create ?
        $currentUser->subscription()->create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => $currentUser->id,
            'revenue_cat_purchase_data' => is_array($revenueCatPurchaseData) ? json_encode($revenueCatPurchaseData, JSON_UNESCAPED_UNICODE) : $revenueCatPurchaseData,
        ]);

        return response()->json([
            'message' => 'SUBSCRIPTION_CREATED',
        ]);
    }

    /**
     * @throws ApiAuthException
     */
    public function addSocial(AddSocialUserRequest $request): AuthUserResource
    {
        // check if token is already assigned to any user
        $socialUser = User::where($request->get('provider').'_token', $request->get('token'))->first();

        if ($socialUser) {
            throw ApiAuthException::socialTokenAlreadyAssigned();
        }

        return (new SocialAuthService())->assignSocialTokenToUser($request->get('token'), $request->get('extra'), $request->get('provider'), Auth::user());
    }
}
