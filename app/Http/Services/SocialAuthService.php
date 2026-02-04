<?php

namespace App\Http\Services;

use App\Http\Resources\Api\AuthUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class SocialAuthService
{
    const SOCIAL_PROVIDERS = [
        'apple' => [
            'dbField' => 'apple_token',
            'dbFieldExtra' => 'apple_extra',
        ],
        'google' => [
            'dbField' => 'google_token',
            'dbFieldExtra' => 'google_extra',
        ],
        'facebook' => [
            'dbField' => 'facebook_token',
            'dbFieldExtra' => 'facebook_extra',
        ],
    ];

    /**
     * @param  string  $socialToken
     * @param  string  $provider  SOCIAL_PROVIDERS
     * @return array
     */
    public function handleSocialRequest(string $socialToken, $socialExtra, string $provider): AuthUserResource
    {
        // find User with token
        $user = User::where(self::SOCIAL_PROVIDERS[$provider]['dbField'], $socialToken)->first();
        if ($user) {
            $user->tokens()->delete();

            return AuthUserResource::make(
                ['token' => $user->createToken(strtoupper($provider))->plainTextToken, 'user' => $user]
            );
        }

//        $userService = new UserService();
//        $userService->store();

        // TODO UserService::store()
        $newUserData = [
            'uuid' => Uuid::uuid4()->toString(),
            'name' => strtoupper($provider).' user',
            'email' => null,
            'password' => Hash::make($socialToken),
        ];
        $newUserData[self::SOCIAL_PROVIDERS[$provider]['dbField']] = $socialToken;
        $newUserData[self::SOCIAL_PROVIDERS[$provider]['dbFieldExtra']] = is_array($socialExtra) ? json_encode($socialExtra, JSON_UNESCAPED_UNICODE) : $socialExtra;
        $newUser = User::create($newUserData);

        return AuthUserResource::make(
            ['token' => $newUser->createToken(strtoupper($provider))->plainTextToken, 'user' => $newUser]
        );
    }

    public function assignSocialTokenToUser(string $socialToken, $socialExtra, string $provider, User $user): AuthUserResource
    {
        $user->{self::SOCIAL_PROVIDERS[$provider]['dbField']} = $socialToken;
        $user->{self::SOCIAL_PROVIDERS[$provider]['dbFieldExtra']} = is_array($socialExtra) ? json_encode($socialExtra, JSON_UNESCAPED_UNICODE) : $socialExtra;
        $user->save();

        return AuthUserResource::make(
            ['user' => $user]
        );
    }
}
