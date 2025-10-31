<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import LocaleSelector from '@/components/LocaleSelector.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthEnhancedLayout.vue';
import { login } from '@/routes';
import { redirect as socialRedirect } from '@/routes/social';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, User, Mail, Lock, Check, Eye, EyeOff } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
const password = ref('');

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};

// Password strength calculation
const passwordStrength = computed(() => {
    const pwd = password.value;
    if (!pwd) return { score: 0, label: '', color: '' };
    
    let score = 0;
    
    // Length
    if (pwd.length >= 8) score++;
    if (pwd.length >= 12) score++;
    
    // Character variety
    if (/[a-z]/.test(pwd)) score++;
    if (/[A-Z]/.test(pwd)) score++;
    if (/[0-9]/.test(pwd)) score++;
    if (/[^a-zA-Z0-9]/.test(pwd)) score++;
    
    // Determine strength
    if (score <= 2) return { score: 1, label: 'Weak', color: 'bg-red-500' };
    if (score <= 4) return { score: 2, label: 'Fair', color: 'bg-orange-500' };
    if (score <= 5) return { score: 3, label: 'Good', color: 'bg-yellow-500' };
    return { score: 4, label: 'Strong', color: 'bg-green-500' };
});
</script>

<template>
    <AuthBase
        :title="$t('auth.register_title')"
        :description="$t('auth.enter_details')"
    >
        <Head :title="$t('auth.register')" />

        <!-- Language Selector -->
        <div class="mb-4 flex justify-end">
            <LocaleSelector />
        </div>

        <Form
            :action="RegisteredUserController.store.url()"
            method="post"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="space-y-5">
                <!-- Name Field -->
                <div class="space-y-2">
                    <Label for="name" class="text-sm font-semibold">
                        {{ $t('auth.name') }}
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <User class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            :placeholder="$t('auth.full_name')"
                            class="h-12 pl-11 transition-all duration-200 focus:ring-2"
                        />
                    </div>
                    <InputError :message="errors.name" />
                </div>

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
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="email@example.com"
                            class="h-12 pl-11 transition-all duration-200 focus:ring-2"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <Label for="password" class="text-sm font-semibold">
                        {{ $t('auth.password') }}
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Lock class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            v-model="password"
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
                    
                    <!-- Password Strength Indicator -->
                    <div v-if="password" class="space-y-2 animate-in fade-in-0 slide-in-from-top-1 duration-300">
                        <div class="flex gap-1">
                            <div 
                                v-for="i in 4" 
                                :key="i"
                                class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                :class="i <= passwordStrength.score ? passwordStrength.color : 'bg-gray-200 dark:bg-gray-700'"
                            ></div>
                        </div>
                        <p class="text-xs font-medium" :class="{
                            'text-red-600 dark:text-red-400': passwordStrength.score === 1,
                            'text-orange-600 dark:text-orange-400': passwordStrength.score === 2,
                            'text-yellow-600 dark:text-yellow-400': passwordStrength.score === 3,
                            'text-green-600 dark:text-green-400': passwordStrength.score === 4,
                        }">
                            {{ passwordStrength.label }} password
                        </p>
                    </div>
                    
                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                    <Label
                        for="password_confirmation"
                        class="text-sm font-semibold"
                    >
                        {{ $t('auth.password_confirmation') }}
                    </Label>
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5"
                        >
                            <Check class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <Input
                            id="password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            :placeholder="$t('auth.password_confirmation')"
                            class="h-12 pl-11 pr-11 transition-all duration-200 focus:ring-2"
                        />
                        <button
                            type="button"
                            @click="togglePasswordConfirmationVisibility"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-muted-foreground hover:text-foreground transition-colors duration-200 focus:outline-none"
                            :aria-label="showPasswordConfirmation ? $t('auth.hide_password') : $t('auth.show_password')"
                        >
                            <EyeOff v-if="showPasswordConfirmation" class="h-5 w-5" />
                            <Eye v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- Submit Button -->
                <Button
                    type="submit"
                    class="mt-6 h-12 w-full text-base font-semibold transition-all duration-200 hover:scale-[1.02] hover:shadow-lg"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="mr-2 h-5 w-5 animate-spin"
                    />
                    <span v-else>{{ $t('auth.create_account') }}</span>
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

            <!-- Sign In Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-muted-foreground">
                    {{ $t('auth.already_registered') }}
                    <TextLink
                        :href="login()"
                        class="font-semibold text-primary underline-offset-4 transition-colors hover:underline"
                        :tabindex="6"
                    >
                        {{ $t('auth.login') }}
                    </TextLink>
                </p>
            </div>
        </Form>
    </AuthBase>
</template>
