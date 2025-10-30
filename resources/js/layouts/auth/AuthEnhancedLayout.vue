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
        <!-- Left Side - Form -->
        <div
            class="relative z-10 flex w-full flex-col items-center justify-center bg-background px-6 py-12 lg:w-1/2 lg:px-12"
        >
            <!-- Theme Toggle - Top Right -->
            <div class="absolute top-6 right-6">
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

        <!-- Right Side - Gradient Background with Animation -->
        <div
            class="fixed right-0 top-0 hidden h-full w-1/2 overflow-hidden lg:block"
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
                        Welcome to Our Platform
                    </h2>
                    <p class="mb-8 text-lg text-white/90">
                        Build amazing applications with modern tools and best
                        practices. Join thousands of developers who trust our
                        platform.
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
                                <h3 class="font-semibold">Secure & Reliable</h3>
                                <p class="text-sm text-white/80">
                                    Enterprise-grade security with 99.9% uptime
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
                                <h3 class="font-semibold">Easy to Use</h3>
                                <p class="text-sm text-white/80">
                                    Intuitive interface designed for productivity
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
                                <h3 class="font-semibold">24/7 Support</h3>
                                <p class="text-sm text-white/80">
                                    Our team is always here to help you succeed
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
