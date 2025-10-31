<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import LocaleSelector from '@/components/LocaleSelector.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthEnhancedLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { redirect as socialRedirect } from '@/routes/social';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <AuthBase
        :title="$t('auth.login_title')"
        :description="$t('auth.enter_details_login')"
    >
        <Head :title="$t('auth.login')" />

        <!-- Language Selector -->
        <div class="mb-4 flex justify-end">
            <LocaleSelector />
        </div>

        <div
            v-if="status"
            class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-center text-sm font-medium text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400"
        >
            {{ status }}
        </div>

        <Form
            :action="AuthenticatedSessionController.store.url()"
            method="post"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="space-y-5">
                <!-- Email Field -->
                <div class="space-y-2">
                    <Label for="email" class="text-sm font-semibold">
                        {{ $t('auth.email') }}
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Mail class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@example.com"
                            class="h-12 pl-11 transition-all duration-200 focus:ring-2"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-sm font-semibold">
                            {{ $t('auth.password') }}
                        </Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-xs font-medium transition-colors hover:text-primary"
                            :tabindex="5"
                        >
                            {{ $t('auth.forgot_password') }}
                        </TextLink>
                    </div>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Lock class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            :placeholder="$t('auth.password')"
                            class="h-12 pl-11 pr-11 transition-all duration-200 focus:ring-2"
                        />
                        <button
                            type="button"
                            @click="togglePasswordVisibility"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-muted-foreground hover:text-foreground transition-colors duration-200 focus:outline-none"
                            :aria-label="showPassword ? $t('auth.hide_password') : $t('auth.show_password')"
                        >
                            <EyeOff v-if="showPassword" class="h-5 w-5" />
                            <Eye v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <Label
                        for="remember"
                        class="flex cursor-pointer items-center space-x-2.5"
                    >
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span class="text-sm font-medium text-foreground">
                            {{ $t('auth.remember_me') }}
                        </span>
                    </Label>
                </div>

                <!-- Submit Button -->
                <Button
                    type="submit"
                    class="mt-6 h-12 w-full text-base font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-lg"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="mr-2 h-5 w-5 animate-spin"
                    />
                    <span v-else>{{ $t('auth.login') }}</span>
                </Button>
            </div>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-border"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-card px-2 text-muted-foreground">{{ $t('auth.or') }}</span>
                </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="space-y-3">
                <a
                    :href="socialRedirect('google').url"
                    class="flex h-11 w-full items-center justify-center gap-3 rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground transition-all duration-200 hover:bg-muted hover:scale-[1.02]"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    {{ $t('auth.login_with_google') }}
                </a>

                <a
                    :href="socialRedirect('facebook').url"
                    class="flex h-11 w-full items-center justify-center gap-3 rounded-lg border border-border bg-background px-4 text-sm font-medium text-foreground transition-all duration-200 hover:bg-muted hover:scale-[1.02]"
                >
                    <svg class="h-5 w-5" fill="#1877F2" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    {{ $t('auth.login_with_facebook') }}
                </a>
            </div>

            <!-- Sign Up Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-muted-foreground">
                    {{ $t('auth.dont_have_account') }}
                    <TextLink
                        :href="register()"
                        class="font-semibold text-primary underline-offset-4 transition-colors hover:underline"
                        :tabindex="5"
                    >
                        {{ $t('auth.register') }}
                    </TextLink>
                </p>
            </div>
        </Form>
    </AuthBase>
</template>
