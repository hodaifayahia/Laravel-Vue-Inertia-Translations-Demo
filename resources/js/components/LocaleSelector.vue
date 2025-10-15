<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import SetLocaleController from '@/actions/App/Http/Controllers/Settings/SetLocaleController';

const page = usePage();
const locale = page.props.locale as string;

const selectedLocale = ref(locale);

const locales = [
    { code: 'en', label: 'EN' },
    { code: 'lt', label: 'LT' },
];

const selectLocale = (code: string) => {
    router.visit(SetLocaleController.url(), {
        method: 'put',
        data: {
            'locale': code
        }
    });
    
    loadLanguageAsync(code);
    selectedLocale.value = code;
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="sm"
                class="h-8 gap-1 text-sm font-medium"
            >
                {{ locales.find(l => l.code === selectedLocale)?.label }}
                <ChevronDown class="h-4 w-4 opacity-50" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-32">
            <DropdownMenuItem
                v-for="locale in locales"
                :key="locale.code"
                @click="selectLocale(locale.code)"
                class="cursor-pointer"
                :class="{ 'bg-sidebar-accent': locale.code == selectedLocale }"
            >
                {{ locale.label }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
