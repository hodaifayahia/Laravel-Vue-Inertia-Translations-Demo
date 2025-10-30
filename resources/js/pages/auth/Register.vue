<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/auth/AuthEnhancedLayout.vue';
import { login } from '@/routes';
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

        <Form
            v-bind="RegisteredUserController.store.form()"
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
                    <span class="bg-card px-2 text-muted-foreground">or</span>
                </div>
            </div>

            <!-- Sign In Link -->
            <div class="text-center">
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
