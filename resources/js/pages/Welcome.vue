<script setup lang="ts">
import LocaleSelector from '@/components/LocaleSelector.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
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
    CheckCircle2,
    Rocket,
    Heart,
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
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80"
                    >
                        <Sparkles class="h-5 w-5 text-primary-foreground" />
                    </div>
                    <span class="text-xl font-bold">{{
                        $t('app.name', 'Laravel')
                    }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <LocaleSelector />
                    <ThemeToggle />
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex h-10 items-center justify-center rounded-md bg-primary px-6 text-sm font-medium text-primary-foreground transition-all hover:bg-primary/90 hover:scale-105"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="hidden sm:inline-flex h-10 items-center justify-center rounded-md px-4 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
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
        <section class="relative pt-32 pb-20 overflow-hidden">
            <!-- Animated Background -->
            <div
                class="pointer-events-none absolute inset-0 overflow-hidden"
            >
                <div
                    class="absolute left-1/4 top-1/4 h-96 w-96 animate-pulse rounded-full bg-blue-500/20 blur-3xl dark:bg-blue-400/10"
                ></div>
                <div
                    class="animation-delay-2000 absolute right-1/4 top-1/3 h-96 w-96 animate-pulse rounded-full bg-purple-500/20 blur-3xl dark:bg-purple-400/10"
                ></div>
                <div
                    class="animation-delay-4000 absolute left-1/2 bottom-1/4 h-96 w-96 animate-pulse rounded-full bg-pink-500/20 blur-3xl dark:bg-pink-400/10"
                ></div>
            </div>

            <div class="container relative z-10 mx-auto px-4">
                <div class="mx-auto max-w-4xl text-center">
                    <!-- Trust Badge -->
                    <div class="mb-8 inline-flex items-center gap-2 rounded-full border border-border bg-muted/50 px-4 py-2 text-sm backdrop-blur-sm">
                        <Star class="h-4 w-4 fill-yellow-500 text-yellow-500" />
                        <span class="font-medium">{{ $t('welcome.trusted_by') }}</span>
                    </div>

                    <!-- Main Heading -->
                    <h1
                        class="mb-6 bg-gradient-to-r from-foreground via-foreground to-foreground/70 bg-clip-text text-5xl font-bold leading-tight tracking-tight text-transparent md:text-7xl animate-fade-in"
                    >
                        {{ $t('welcome.hero_title') }}
                    </h1>

                    <!-- Subheading -->
                    <p
                        class="mb-10 text-lg text-muted-foreground md:text-xl animate-fade-in-delay"
                    >
                        {{ $t('welcome.hero_subtitle') }}
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row animate-fade-in-delay-2">
                                                <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground shadow-lg transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                        >
                            <Rocket class="h-5 w-5" />
                            {{ $t('welcome.get_started_free') }}
                            <ArrowRight
                                class="h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                        <Link
                            v-else
                            :href="dashboard()"
                            class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground shadow-lg transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                        >
                            {{ $t('welcome.go_to_dashboard') }}
                            <ArrowRight
                                class="h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Link>
                        <a
                            href="https://laravel.com/docs"
                            target="_blank"
                            class="inline-flex h-14 items-center justify-center gap-2 rounded-lg border-2 border-border bg-background px-8 text-base font-semibold transition-all hover:bg-accent hover:border-foreground/20"
                        >
                            {{ $t('welcome.view_documentation') }}
                        </a>
                    </div>

                    <!-- Social Proof -->
                    <div class="mt-12 flex items-center justify-center gap-8 text-sm text-muted-foreground">
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="h-4 w-4 text-green-500" />
                            <span>{{ $t('welcome.no_credit_card') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="h-4 w-4 text-green-500" />
                            <span>{{ $t('welcome.free_forever') }}</span>
                        </div>
                        <div class="hidden sm:flex items-center gap-2">
                            <CheckCircle2 class="h-4 w-4 text-green-500" />
                            <span>{{ $t('welcome.setup_in_minutes') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-muted/30">
            <div class="container mx-auto px-4">
                                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-4xl font-bold">{{ $t('welcome.why_choose_us') }}</h2>
                    <p class="text-lg text-muted-foreground">
                        {{ $t('welcome.features_subtitle') }}
                    </p>
                </div>

                <div
                    class="grid gap-8 md:grid-cols-2 lg:grid-cols-3"
                >
                    <!-- Feature Cards -->
                    <div
                        class="group relative overflow-hidden rounded-2xl border border-border bg-card p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                        >
                            <Zap class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">{{ $t('welcome.feature_fast_title') }}</h3>
                        <p class="text-muted-foreground">
                            {{ $t('welcome.feature_fast_description') }}
                        </p>
                        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-300 group-hover:w-full"></div>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-2xl border border-border bg-card p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                        >
                            <Shield class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">{{ $t('welcome.feature_secure_title') }}</h3>
                        <p class="text-muted-foreground">
                            {{ $t('welcome.feature_secure_description') }}
                        </p>
                        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-green-500 to-green-600 transition-all duration-300 group-hover:w-full"></div>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-2xl border border-border bg-card p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                        >
                            <Users class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">{{ $t('welcome.feature_users_title') }}</h3>
                        <p class="text-muted-foreground">
                            {{ $t('welcome.feature_users_description') }}
                        </p>
                        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-purple-500 to-purple-600 transition-all duration-300 group-hover:w-full"></div>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-2xl border border-border bg-card p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                        >
                            <Code class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">{{ $t('welcome.feature_stack_title') }}</h3>
                        <p class="text-muted-foreground">
                            {{ $t('welcome.feature_stack_description') }}
                        </p>
                        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-orange-500 to-orange-600 transition-all duration-300 group-hover:w-full"></div>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-2xl border border-border bg-card p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                        >
                            <Globe class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">{{ $t('welcome.feature_i18n_title') }}</h3>
                        <p class="text-muted-foreground">
                            {{ $t('welcome.feature_i18n_description') }}
                        </p>
                        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-pink-500 to-pink-600 transition-all duration-300 group-hover:w-full"></div>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-2xl border border-border bg-card p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl"
                    >
                        <div
                            class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 text-white shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3"
                        >
                            <Layers class="h-7 w-7" />
                        </div>
                        <h3 class="mb-3 text-xl font-bold">{{ $t('welcome.feature_scalable_title') }}</h3>
                        <p class="text-muted-foreground">
                            {{ $t('welcome.feature_scalable_description') }}
                        </p>
                        <div class="absolute bottom-0 left-0 h-1 w-0 bg-gradient-to-r from-cyan-500 to-cyan-600 transition-all duration-300 group-hover:w-full"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-20">
            <div class="container mx-auto px-4">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="text-center group">
                        <div class="mb-2 text-5xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent transition-transform duration-300 group-hover:scale-110">
                            10K+
                        </div>
                        <div class="text-muted-foreground font-medium">{{ $t('welcome.active_users') }}</div>
                    </div>
                    <div class="text-center group">
                        <div class="mb-2 text-5xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent transition-transform duration-300 group-hover:scale-110">
                            99.9%
                        </div>
                        <div class="text-muted-foreground font-medium">{{ $t('welcome.uptime') }}</div>
                    </div>
                    <div class="text-center group">
                        <div class="mb-2 text-5xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent transition-transform duration-300 group-hover:scale-110">
                            24/7
                        </div>
                        <div class="text-muted-foreground font-medium">{{ $t('welcome.support') }}</div>
                    </div>
                    <div class="text-center group">
                        <div class="mb-2 text-5xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent transition-transform duration-300 group-hover:scale-110">
                            500+
                        </div>
                        <div class="text-muted-foreground font-medium">{{ $t('welcome.projects') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-muted/30">
            <div class="container mx-auto px-4">
                <div
                    class="relative overflow-hidden rounded-3xl border-2 border-border bg-gradient-to-br from-primary/10 via-purple-500/10 to-pink-500/10 p-12 text-center backdrop-blur-sm"
                >
                    <!-- Decorative Elements -->
                    <div
                        class="pointer-events-none absolute inset-0"
                    >
                        <div
                            class="absolute left-1/4 top-1/4 h-64 w-64 rounded-full bg-primary/20 blur-3xl animate-pulse"
                        ></div>
                        <div
                            class="animation-delay-2000 absolute right-1/4 bottom-1/4 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl animate-pulse"
                        ></div>
                    </div>

                    <div class="relative z-10">
                        <div class="mb-4 inline-flex items-center justify-center rounded-full bg-primary/10 px-4 py-2">
                            <Heart class="h-4 w-4 mr-2 text-red-500 fill-red-500" />
                            <span class="text-sm font-medium">{{ $t('welcome.join_community') }}</span>
                        </div>
                        <h2 class="mb-4 text-4xl font-bold md:text-5xl">
                            {{ $t('welcome.ready_to_start') }}
                        </h2>
                        <p class="mb-8 text-lg text-muted-foreground max-w-2xl mx-auto">
                            {{ $t('welcome.cta_description') }}
                        </p>
                        <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                            <Link
                                v-if="!$page.props.auth.user"
                                :href="register()"
                                class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground shadow-lg transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                            >
                                <Rocket class="h-5 w-5" />
                                {{ $t('welcome.start_building_now') }}
                                <ArrowRight
                                    class="h-5 w-5 transition-transform group-hover:translate-x-1"
                                />
                            </Link>
                            <Link
                                v-else
                                :href="dashboard()"
                                class="group inline-flex h-14 items-center justify-center gap-2 rounded-lg bg-primary px-8 text-base font-semibold text-primary-foreground shadow-lg transition-all hover:scale-105 hover:bg-primary/90 hover:shadow-xl"
                            >
                                {{ $t('welcome.open_dashboard') }}
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
        <footer class="border-t border-border bg-muted/20 py-12">
            <div class="container mx-auto px-4">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div class="flex items-center gap-2">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80"
                        >
                            <Sparkles class="h-4 w-4 text-primary-foreground" />
                        </div>
                        <span class="font-bold">{{
                            $t('app.name', 'Laravel')
                        }}</span>
                    </div>
                    <div class="text-sm text-muted-foreground">
                        {{ $t('welcome.all_rights_reserved') }} <Heart class="inline h-4 w-4 text-red-500 fill-red-500" /> {{ $t('welcome.using_laravel_vue') }}
                    </div>
                    <div class="flex gap-6">
                        <a
                            href="https://laravel.com/docs"
                            target="_blank"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ $t('welcome.documentation') }}
                        </a>
                        <a
                            href="https://github.com"
                            target="_blank"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ $t('welcome.github') }}
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

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-pulse {
    animation: pulse-slow 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-fade-in-delay {
    animation: fade-in 0.8s ease-out 0.2s both;
}

.animate-fade-in-delay-2 {
    animation: fade-in 0.8s ease-out 0.4s both;
}
</style>
