<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const isItemActive = (item: NavItem): boolean => {
    if (item.href && urlIsActive(item.href, page.url)) {
        return true;
    }
    if (item.items) {
        return item.items.some(subItem => subItem.href && urlIsActive(subItem.href, page.url));
    }
    return false;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ $t('sidebar.platform') }}</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- Item with sub-items -->
                <Collapsible v-if="item.items && item.items.length > 0" as-child :default-open="isItemActive(item)">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title">
                                <component :is="item.icon" v-if="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="subItem.href ? urlIsActive(subItem.href, page.url) : false"
                                    >
                                        <Link :href="subItem.href || '#'">
                                            <component :is="subItem.icon" v-if="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Simple item without sub-items -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="item.href ? urlIsActive(item.href, page.url) : false"
                        :tooltip="item.title"
                    >
                        <Link :href="item.href || '#'">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
