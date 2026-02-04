import { Head, useForm, router } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler, useState } from 'react';
import { GoogleLogin, CredentialResponse } from '@react-oauth/google';
import AppleSignin from 'react-apple-signin-auth';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import {jwtDecode} from "jwt-decode";
import { useLang } from '@/hooks/useLang';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { __, trans } = useLang();
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        email: '',
        password: '',
        remember: false,
    });

    const [socialLoading, setSocialLoading] = useState<string | null>(null);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    const handleGoogleSuccess = (credentialResponse: CredentialResponse) => {
        setSocialLoading('google');

        if (credentialResponse.credential) {
            const decodedResponse = jwtDecode(credentialResponse.credential);
            if (decodedResponse.sub) {
                router.post(route('login.social'), {
                    token: decodedResponse.sub,
                    extra: decodedResponse,
                    provider: 'google'
                }, {
                    onSuccess: () => {
                        console.log('Google login successful');
                    },
                    onError: (errors) => {
                        console.error('Google login failed:', errors);
                    },
                    onFinish: () => {
                        setSocialLoading(null);
                    }
                });
            }
        }
    };

    const handleGoogleError = () => {
        console.error('Google login failed');
        setSocialLoading(null);
    };

    const handleAppleSuccess = (response: any) => {
        setSocialLoading('apple');

        router.post(route('login.social'), {
            token: response.authorization.id_token,
            extra: {
                name: response.user?.name ? `${response.user.name.firstName} ${response.user.name.lastName}` : '',
                email: response.user?.email || ''
            },
            provider: 'apple'
        }, {
            onSuccess: () => {
                console.log('Apple login successful');
            },
            onError: (errors) => {
                console.error('Apple login failed:', errors);
            },
            onFinish: () => {
                setSocialLoading(null);
            }
        });
    };

    const handleAppleError = (error: any) => {
        console.error('Apple login failed:', error);
        setSocialLoading(null);
    };

    return (
        <AuthLayout title={__('auth.login.title')} description={__('auth.login.description')}>
            <Head title={__('auth.login.login_button')} />

            <form className="flex flex-col gap-6" onSubmit={submit}>
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="email">{__('auth.login.email')}</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder={__('auth.login.email_placeholder')}
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <div className="flex items-center">
                            <Label htmlFor="password">{__('auth.login.password')}</Label>
                            {canResetPassword && (
                                <TextLink href={route('password.request')} className="ml-auto text-sm" tabIndex={5}>
                                    {__('auth.login.forgot_password')}
                                </TextLink>
                            )}
                        </div>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={2}
                            autoComplete="current-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder={__('auth.login.password_placeholder')}
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="flex items-center space-x-3">
                        <Checkbox
                            id="remember"
                            name="remember"
                            checked={data.remember}
                            onClick={() => setData('remember', !data.remember)}
                            tabIndex={3}
                        />
                        <Label htmlFor="remember">{__('auth.login.remember_me')}</Label>
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                        {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                        {__('auth.login.login_button')}
                    </Button>
                </div>
            </form>

            <div className="relative">
                <div className="absolute inset-0 flex items-center">
                    <span className="w-full border-t" />
                </div>
                <div className="relative flex justify-center text-xs uppercase">
                    <span className="bg-background px-2 text-muted-foreground">{__('auth.login.or_continue_with')}</span>
                </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
                <div className="w-full">
                    {socialLoading === 'google' ? (
                        <Button variant="outline" disabled className="w-full">
                            <LoaderCircle className="h-4 w-4 animate-spin" />
                            {__('auth.login.google')}
                        </Button>
                    ) : (
                        <div className="w-full">
                            <GoogleLogin
                                onSuccess={handleGoogleSuccess}
                                onError={handleGoogleError}
                                theme="outline"
                                size="large"
                                width="100%"
                                text="signin_with"
                                shape="rectangular"
                                logo_alignment="left"
                            />
                        </div>
                    )}
                </div>

                <div className="w-full">
                    {socialLoading === 'apple' ? (
                        <Button variant="outline" disabled className="w-full">
                            <LoaderCircle className="h-4 w-4 animate-spin" />
                            {__('auth.login.apple')}
                        </Button>
                    ) : (
                        <AppleSignin
                            uiType="light"
                            authOptions={{
                                clientId: 'app.fakturo.signin',
                                scope: 'name email',
                                redirectURI: window.location.origin,
                                state: 'state',
                                usePopup: true
                            }}
                            onSuccess={handleAppleSuccess}
                            onError={handleAppleError}
                            skipScript={false}
                            render={(renderProps: any) => (
                                <Button
                                    variant="outline"
                                    onClick={renderProps.onClick}
                                    disabled={socialLoading !== null}
                                    className="w-full"
                                >
                                    <svg className="h-4 w-4 mr-2" viewBox="0 0 24 24">
                                        <path
                                            fill="currentColor"
                                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"
                                        />
                                    </svg>
                                    {__('auth.login.sign_in_with_apple')}
                                </Button>
                            )}
                        />
                    )}
                </div>
            </div>

            <div className="text-center text-sm text-muted-foreground">
                {__('auth.login.no_account')}{' '}
                <TextLink href={route('register')} tabIndex={5}>
                    {__('auth.login.sign_up')}
                </TextLink>
            </div>

            {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
        </AuthLayout>
    );
}
