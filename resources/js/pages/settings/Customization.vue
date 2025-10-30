<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { wTrans } from 'laravel-vue-i18n';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs';
import { 
    Palette, 
    Home, 
    Image as ImageIcon, 
    Type,
    Sparkles,
    Save,
    RotateCcw,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

interface Props {
    settings?: {
        welcome_title?: string;
        welcome_subtitle?: string;
        welcome_description?: string;
        primary_color?: string;
        secondary_color?: string;
        accent_color?: string;
        logo_text?: string;
        favicon_url?: string;
        show_welcome_page?: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: wTrans('settings.customization.title'),
        href: '/settings/customization',
    },
];

// Form for welcome page settings
const welcomeForm = useForm({
    welcome_title: props.settings?.welcome_title || 'Welcome to Laravel',
    welcome_subtitle: props.settings?.welcome_subtitle || 'Get started with your application',
    welcome_description: props.settings?.welcome_description || 'Build something amazing with Laravel, Vue, and Inertia.',
    show_welcome_page: props.settings?.show_welcome_page ?? true,
});

// Form for theme settings
const themeForm = useForm({
    primary_color: props.settings?.primary_color || '#3b82f6',
    secondary_color: props.settings?.secondary_color || '#8b5cf6',
    accent_color: props.settings?.accent_color || '#10b981',
});

// Form for branding settings
const brandingForm = useForm({
    logo_text: props.settings?.logo_text || 'Laravel',
    favicon_url: props.settings?.favicon_url || '',
});

const activeTab = ref('welcome');

const saveWelcomeSettings = () => {
    welcomeForm.post('/settings/customization/welcome', {
        preserveScroll: true,
    });
};

const saveThemeSettings = () => {
    themeForm.post('/settings/customization/theme', {
        preserveScroll: true,
        onSuccess: () => {
            // Apply colors to CSS variables
            document.documentElement.style.setProperty('--primary', themeForm.primary_color);
            document.documentElement.style.setProperty('--secondary', themeForm.secondary_color);
            document.documentElement.style.setProperty('--accent', themeForm.accent_color);
        },
    });
};

const saveBrandingSettings = () => {
    brandingForm.post('/settings/customization/branding', {
        preserveScroll: true,
    });
};

const resetToDefaults = () => {
    if (confirm('Are you sure you want to reset all customization settings to defaults?')) {
        router.post('/settings/customization/reset', {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Reset forms to default values
                welcomeForm.welcome_title = 'Welcome to Laravel';
                welcomeForm.welcome_subtitle = 'Get started with your application';
                welcomeForm.welcome_description = 'Build something amazing with Laravel, Vue, and Inertia.';
                welcomeForm.show_welcome_page = true;
                
                themeForm.primary_color = '#3b82f6';
                themeForm.secondary_color = '#8b5cf6';
                themeForm.accent_color = '#10b981';
                
                brandingForm.logo_text = 'Laravel';
                brandingForm.favicon_url = '';
                
                // Reset CSS variables
                document.documentElement.style.setProperty('--primary', '#3b82f6');
                document.documentElement.style.setProperty('--secondary', '#8b5cf6');
                document.documentElement.style.setProperty('--accent', '#10b981');
            },
        });
    }
};

// Watch for color changes and apply them live
watch(() => themeForm.primary_color, (newColor) => {
    document.documentElement.style.setProperty('--primary-preview', newColor);
});

watch(() => themeForm.secondary_color, (newColor) => {
    document.documentElement.style.setProperty('--secondary-preview', newColor);
});

watch(() => themeForm.accent_color, (newColor) => {
    document.documentElement.style.setProperty('--accent-preview', newColor);
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('settings.customization.title')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('settings.customization.heading')"
                    :description="$t('settings.customization.description')"
                />

                <Tabs v-model="activeTab" class="w-full">
                    <TabsList class="grid w-full grid-cols-3">
                        <TabsTrigger value="welcome">
                            <Home class="mr-2 h-4 w-4" />
                            {{ $t('settings.customization.tabs.welcome') }}
                        </TabsTrigger>
                        <TabsTrigger value="theme">
                            <Palette class="mr-2 h-4 w-4" />
                            {{ $t('settings.customization.tabs.theme') }}
                        </TabsTrigger>
                        <TabsTrigger value="branding">
                            <Sparkles class="mr-2 h-4 w-4" />
                            {{ $t('settings.customization.tabs.branding') }}
                        </TabsTrigger>
                    </TabsList>

                    <!-- Welcome Page Settings -->
                    <TabsContent value="welcome" class="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ $t('settings.customization.welcome.title') }}</CardTitle>
                                <CardDescription>
                                    {{ $t('settings.customization.welcome.description') }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form @submit.prevent="saveWelcomeSettings" class="space-y-6">
                                    <div class="flex items-center justify-between space-x-2">
                                        <div class="space-y-0.5">
                                            <Label for="show-welcome">
                                                {{ $t('settings.customization.welcome.show_page') }}
                                            </Label>
                                            <p class="text-sm text-muted-foreground">
                                                {{ $t('settings.customization.welcome.show_page_description') }}
                                            </p>
                                        </div>
                                        <Switch
                                            id="show-welcome"
                                            v-model:checked="welcomeForm.show_welcome_page"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="welcome-title">
                                            {{ $t('settings.customization.welcome.page_title') }}
                                        </Label>
                                        <Input
                                            id="welcome-title"
                                            v-model="welcomeForm.welcome_title"
                                            :placeholder="$t('settings.customization.welcome.page_title_placeholder')"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="welcome-subtitle">
                                            {{ $t('settings.customization.welcome.page_subtitle') }}
                                        </Label>
                                        <Input
                                            id="welcome-subtitle"
                                            v-model="welcomeForm.welcome_subtitle"
                                            :placeholder="$t('settings.customization.welcome.page_subtitle_placeholder')"
                                        />
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="welcome-description">
                                            {{ $t('settings.customization.welcome.page_description') }}
                                        </Label>
                                        <Textarea
                                            id="welcome-description"
                                            v-model="welcomeForm.welcome_description"
                                            :placeholder="$t('settings.customization.welcome.page_description_placeholder')"
                                            rows="4"
                                        />
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <Button type="submit" :disabled="welcomeForm.processing">
                                            <Save class="mr-2 h-4 w-4" />
                                            {{ welcomeForm.processing ? $t('settings.customization.saving') : $t('settings.customization.save') }}
                                        </Button>

                                        <Transition
                                            enter-active-class="transition ease-in-out"
                                            enter-from-class="opacity-0"
                                            leave-active-class="transition ease-in-out"
                                            leave-to-class="opacity-0"
                                        >
                                            <p
                                                v-show="welcomeForm.recentlySuccessful"
                                                class="text-sm text-green-600"
                                            >
                                                {{ $t('settings.customization.saved') }}
                                            </p>
                                        </Transition>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- Theme Settings -->
                    <TabsContent value="theme" class="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ $t('settings.customization.theme.title') }}</CardTitle>
                                <CardDescription>
                                    {{ $t('settings.customization.theme.description') }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form @submit.prevent="saveThemeSettings" class="space-y-6">
                                    <div class="space-y-2">
                                        <Label for="primary-color">
                                            {{ $t('settings.customization.theme.primary_color') }}
                                        </Label>
                                        <div class="flex items-center gap-3">
                                            <Input
                                                id="primary-color"
                                                type="color"
                                                v-model="themeForm.primary_color"
                                                class="h-12 w-20 cursor-pointer"
                                            />
                                            <Input
                                                v-model="themeForm.primary_color"
                                                :placeholder="$t('settings.customization.theme.color_placeholder')"
                                                class="flex-1"
                                            />
                                            <div 
                                                class="h-12 w-12 rounded-md border shadow-sm"
                                                :style="{ backgroundColor: themeForm.primary_color }"
                                            />
                                        </div>
                                        <p class="text-xs text-muted-foreground">
                                            {{ $t('settings.customization.theme.primary_color_description') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="secondary-color">
                                            {{ $t('settings.customization.theme.secondary_color') }}
                                        </Label>
                                        <div class="flex items-center gap-3">
                                            <Input
                                                id="secondary-color"
                                                type="color"
                                                v-model="themeForm.secondary_color"
                                                class="h-12 w-20 cursor-pointer"
                                            />
                                            <Input
                                                v-model="themeForm.secondary_color"
                                                :placeholder="$t('settings.customization.theme.color_placeholder')"
                                                class="flex-1"
                                            />
                                            <div 
                                                class="h-12 w-12 rounded-md border shadow-sm"
                                                :style="{ backgroundColor: themeForm.secondary_color }"
                                            />
                                        </div>
                                        <p class="text-xs text-muted-foreground">
                                            {{ $t('settings.customization.theme.secondary_color_description') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="accent-color">
                                            {{ $t('settings.customization.theme.accent_color') }}
                                        </Label>
                                        <div class="flex items-center gap-3">
                                            <Input
                                                id="accent-color"
                                                type="color"
                                                v-model="themeForm.accent_color"
                                                class="h-12 w-20 cursor-pointer"
                                            />
                                            <Input
                                                v-model="themeForm.accent_color"
                                                :placeholder="$t('settings.customization.theme.color_placeholder')"
                                                class="flex-1"
                                            />
                                            <div 
                                                class="h-12 w-12 rounded-md border shadow-sm"
                                                :style="{ backgroundColor: themeForm.accent_color }"
                                            />
                                        </div>
                                        <p class="text-xs text-muted-foreground">
                                            {{ $t('settings.customization.theme.accent_color_description') }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg border bg-muted/50 p-4">
                                        <p class="mb-3 text-sm font-medium">{{ $t('settings.customization.theme.preview') }}</p>
                                        <div class="flex gap-3">
                                            <Button 
                                                type="button" 
                                                variant="default"
                                                :style="{ backgroundColor: themeForm.primary_color, borderColor: themeForm.primary_color }"
                                            >
                                                Primary
                                            </Button>
                                            <Button 
                                                type="button" 
                                                variant="secondary"
                                                :style="{ backgroundColor: themeForm.secondary_color, borderColor: themeForm.secondary_color }"
                                            >
                                                Secondary
                                            </Button>
                                            <Button 
                                                type="button" 
                                                variant="outline"
                                                :style="{ color: themeForm.accent_color, borderColor: themeForm.accent_color }"
                                            >
                                                Accent
                                            </Button>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <Button type="submit" :disabled="themeForm.processing">
                                            <Save class="mr-2 h-4 w-4" />
                                            {{ themeForm.processing ? $t('settings.customization.saving') : $t('settings.customization.save') }}
                                        </Button>

                                        <Transition
                                            enter-active-class="transition ease-in-out"
                                            enter-from-class="opacity-0"
                                            leave-active-class="transition ease-in-out"
                                            leave-to-class="opacity-0"
                                        >
                                            <p
                                                v-show="themeForm.recentlySuccessful"
                                                class="text-sm text-green-600"
                                            >
                                                {{ $t('settings.customization.saved') }}
                                            </p>
                                        </Transition>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- Branding Settings -->
                    <TabsContent value="branding" class="space-y-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>{{ $t('settings.customization.branding.title') }}</CardTitle>
                                <CardDescription>
                                    {{ $t('settings.customization.branding.description') }}
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <form @submit.prevent="saveBrandingSettings" class="space-y-6">
                                    <div class="space-y-2">
                                        <Label for="logo-text">
                                            {{ $t('settings.customization.branding.logo_text') }}
                                        </Label>
                                        <Input
                                            id="logo-text"
                                            v-model="brandingForm.logo_text"
                                            :placeholder="$t('settings.customization.branding.logo_text_placeholder')"
                                        />
                                        <p class="text-xs text-muted-foreground">
                                            {{ $t('settings.customization.branding.logo_text_description') }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="favicon">
                                            {{ $t('settings.customization.branding.favicon') }}
                                        </Label>
                                        <Input
                                            id="favicon"
                                            v-model="brandingForm.favicon_url"
                                            :placeholder="$t('settings.customization.branding.favicon_placeholder')"
                                        />
                                        <p class="text-xs text-muted-foreground">
                                            {{ $t('settings.customization.branding.favicon_description') }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg border bg-muted/50 p-4">
                                        <p class="mb-3 text-sm font-medium">{{ $t('settings.customization.branding.preview') }}</p>
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                                                <Type class="h-6 w-6" />
                                            </div>
                                            <div>
                                                <p class="font-semibold">{{ brandingForm.logo_text || 'Your Logo' }}</p>
                                                <p class="text-xs text-muted-foreground">Logo preview</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <Button type="submit" :disabled="brandingForm.processing">
                                            <Save class="mr-2 h-4 w-4" />
                                            {{ brandingForm.processing ? $t('settings.customization.saving') : $t('settings.customization.save') }}
                                        </Button>

                                        <Transition
                                            enter-active-class="transition ease-in-out"
                                            enter-from-class="opacity-0"
                                            leave-active-class="transition ease-in-out"
                                            leave-to-class="opacity-0"
                                        >
                                            <p
                                                v-show="brandingForm.recentlySuccessful"
                                                class="text-sm text-green-600"
                                            >
                                                {{ $t('settings.customization.saved') }}
                                            </p>
                                        </Transition>
                                    </div>
                                </form>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>

                <!-- Reset Section -->
                <Card class="border-destructive/50">
                    <CardHeader>
                        <CardTitle class="text-destructive">
                            {{ $t('settings.customization.reset.title') }}
                        </CardTitle>
                        <CardDescription>
                            {{ $t('settings.customization.reset.description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button 
                            variant="destructive" 
                            @click="resetToDefaults"
                        >
                            <RotateCcw class="mr-2 h-4 w-4" />
                            {{ $t('settings.customization.reset.button') }}
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
