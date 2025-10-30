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
            v-bind="AuthenticatedSessionController.store.form()"
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
                    <span class="bg-card px-2 text-muted-foreground">or</span>
                </div>
            </div>

            <!-- Sign Up Link -->
            <div class="text-center">
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
