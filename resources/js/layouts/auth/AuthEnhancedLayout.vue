<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-screen w-full">
        <!-- Form Side - Left in LTR, Right in RTL -->
        <div
            class="relative z-10 flex w-full flex-col items-center justify-center bg-background px-6 py-12 lg:w-1/2 lg:px-12"
        >
            <!-- Theme Toggle - Top Right in LTR, Top Left in RTL -->
            <div class="absolute top-6 ltr:right-6 rtl:left-6">
                <ThemeToggle />
            </div>

            <div class="w-full max-w-md">
                <!-- Logo -->
                <Link
                    :href="home()"
                    class="mb-8 flex items-center justify-center gap-3 transition-opacity hover:opacity-80"
                >
                    <div class="flex items-center justify-center">
                        <AppLogoIcon
                            class="size-10 fill-current text-primary dark:text-white"
                        />
                    </div>
                    <span class="text-2xl font-bold text-foreground"
                        >{{ $t('app.name', 'Laravel') }}</span
                    >
                </Link>

                <!-- Title and Description -->
                <div class="mb-8 text-center">
                    <h1
                        class="mb-2 text-3xl font-bold tracking-tight text-foreground"
                    >
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>

                <!-- Form Slot -->
                <div
                    class="rounded-xl border border-border bg-card p-8 shadow-lg"
                >
                    <slot />
                </div>
            </div>
        </div>

        <!-- Features Side - Right in LTR, Left in RTL -->
        <div
            class="fixed top-0 hidden h-full w-1/2 overflow-hidden ltr:right-0 rtl:left-0 lg:block"
        >
            <!-- Animated Gradient Background -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 opacity-90"
            >
                <!-- Animated Blobs -->
                <div
                    class="absolute -left-20 -top-20 h-96 w-96 animate-blob rounded-full bg-blue-400 opacity-70 mix-blend-multiply blur-3xl filter"
                ></div>
                <div
                    class="animation-delay-2000 absolute -right-20 -top-20 h-96 w-96 animate-blob rounded-full bg-purple-400 opacity-70 mix-blend-multiply blur-3xl filter"
                ></div>
                <div
                    class="animation-delay-4000 absolute -bottom-20 left-20 h-96 w-96 animate-blob rounded-full bg-pink-400 opacity-70 mix-blend-multiply blur-3xl filter"
                ></div>
            </div>

            <!-- Content Overlay -->
            <div
                class="relative z-10 flex h-full flex-col items-center justify-center px-12 text-white"
            >
                <div class="max-w-lg text-center">
                    <h2 class="mb-6 text-4xl font-bold leading-tight">
                        {{ $t('auth.welcome_title') }}
                    </h2>
                    <p class="mb-8 text-lg text-white/90">
                        {{ $t('auth.welcome_description') }}
                    </p>

                    <!-- Features -->
                    <div class="space-y-4 text-left">
                        <div class="flex items-start gap-3">
                            <svg
                                class="mt-1 h-5 w-5 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <div>
                                <h3 class="font-semibold">{{ $t('auth.feature_secure_title') }}</h3>
                                <p class="text-sm text-white/80">
                                    {{ $t('auth.feature_secure_description') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg
                                class="mt-1 h-5 w-5 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <div>
                                <h3 class="font-semibold">{{ $t('auth.feature_easy_title') }}</h3>
                                <p class="text-sm text-white/80">
                                    {{ $t('auth.feature_easy_description') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg
                                class="mt-1 h-5 w-5 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <div>
                                <h3 class="font-semibold">{{ $t('auth.feature_support_title') }}</h3>
                                <p class="text-sm text-white/80">
                                    {{ $t('auth.feature_support_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
