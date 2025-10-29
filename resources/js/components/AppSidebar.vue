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
import { BookOpen, Folder, LayoutGrid, Users, Shield, Key } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { wTrans } from 'laravel-vue-i18n';

const mainNavItems: NavItem[] = [
    {
        title: wTrans('sidebar.dashboard'),
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: wTrans('sidebar.users'),
        href: usersRoutes.index(),
        icon: Users,
    },
    {
        title: wTrans('sidebar.roles'),
        href: rolesRoutes.index(),
        icon: Shield,
    },
    {
        title: wTrans('sidebar.permissions'),
        href: permissionsRoutes.index(),
        icon: Key,
    },
];

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
    <Sidebar collapsible="icon" variant="inset">
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
