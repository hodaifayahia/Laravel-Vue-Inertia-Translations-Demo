<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { wTrans } from 'laravel-vue-i18n';
import {
    Plus,
    MoreVertical,
    Edit,
    Trash2,
    Key,
    AlertTriangle,
} from 'lucide-vue-next';
import * as permissionsRoutes from '@/routes/permissions';

interface Permission {
    id: number;
    name: string;
}

interface Props {
    permissions: Permission[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: wTrans('permissions.title'),
        href: permissionsRoutes.index().url,
    },
];

// Dialog states
const createDialogOpen = ref(false);
const editDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedPermission = ref<Permission | null>(null);

// Forms
const createForm = useForm({
    name: '',
});

const editForm = useForm({
    name: '',
});

const handleCreate = () => {
    createForm.post(permissionsRoutes.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            createDialogOpen.value = false;
            createForm.reset();
        },
    });
};

const handleEdit = (permission: Permission) => {
    selectedPermission.value = permission;
    editForm.name = permission.name;
    editDialogOpen.value = true;
};

const handleUpdate = () => {
    if (!selectedPermission.value) return;
    
    editForm.put(permissionsRoutes.update({ permission: selectedPermission.value.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            editDialogOpen.value = false;
            editForm.reset();
            selectedPermission.value = null;
        },
    });
};

const handleDelete = (permission: Permission) => {
    selectedPermission.value = permission;
    deleteDialogOpen.value = true;
};

const confirmDelete = () => {
    if (!selectedPermission.value) return;
    
    router.delete(permissionsRoutes.destroy({ permission: selectedPermission.value.id }).url, {
        preserveScroll: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            selectedPermission.value = null;
        },
    });
};
</script>

<template>
    <Head :title="$t('permissions.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ $t('permissions.title') }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ $t('permissions.description') }}
                    </p>
                </div>
                <Button @click="createDialogOpen = true" size="lg">
                    <Plus class="mr-2 h-5 w-5" />
                    {{ $t('permissions.add_permission') }}
                </Button>
            </div>

            <!-- Stats Card -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">
                        {{ $t('permissions.total_permissions') }}
                    </CardTitle>
                    <Key class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ permissions.length }}</div>
                </CardContent>
            </Card>

            <!-- Permissions Table -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ $t('permissions.permission_list') }}</CardTitle>
                    <CardDescription>
                        {{ $t('permissions.description') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="permissions.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                        <Key class="mb-4 h-12 w-12 text-muted-foreground" />
                        <h3 class="text-lg font-semibold">{{ $t('permissions.no_permissions') }}</h3>
                        <p class="text-sm text-muted-foreground">{{ $t('permissions.no_permissions_description') }}</p>
                    </div>

                    <div v-else class="relative overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b text-xs uppercase text-muted-foreground">
                                <tr>
                                    <th class="px-6 py-3">{{ $t('permissions.name') }}</th>
                                    <th class="px-6 py-3 text-right">{{ $t('permissions.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="permission in permissions"
                                    :key="permission.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <Key class="h-4 w-4 text-muted-foreground" />
                                            <span class="font-medium">{{ permission.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm">
                                                    <MoreVertical class="h-4 w-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="end">
                                                <DropdownMenuLabel>{{ $t('permissions.actions') }}</DropdownMenuLabel>
                                                <DropdownMenuSeparator />
                                                <DropdownMenuItem @click="handleEdit(permission)">
                                                    <Edit class="mr-2 h-4 w-4" />
                                                    {{ $t('permissions.edit') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem
                                                    @click="handleDelete(permission)"
                                                    class="text-red-600"
                                                >
                                                    <Trash2 class="mr-2 h-4 w-4" />
                                                    {{ $t('permissions.delete') }}
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Create Permission Dialog -->
        <Dialog v-model:open="createDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ $t('permissions.create_permission') }}</DialogTitle>
                    <DialogDescription>
                        {{ $t('permissions.description') }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleCreate" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="create-name">{{ $t('permissions.name') }}</Label>
                        <Input
                            id="create-name"
                            v-model="createForm.name"
                            :placeholder="$t('permissions.name_placeholder')"
                            required
                        />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="createDialogOpen = false"
                        >
                            {{ $t('permissions.cancel') }}
                        </Button>
                        <Button type="submit" :disabled="createForm.processing">
                            {{ createForm.processing ? $t('permissions.creating') : $t('permissions.create') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Edit Permission Dialog -->
        <Dialog v-model:open="editDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ $t('permissions.edit_permission') }}</DialogTitle>
                    <DialogDescription>
                        {{ $t('permissions.description') }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleUpdate" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="edit-name">{{ $t('permissions.name') }}</Label>
                        <Input
                            id="edit-name"
                            v-model="editForm.name"
                            :placeholder="$t('permissions.name_placeholder')"
                            required
                        />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            @click="editDialogOpen = false"
                        >
                            {{ $t('permissions.cancel') }}
                        </Button>
                        <Button type="submit" :disabled="editForm.processing">
                            {{ editForm.processing ? $t('permissions.updating') : $t('permissions.update') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-destructive/10">
                            <AlertTriangle class="h-5 w-5 text-destructive" />
                        </div>
                        <div>
                            <DialogTitle>{{ $t('permissions.delete_permission') }}</DialogTitle>
                            <DialogDescription>
                                {{ $t('permissions.delete_warning') }}
                            </DialogDescription>
                        </div>
                    </div>
                </DialogHeader>

                <div class="rounded-lg border border-destructive/20 bg-destructive/5 p-4">
                    <p class="text-sm">
                        {{ $t('permissions.delete_confirm_message', { name: selectedPermission?.name || '' }) }}
                    </p>
                </div>

                <DialogFooter class="flex-row justify-end gap-2 sm:gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        @click="deleteDialogOpen = false"
                    >
                        {{ $t('permissions.cancel') }}
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        @click="confirmDelete"
                    >
                        {{ $t('permissions.delete_confirm') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
