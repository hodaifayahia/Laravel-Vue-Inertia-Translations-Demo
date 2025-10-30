<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Shield,
    Zap,
    Users,
    Star,
    Globe,
    Sparkles,
    Code,
    Layers,
} from 'lucide-vue-next';
</script>

<template>
    <Head title="Welcome" />

    <!-- Modern Landing Page -->
    <div class="relative min-h-screen overflow-hidden bg-background">
        <!-- Navigation -->
        <nav
            class="fixed top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60"
        >
            <div class="container mx-auto flex h-16 items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary"
                    >
                        <Sparkles class="h-5 w-5 text-primary-foreground" />
                    </div>
                    <span class="text-xl font-bold">{{
                        $t('app.name', 'Laravel')
                    }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-6 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-flex h-10 items-center justify-center rounded-md px-4 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                        >
                            {{ $t('auth.login') }}
                        </Link>
                        <Link
                            :href="register()"
                            class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-6 text-sm font-medium text-primary-foreground transition-all hover:bg-primary/90 hover:scale-105"
                        >
                            {{ $t('auth.register') }}
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20">
            <div
                class="pointer-events-none absolute inset-0 overflow-hidden opacity-30"
            >
                <div
                    class="absolute left-1/4 top-1/4 h-96 w-96 animate-pulse rounded-full bg-blue-500/20 blur-3xl"
                ></div>
                <div
                    class="animation-delay-2000 absolute right-1/4 top-1/3 h-96 w-96 animate-pulse rounded-full bg-purple-500/20 blur-3xl"
                ></div>
            </div>

            <div class="container relative z-10 mx-auto px-4">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-border bg-muted px-4 py-2 text-sm">
                        <Star class="h-4 w-4 text-yellow-500" />
                        <span>Trusted by thousands of developers worldwide</span>
                    </div>

                    <h1
                        class="mb-6 bg-gradient-to-r from-foreground to-foreground/70 bg-clip-text text-5xl font-bold leading-tight tracking-tight text-transparent md:text-7xl"
                    >
                        Build Amazing Applications with Modern Tools
                    </h1>

                    <p
                        class="mb-10 text-lg text-muted-foreground md:text-xl"
                    >
                        The perfect starting point for your next project. Built
                        with Laravel, Vue, and Inertia.js for the ultimate
                        developer experience.
                    </p>

                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                        >
                            Get Started Free
                            <ArrowRight
                                class="h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                        <Link
                            v-else
                            :href="dashboard()"
                            class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                        >
                            Go to Dashboard
                            <ArrowRight
                                class="h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                        <a
                            href="https://laravel.com/docs"
                            target="_blank"
                            class="inline-flex h-14 items-center justify-center gap-2 rounded-lg border border-border bg-background px-8 text-base font-semibold transition-all hover:bg-accent"
                        >
                            View Documentation
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20">
            <div class="container mx-auto px-4">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-4xl font-bold">Why Choose Us?</h2>
                    <p class="text-lg text-muted-foreground">
                        Everything you need to build modern web applications
                    </p>
                </div>

                <div
                    class="grid gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        class="group rounded-2xl border border-border bg-card p-8 transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-blue-500/10 text-blue-500 transition-transform group-hover:scale-110"
                        >
                            <Zap class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">Lightning Fast</h3>
                        <p class="text-muted-foreground">
                            Built with performance in mind. Experience blazing
                            fast page loads and smooth interactions.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-border bg-card p-8 transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-green-500/10 text-green-500 transition-transform group-hover:scale-110"
                        >
                            <Shield class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">Secure by Default</h3>
                        <p class="text-muted-foreground">
                            Enterprise-grade security with authentication, authorization, and encrypted data.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-border bg-card p-8 transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-purple-500/10 text-purple-500 transition-transform group-hover:scale-110"
                        >
                            <Users class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">User Management</h3>
                        <p class="text-muted-foreground">
                            Complete user management system with roles, permissions, and profiles built-in.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-border bg-card p-8 transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-orange-500/10 text-orange-500 transition-transform group-hover:scale-110"
                        >
                            <Code class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">Modern Stack</h3>
                        <p class="text-muted-foreground">
                            Laravel, Vue 3, Inertia.js, and Tailwind CSS. The best tools for modern development.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-border bg-card p-8 transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-pink-500/10 text-pink-500 transition-transform group-hover:scale-110"
                        >
                            <Globe class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">Internationalization</h3>
                        <p class="text-muted-foreground">
                            Multi-language support built-in. Reach a global audience with ease.
                        </p>
                    </div>

                    <div
                        class="group rounded-2xl border border-border bg-card p-8 transition-all hover:scale-105 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-cyan-500/10 text-cyan-500 transition-transform group-hover:scale-110"
                        >
                            <Layers class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">Scalable Architecture</h3>
                        <p class="text-muted-foreground">
                            Clean, maintainable code structure that scales with your business needs.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-20 bg-muted/30">
            <div class="container mx-auto px-4">
                <div class="grid gap-8 md:grid-cols-4">
                    <div class="text-center">
                        <div class="mb-2 text-5xl font-bold text-primary">
                            10K+
                        </div>
                        <div class="text-muted-foreground">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-5xl font-bold text-primary">
                            99.9%
                        </div>
                        <div class="text-muted-foreground">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-5xl font-bold text-primary">
                            24/7
                        </div>
                        <div class="text-muted-foreground">Support</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-5xl font-bold text-primary">
                            500+
                        </div>
                        <div class="text-muted-foreground">Projects</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20">
            <div class="container mx-auto px-4">
                <div
                    class="relative overflow-hidden rounded-3xl border border-border bg-gradient-to-br from-primary/10 via-purple-500/10 to-pink-500/10 p-12 text-center"
                >
                    <div
                        class="pointer-events-none absolute inset-0 opacity-20"
                    >
                        <div
                            class="absolute left-1/4 top-1/4 h-64 w-64 rounded-full bg-primary/30 blur-3xl"
                        ></div>
                        <div
                            class="absolute right-1/4 bottom-1/4 h-64 w-64 rounded-full bg-purple-500/30 blur-3xl"
                        ></div>
                    </div>

                    <div class="relative z-10">
                        <h2 class="mb-4 text-4xl font-bold md:text-5xl">
                            Ready to Get Started?
                        </h2>
                        <p class="mb-8 text-lg text-muted-foreground">
                            Join thousands of developers building amazing
                            applications today.
                        </p>
                        <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                            <Link
                                v-if="!$page.props.auth.user"
                                :href="register()"
                                class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                            >
                                Start Building Now
                                <ArrowRight
                                    class="h-5 w-5 transition-transform group-hover:translate-x-1"
                                />
                            </Link>
                            <Link
                                v-else
                                :href="dashboard()"
                                class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                            >
                                Open Dashboard
                                <ArrowRight
                                    class="h-5 w-5 transition-transform group-hover:translate-x-1"
                                />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border py-12">
            <div class="container mx-auto px-4">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary"
                        >
                            <Sparkles class="h-4 w-4 text-primary-foreground" />
                        </div>
                        <span class="font-bold">{{
                            $t('app.name', 'Laravel')
                        }}</span>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        © 2025 All rights reserved. Built with ❤️ using Laravel
                        & Vue
                    </div>
                    <div class="flex gap-6">
                        <a
                            href="https://laravel.com/docs"
                            target="_blank"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            Documentation
                        </a>
                        <a
                            href="https://github.com"
                            target="_blank"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            GitHub
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes pulse-slow {
    0%,
    100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>
