<script setup lang="ts">
import LocaleSelector from '@/components/LocaleSelector.vue';
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, home, login, register } from '@/routes';
import type { Component } from 'vue';
import {
    ArrowRight,
    BarChart3,
    CalendarCheck,
    Globe2,
    Languages,
    MessageCircle,
    ShieldCheck,
    Sparkles,
    Users,
} from 'lucide-vue-next';

type TranslationKey = string;

type Feature = {
    titleKey: TranslationKey;
    descriptionKey: TranslationKey;
    icon: Component;
};

type Stat = {
    labelKey: TranslationKey;
    valueKey: TranslationKey;
    sublabelKey: TranslationKey;
};

type Testimonial = {
    quoteKey: TranslationKey;
    nameKey: TranslationKey;
    roleKey: TranslationKey;
    companyKey: TranslationKey;
};

type PurposeItem = {
    titleKey: TranslationKey;
    descriptionKey: TranslationKey;
    icon: Component;
};

const heroHighlights: Array<{ labelKey: TranslationKey; detailKey: TranslationKey }> = [
    {
        labelKey: 'landing.hero.highlights.translate_once.label',
        detailKey: 'landing.hero.highlights.translate_once.detail',
    },
    {
        labelKey: 'landing.hero.highlights.launch_faster.label',
        detailKey: 'landing.hero.highlights.launch_faster.detail',
    },
];

const purpose: PurposeItem[] = [
    {
        titleKey: 'landing.purpose.items.purpose_built.title',
        descriptionKey: 'landing.purpose.items.purpose_built.description',
        icon: Sparkles,
    },
    {
        titleKey: 'landing.purpose.items.bridge_gap.title',
        descriptionKey: 'landing.purpose.items.bridge_gap.description',
        icon: Globe2,
    },
    {
        titleKey: 'landing.purpose.items.operationalize.title',
        descriptionKey: 'landing.purpose.items.operationalize.description',
        icon: CalendarCheck,
    },
];

const features: Feature[] = [
    {
        titleKey: 'landing.features.items.translation_hub.title',
        descriptionKey: 'landing.features.items.translation_hub.description',
        icon: Languages,
    },
    {
        titleKey: 'landing.features.items.permission_workflows.title',
        descriptionKey: 'landing.features.items.permission_workflows.description',
        icon: ShieldCheck,
    },
    {
        titleKey: 'landing.features.items.team_pulse.title',
        descriptionKey: 'landing.features.items.team_pulse.description',
        icon: BarChart3,
    },
    {
        titleKey: 'landing.features.items.feedback_loop.title',
        descriptionKey: 'landing.features.items.feedback_loop.description',
        icon: MessageCircle,
    },
];

const stats: Stat[] = [
    {
        labelKey: 'landing.stats.teams_onboarded.label',
        valueKey: 'landing.stats.teams_onboarded.value',
        sublabelKey: 'landing.stats.teams_onboarded.sublabel',
    },
    {
        labelKey: 'landing.stats.locale_coverage.label',
        valueKey: 'landing.stats.locale_coverage.value',
        sublabelKey: 'landing.stats.locale_coverage.sublabel',
    },
    {
        labelKey: 'landing.stats.turnaround_acceleration.label',
        valueKey: 'landing.stats.turnaround_acceleration.value',
        sublabelKey: 'landing.stats.turnaround_acceleration.sublabel',
    },
    {
        labelKey: 'landing.stats.collaboration_satisfaction.label',
        valueKey: 'landing.stats.collaboration_satisfaction.value',
        sublabelKey: 'landing.stats.collaboration_satisfaction.sublabel',
    },
];

const testimonials: Testimonial[] = [
    {
        quoteKey: 'landing.testimonials.maya.quote',
        nameKey: 'landing.testimonials.maya.name',
        roleKey: 'landing.testimonials.maya.role',
        companyKey: 'landing.testimonials.maya.company',
    },
    {
        quoteKey: 'landing.testimonials.elijah.quote',
        nameKey: 'landing.testimonials.elijah.name',
        roleKey: 'landing.testimonials.elijah.role',
        companyKey: 'landing.testimonials.elijah.company',
    },
];

const ctaLinks = [
    {
        textKey: 'landing.cta.links.explore_dashboard',
        href: dashboard().url,
    },
    {
        textKey: 'landing.cta.links.return_welcome',
        href: home().url,
    },
];

const year = new Date().getFullYear();
</script>

<template>
    <Head :title="$t('landing.meta.title')" />

    <div class="relative min-h-screen overflow-hidden bg-slate-950 text-slate-100">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-[-10%] top-[10%] h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="absolute right-[-20%] top-[35%] h-96 w-96 rounded-full bg-purple-500/20 blur-3xl"></div>
            <div class="absolute bottom-[-15%] left-[25%] h-80 w-80 rounded-full bg-emerald-500/20 blur-3xl"></div>
        </div>

        <div class="relative z-10 flex min-h-screen flex-col">
            <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-6 py-6">
                    <div class="flex items-center gap-3 text-lg font-semibold">
                        <Sparkles class="h-6 w-6 text-indigo-400" />
                        <span>{{ $t('landing.brand') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <nav class="hidden items-center gap-4 text-sm font-medium md:flex">
                            <Link :href="home().url" class="rounded-full px-4 py-2 text-slate-300 transition hover:text-white">
                                {{ $t('landing.nav.classic_welcome') }}
                            </Link>
                            <Link :href="dashboard().url" class="rounded-full px-4 py-2 text-slate-300 transition hover:text-white">
                                {{ $t('landing.nav.dashboard') }}
                            </Link>
                            <Link :href="login().url" class="rounded-full border border-indigo-500/40 px-4 py-2 text-indigo-300 transition hover:border-indigo-500 hover:text-white">
                                {{ $t('auth.login') }}
                            </Link>
                        </nav>
                        <LocaleSelector />
                    </div>
                </div>
            </header>

            <main class="flex-1">
                <section class="relative py-24 sm:py-28">
                    <div class="mx-auto flex max-w-6xl flex-col gap-16 px-6 lg:flex-row lg:items-center">
                        <div class="flex-1">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">
                                <Users class="h-4 w-4" />
                                {{ $t('landing.hero.badge') }}
                            </span>
                            <h1 class="mt-6 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                                {{ $t('landing.hero.title') }}
                            </h1>
                            <p class="mt-6 text-lg text-slate-300 sm:text-xl">
                                {{ $t('landing.hero.subtitle') }}
                            </p>
                            <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center">
                                <Link
                                    :href="register().url"
                                    class="inline-flex items-center justify-center gap-2 rounded-full bg-indigo-500 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400"
                                >
                                    {{ $t('landing.hero.primary_cta') }}
                                    <ArrowRight class="h-4 w-4" />
                                </Link>
                                <Link
                                    :href="login().url"
                                    class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 px-6 py-3 font-semibold text-slate-200 transition hover:border-white hover:text-white"
                                >
                                    {{ $t('landing.hero.secondary_cta') }}
                                </Link>
                            </div>
                            <dl class="mt-10 grid gap-6 sm:grid-cols-2">
                                <div
                                    v-for="item in heroHighlights"
                                    :key="item.labelKey"
                                    class="rounded-2xl border border-white/10 bg-white/5 p-5"
                                >
                                    <dt class="text-sm font-semibold uppercase tracking-wide text-slate-300">
                                        {{ $t(item.labelKey) }}
                                    </dt>
                                    <dd class="mt-2 text-base text-slate-100">
                                        {{ $t(item.detailKey) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="flex-1">
                            <div class="relative overflow-hidden rounded-3xl border border-white/15 bg-gradient-to-br from-slate-900/80 via-slate-900/30 to-slate-800/40 p-8 shadow-2xl">
                                <div class="absolute -left-32 -top-28 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl"></div>
                                <div class="absolute -bottom-24 -right-28 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl"></div>
                                <div class="relative flex flex-col gap-6">
                                    <div class="flex items-center gap-3 text-sm font-semibold text-indigo-200">
                                        <Sparkles class="h-5 w-5" />
                                        {{ $t('landing.purpose.badge') }}
                                    </div>
                                    <h2 class="text-2xl font-semibold text-white">
                                        {{ $t('landing.purpose.title') }}
                                    </h2>
                                    <p class="text-base text-slate-300">
                                        {{ $t('landing.purpose.description') }}
                                    </p>
                                    <ul class="grid gap-4">
                                        <li
                                            v-for="item in purpose"
                                            :key="item.titleKey"
                                            class="flex gap-4 rounded-2xl border border-white/10 bg-white/5 p-4"
                                        >
                                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-200">
                                                <component :is="item.icon" class="h-6 w-6" />
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-white">
                                                    {{ $t(item.titleKey) }}
                                                </h3>
                                                <p class="mt-1 text-sm text-slate-300">
                                                    {{ $t(item.descriptionKey) }}
                                                </p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-y border-white/10 bg-slate-900/40 py-16">
                    <div class="mx-auto grid max-w-6xl gap-8 px-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="stat in stats"
                            :key="stat.labelKey"
                            class="rounded-3xl border border-white/10 bg-slate-950/40 p-6 text-center"
                        >
                            <dt class="text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">
                                {{ $t(stat.labelKey) }}
                            </dt>
                            <dd class="mt-4 text-3xl font-bold text-white">
                                {{ $t(stat.valueKey) }}
                            </dd>
                            <p class="mt-3 text-sm text-slate-300">
                                {{ $t(stat.sublabelKey) }}
                            </p>
                        </div>
                    </div>
                </section>

                <section class="py-20">
                    <div class="mx-auto max-w-6xl px-6">
                        <div class="max-w-2xl">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">
                                <Globe2 class="h-4 w-4" />
                                {{ $t('landing.features.badge') }}
                            </span>
                            <h2 class="mt-6 text-3xl font-semibold text-white sm:text-4xl">
                                {{ $t('landing.features.title') }}
                            </h2>
                            <p class="mt-4 text-base text-slate-300">
                                {{ $t('landing.features.description') }}
                            </p>
                        </div>
                        <div class="mt-12 grid gap-6 sm:grid-cols-2">
                            <article
                                v-for="feature in features"
                                :key="feature.titleKey"
                                class="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-6 transition hover:border-indigo-400/60 hover:bg-indigo-500/10"
                            >
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-200">
                                    <component :is="feature.icon" class="h-6 w-6" />
                                </div>
                                <h3 class="mt-6 text-xl font-semibold text-white">
                                    {{ $t(feature.titleKey) }}
                                </h3>
                                <p class="mt-3 text-sm text-slate-300">
                                    {{ $t(feature.descriptionKey) }}
                                </p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="border-y border-white/10 bg-slate-900/30 py-20">
                    <div class="mx-auto max-w-5xl px-6">
                        <div class="text-center">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">
                                <MessageCircle class="h-4 w-4" />
                                {{ $t('landing.testimonials.badge') }}
                            </span>
                            <h2 class="mt-6 text-3xl font-semibold text-white">
                                {{ $t('landing.testimonials.title') }}
                            </h2>
                            <p class="mt-4 text-base text-slate-300">
                                {{ $t('landing.testimonials.description') }}
                            </p>
                        </div>
                        <div class="mt-12 grid gap-8 md:grid-cols-2">
                            <figure
                                v-for="testimonial in testimonials"
                                :key="testimonial.quoteKey"
                                class="rounded-3xl border border-white/10 bg-white/5 p-6 text-left"
                            >
                                <blockquote class="text-base leading-relaxed text-slate-200">
                                    {{ $t(testimonial.quoteKey) }}
                                </blockquote>
                                <figcaption class="mt-6 text-sm text-slate-300">
                                    <span class="font-semibold text-white">{{ $t(testimonial.nameKey) }}</span>
                                    · {{ $t(testimonial.roleKey) }} @ {{ $t(testimonial.companyKey) }}
                                </figcaption>
                            </figure>
                        </div>
                    </div>
                </section>

                <section class="py-20">
                    <div class="mx-auto flex max-w-5xl flex-col items-center gap-8 rounded-3xl border border-white/10 bg-white/5 px-8 py-16 text-center">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-indigo-300">
                            <ArrowRight class="h-4 w-4" />
                            {{ $t('landing.cta.badge') }}
                        </div>
                        <h2 class="text-3xl font-semibold text-white sm:text-4xl">
                            {{ $t('landing.cta.title') }}
                        </h2>
                        <p class="max-w-2xl text-base text-slate-300">
                            {{ $t('landing.cta.description') }}
                        </p>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Link
                                v-for="link in ctaLinks"
                                :key="link.textKey"
                                :href="link.href"
                                class="inline-flex items-center gap-2 rounded-full border border-white/15 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:border-white hover:text-white"
                            >
                                {{ $t(link.textKey) }}
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-white/10 bg-slate-950/80 py-8">
                <div class="mx-auto flex max-w-6xl flex-col gap-4 px-6 text-sm text-slate-400 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 text-slate-300">
                        <Sparkles class="h-5 w-5 text-indigo-400" />
                        <span>{{ $t('landing.brand') }}</span>
                    </div>
                    <p>&copy; {{ year }} {{ $t('landing.footer.tagline') }}</p>
                </div>
            </footer>
        </div>
    </div>
</template>
