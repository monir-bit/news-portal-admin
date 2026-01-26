import { Head, useForm } from '@inertiajs/react'; // 1. Use standard Inertia hooks
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';
import { route } from 'ziggy-js'; // 2. Use Ziggy for routing

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function ForgotPassword({ status }: { status?: string }) {
    // 3. Initialize form state
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    // 4. Handle form submission
    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        // Standard Laravel route for sending password reset link
        post(route('password.email'));
    };

    return (
        <AuthLayout
            title="Forgot password"
            description="Enter your email to receive a password reset link"
        >
            <Head title="Forgot password" />

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <div className="space-y-6">
                {/* 5. Standard HTML form tag */}
                <form onSubmit={submit}>
                    <div className="grid gap-2">
                        <Label htmlFor="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            // 6. Bind value and onChange to make it a controlled input
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            autoComplete="email"
                            autoFocus
                            placeholder="email@example.com"
                        />

                        <InputError message={errors.email} />
                    </div>

                    <div className="my-6 flex items-center justify-start">
                        <Button
                            className="w-full"
                            disabled={processing}
                            data-test="email-password-reset-link-button"
                        >
                            {processing && (
                                <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />
                            )}
                            Email password reset link
                        </Button>
                    </div>
                </form>

                <div className="space-x-1 text-center text-sm text-muted-foreground">
                    <span>Or, return to</span>
                    <TextLink href={route('login')}>log in</TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}
