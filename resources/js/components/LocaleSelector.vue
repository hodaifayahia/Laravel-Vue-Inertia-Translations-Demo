<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { ChevronDown } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';

const page = usePage();
const locale = page.props.locale as string;
const isAuthenticated = computed(() => !!page.props.auth?.user);

const selectedLocale = ref(locale);

interface LocaleOption {
    code: string;
    label: string;
}

const locales: LocaleOption[] = [
    { code: 'en', label: 'EN' },
    { code: 'fr', label: 'FR' },
    { code: 'ar', label: 'AR' },
    { code: 'lt', label: 'LT' },
];

const selectLocale = (code: string) => {
    // Load the language translations
    loadLanguageAsync(code);
    selectedLocale.value = code;
    
    // Update document direction for RTL languages
    document.documentElement.setAttribute('dir', code === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', code);
    
    // For authenticated users, save preference to database
    if (isAuthenticated.value) {
        router.put('/settings/locale', {
            locale: code
        }, {
            preserveScroll: true,
            preserveState: true,
        });
    } else {
        // For guests, just reload the current page with locale parameter
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('locale', code);
        router.get(currentUrl.pathname + currentUrl.search, {}, {
            preserveScroll: true,
            preserveState: false,
        });
    }
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
                {{ locales.find((locale: LocaleOption) => locale.code === selectedLocale)?.label }}
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
