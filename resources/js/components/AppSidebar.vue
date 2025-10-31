<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import * as usersRoutes from '@/routes/users';
import * as rolesRoutes from '@/routes/roles';
import * as permissionsRoutes from '@/routes/permissions';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, Shield, Key, MessageCircle, Settings, Calendar, CalendarCheck, Stethoscope, Clock } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { wTrans } from 'laravel-vue-i18n';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';

// Determine sidebar side based on language direction
// Watch for changes in the HTML dir attribute which changes when language changes
const documentDir = ref(document.documentElement.getAttribute('dir') || 'ltr');

const sidebarSide = computed(() => documentDir.value === 'rtl' ? 'right' : 'left');

// Watch for dir attribute changes using MutationObserver
let observer: MutationObserver | null = null;

onMounted(() => {
    observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes' && mutation.attributeName === 'dir') {
                documentDir.value = document.documentElement.getAttribute('dir') || 'ltr';
            }
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['dir'],
    });
});

onUnmounted(() => {
    if (observer) {
        observer.disconnect();
    }
});

// Get permissions
const { hasPermission } = usePermissions();

// Define all possible nav items with their required permissions
const allNavItems = [
    {
        title: wTrans('sidebar.dashboard'),
        href: dashboard(),
        icon: LayoutGrid,
        permission: 'view dashboard sidebar',
    },
    {
        title: wTrans('sidebar.chat'),
        href: '/chat',
        icon: MessageCircle,
        permission: 'view chat',
    },
    {
        title: wTrans('sidebar.bookings'),
        icon: Calendar,
        permission: 'can-book',
        items: [
            {
                title: wTrans('sidebar.book_appointment'),
                href: '/book',
                icon: CalendarCheck,
            },
            {
                title: wTrans('sidebar.my_appointments'),
                href: '/appointments',
                icon: Calendar,
            },
        ],
    },
    {
        title: wTrans('sidebar.bookings'),
        icon: Stethoscope,
        permission: 'book-sys',
        items: [
            {
                title: wTrans('sidebar.provider_profile'),
                href: '/provider/profile',
                icon: Stethoscope,
            },
            {
                title: wTrans('sidebar.provider_schedule'),
                href: '/provider/schedule',
                icon: Clock,
            },
            {
                title: wTrans('sidebar.my_appointments'),
                href: '/appointments',
                icon: Calendar,
            },
        ],
    },
    {
        title: wTrans('sidebar.users'),
        href: usersRoutes.index(),
        icon: Users,
        permission: 'view users sidebar',
    },
    {
        title: wTrans('sidebar.roles'),
        href: rolesRoutes.index(),
        icon: Shield,
        permission: 'view roles sidebar',
    },
    {
        title: wTrans('sidebar.permissions'),
        href: permissionsRoutes.index(),
        icon: Key,
        permission: 'view permissions sidebar',
    },
    {
        title: wTrans('sidebar.chat_permissions'),
        href: '/chat/permission-settings',
        icon: Settings,
        permission: 'manage chat',
    },
    {
        title: wTrans('sidebar.specializations'),
        href: '/specializations',
        icon: Stethoscope,
        permission: 'manage bookings',
    },
];

// Filter nav items based on user permissions
const mainNavItems = computed(() => 
    allNavItems.filter(item => hasPermission(item.permission))
);

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: wTrans('sidebar.documentation'),
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" :side="sidebarSide">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" v-if="mainNavItems.length > 0" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
