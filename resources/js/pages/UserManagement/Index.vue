<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem, type User } from '@/types';
import { computed, ref, watch } from 'vue';
import { wTrans } from 'laravel-vue-i18n';
import {
    Search,
    Plus,
    MoreVertical,
    Edit,
    Trash2,
    Mail,
    Calendar,
    Filter,
    CheckCircle2,
    XCircle,
    Users,
} from 'lucide-vue-next';
import CreateUserDialog from '@/components/CreateUserDialog.vue';
import EditUserDialog from '@/components/EditUserDialog.vue';
import DeleteUserDialog from '@/components/DeleteUserDialog.vue';
import { useInitials } from '@/composables/useInitials';
import { usePermissions } from '@/composables/usePermissions';
import * as usersRoutes from '@/routes/users';

interface UserData extends User {
    locale: string;
    roles?: Array<{ id: number; name: string }>;
}

interface Props {
    users: {
        data: UserData[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: {
        search?: string;
        verified?: string;
        sort_by?: string;
        sort_direction?: string;
    };
    roles?: Array<{ id: number; name: string }>;
}

const props = defineProps<Props>();

// Get permissions
const { hasPermission } = usePermissions();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: wTrans('users.title'),
        href: usersRoutes.index().url,
    },
];

// Permission checks
const canCreateUsers = computed(() => hasPermission('create users'));
const canEditUsers = computed(() => hasPermission('edit users'));
const canDeleteUsers = computed(() => hasPermission('delete users'));

// Search and filters
const searchQuery = ref(props.filters.search || '');
const verifiedFilter = ref(props.filters.verified || 'all');

// Dialogs state
const createDialogOpen = ref(false);
const editDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedUser = ref<UserData | null>(null);

// Watch for search changes and debounce
let searchTimeout: ReturnType<typeof setTimeout>;
watch(searchQuery, (value: string) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(
            usersRoutes.index().url,
            { search: value || undefined, verified: verifiedFilter.value !== 'all' ? verifiedFilter.value : undefined },
            { preserveState: true, replace: true }
        );
    }, 300);
});

// Watch for filter changes
watch(verifiedFilter, (value: string) => {
    router.get(
        usersRoutes.index().url,
        { search: searchQuery.value || undefined, verified: value !== 'all' ? value : undefined },
        { preserveState: true, replace: true }
    );
});

const handleEdit = (user: UserData) => {
    selectedUser.value = user;
    editDialogOpen.value = true;
};

const handleDelete = (user: UserData) => {
    selectedUser.value = user;
    deleteDialogOpen.value = true;
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const getLocaleLabel = (locale: string) => {
    const labels: Record<string, string> = {
        en: 'English',
        fr: 'Français',
        ar: 'العربية',
        lt: 'Lietuvių',
    };
    return labels[locale] || locale;
};
</script>

<template>
    <Head :title="$t('users.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ $t('users.title') }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ $t('users.description') }}
                    </p>
                </div>
                <Button 
                    v-if="canCreateUsers" 
                    @click="createDialogOpen = true" 
                    size="lg"
                >
                    <Plus class="mr-2 h-5 w-5" />
                    {{ $t('users.add_user') }}
                </Button>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            {{ $t('users.total_users') }}
                        </CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ users.total }}</div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            {{ $t('users.verified_users') }}
                        </CardTitle>
                        <CheckCircle2 class="h-4 w-4 text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ users.data.filter((u: UserData) => u.email_verified_at).length }}
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            {{ $t('users.unverified_users') }}
                        </CardTitle>
                        <XCircle class="h-4 w-4 text-orange-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ users.data.filter((u: UserData) => !u.email_verified_at).length }}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters and Search -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ $t('users.filters') }}</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-4 md:flex-row">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="searchQuery"
                            :placeholder="$t('users.search_placeholder')"
                            class="pl-10"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <Filter class="h-4 w-4 text-muted-foreground" />
                        <select
                            v-model="verifiedFilter"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:w-[200px]"
                        >
                            <option value="all">{{ $t('users.all_users') }}</option>
                            <option value="true">{{ $t('users.verified_only') }}</option>
                            <option value="false">{{ $t('users.unverified_only') }}</option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <!-- Users Table -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ $t('users.user_list') }}</CardTitle>
                    <CardDescription>
                        {{ $t('users.showing_results', { total: users.total }) }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b text-xs uppercase text-muted-foreground">
                                <tr>
                                    <th class="px-6 py-3">{{ $t('users.user') }}</th>
                                    <th class="px-6 py-3">{{ $t('users.email') }}</th>
                                    <th class="px-6 py-3">{{ $t('users.roles') }}</th>
                                    <th class="px-6 py-3">{{ $t('users.status') }}</th>
                                    <th class="px-6 py-3">{{ $t('users.locale') }}</th>
                                    <th class="px-6 py-3">{{ $t('users.joined') }}</th>
                                    <th class="px-6 py-3 text-right">{{ $t('users.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <Avatar>
                                                <AvatarFallback>
                                                    {{ useInitials(user.name) }}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div class="font-medium">{{ user.name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-muted-foreground">
                                            <Mail class="h-4 w-4" />
                                            {{ user.email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <Badge
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                variant="secondary"
                                                class="text-xs"
                                            >
                                                {{ role.name }}
                                            </Badge>
                                            <Badge v-if="!user.roles || user.roles.length === 0" variant="outline" class="text-xs">
                                                No roles
                                            </Badge>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Badge
                                            :variant="user.email_verified_at ? 'default' : 'secondary'"
                                        >
                                            <CheckCircle2
                                                v-if="user.email_verified_at"
                                                class="mr-1 h-3 w-3"
                                            />
                                            <XCircle v-else class="mr-1 h-3 w-3" />
                                            {{
                                                user.email_verified_at
                                                    ? $t('users.verified')
                                                    : $t('users.unverified')
                                            }}
                                        </Badge>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Badge variant="outline">
                                            {{ getLocaleLabel(user.locale) }}
                                        </Badge>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-muted-foreground">
                                            <Calendar class="h-4 w-4" />
                                            {{ formatDate(user.created_at) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <DropdownMenu v-if="canEditUsers || canDeleteUsers">
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm">
                                                    <MoreVertical class="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuLabel>
                                                    {{ $t('users.actions') }}
                                                </DropdownMenuLabel>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem 
                                                    v-if="canEditUsers" 
                                                    @click="handleEdit(user)"
                                                >
                                                    <Edit class="mr-2 h-4 w-4" />
                                                    {{ $t('users.edit') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem
                                                    v-if="canDeleteUsers"
                                                    @click="handleDelete(user)"
                                                    class="text-destructive focus:text-destructive"
                                                >
                                                    <Trash2 class="mr-2 h-4 w-4" />
                                                    {{ $t('users.delete') }}
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                        <span v-else class="text-sm text-muted-foreground">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Empty State -->
                        <div
                            v-if="users.data.length === 0"
                            class="flex flex-col items-center justify-center py-12 text-center"
                        >
                            <Users class="mb-4 h-12 w-12 text-muted-foreground/50" />
                            <h3 class="mb-2 text-lg font-semibold">{{ $t('users.no_users') }}</h3>
                            <p class="mb-4 text-sm text-muted-foreground">
                                {{ $t('users.no_users_description') }}
                            </p>
                            <Button v-if="canCreateUsers" @click="createDialogOpen = true">
                                <Plus class="mr-2 h-4 w-4" />
                                {{ $t('users.add_first_user') }}
                            </Button>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="users.data.length > 0 && users.last_page > 1"
                        class="mt-6 flex items-center justify-between"
                    >
                        <div class="text-sm text-muted-foreground">
                            {{
                                $t('users.pagination_info', {
                                    from: (users.current_page - 1) * users.per_page + 1,
                                    to: Math.min(users.current_page * users.per_page, users.total),
                                    total: users.total,
                                })
                            }}
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in users.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium transition-colors',
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : 'bg-background hover:bg-muted',
                                    !link.url && 'cursor-not-allowed opacity-50',
                                ]"
                                :disabled="!link.url"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Dialogs -->
        <CreateUserDialog v-model:open="createDialogOpen" :roles="roles" />
        <EditUserDialog
            v-if="selectedUser"
            v-model:open="editDialogOpen"
            :user="selectedUser"
            :roles="roles"
        />
        <DeleteUserDialog
            v-if="selectedUser"
            v-model:open="deleteDialogOpen"
            :user="selectedUser"
        />
    </AppLayout>
</template>
