<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { wTrans } from 'laravel-vue-i18n';
import { MessageCircle, Users, Shield, Key } from 'lucide-vue-next';
import { usePermissions } from '@/composables/usePermissions';

const { hasPermission } = usePermissions();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: wTrans('dashboard.title'),
        href: dashboard().url,
    },
];

const quickLinks = [
    {
        title: wTrans('sidebar.chat'),
        description: wTrans('dashboard.chat_description'),
        href: '/chat',
        icon: MessageCircle,
        color: 'bg-blue-500',
        permission: 'view chat',
    },
    {
        title: wTrans('sidebar.users'),
        description: wTrans('dashboard.users_description'),
        href: '/users',
        icon: Users,
        color: 'bg-green-500',
        permission: 'view users sidebar',
    },
    {
        title: wTrans('sidebar.roles'),
        description: wTrans('dashboard.roles_description'),
        href: '/roles',
        icon: Shield,
        color: 'bg-purple-500',
        permission: 'view roles sidebar',
    },
].filter(link => hasPermission(link.permission));
</script>

<template>
    <Head :title="$t('dashboard.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Quick Access Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Link
                    v-for="link in quickLinks"
                    :key="link.href"
                    :href="link.href"
                    class="group relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-white dark:bg-gray-800 p-6 transition-all hover:shadow-lg hover:scale-105"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div :class="[link.color, 'p-2 rounded-lg']">
                                    <component :is="link.icon" class="w-5 h-5 text-white" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ link.title }}
                                </h3>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ link.description }}
                            </p>
                        </div>
                        <svg 
                            class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </Link>
            </div>

            <!-- Main Content Area -->
            <div class="relative min-h-[50vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border bg-white dark:bg-gray-800 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $t('dashboard.welcome') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $t('dashboard.welcome_message') }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
