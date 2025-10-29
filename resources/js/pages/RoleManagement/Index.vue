<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
  Card, CardContent, CardDescription, CardHeader, CardTitle,
} from '@/components/ui/card';
import {
  Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle,
} from '@/components/ui/dialog';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import { Head, router, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref, watch, computed } from 'vue';
import { wTrans } from 'laravel-vue-i18n';
import { Plus, MoreVertical, Edit, Trash2, Shield, Key, AlertTriangle } from 'lucide-vue-next';
import * as rolesRoutes from '@/routes/roles';

interface Permission {
  id: number;
  name: string;
}

interface Role {
  id: number;
  name: string;
  permissions: Permission[];
}

interface Props {
  roles: Role[];
  permissions: Permission[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: wTrans('roles.title'), href: rolesRoutes.index().url },
];

// Dialog states
const createDialogOpen = ref(false);
const editDialogOpen = ref(false);
const deleteDialogOpen = ref(false);
const selectedRole = ref<Role | null>(null);

// Forms
const createForm = useForm({
  name: '',
});
const editForm = useForm({
  name: '',
});

// Single source of truth: store selected permission IDs
const createSelectedPermissionIds = ref<number[]>([]);
const editSelectedPermissionIds = ref<number[]>([]);

// Map for quick lookups
const permissionIdByName = computed<Record<string, number>>(() => {
  const map: Record<string, number> = {};
  for (const p of props.permissions) map[p.name] = p.id;
  return map;
});

const resetCreateState = () => {
  createForm.reset();
  createForm.clearErrors();
  createSelectedPermissionIds.value = [];
};

const resetEditState = () => {
  editForm.reset();
  editForm.clearErrors();
  editSelectedPermissionIds.value = [];
  selectedRole.value = null;
};

const handleCreate = () => {
  createForm
    .transform((data) => ({
      ...data,
      permissions: [...createSelectedPermissionIds.value],
    }))
    .post(rolesRoutes.store().url, {
      preserveScroll: true,
      onSuccess: () => { createDialogOpen.value = false; },
    });
};

const handleEdit = (role: Role) => {
  selectedRole.value = role;
  editForm.clearErrors();
  editForm.name = role.name;

  // Pre-fill selected permission IDs from role
  editSelectedPermissionIds.value = role.permissions.map((p) => p.id);

  editDialogOpen.value = true;
};

const handleUpdate = () => {
  if (!selectedRole.value) return;

  editForm
    .transform((data) => ({
      ...data,
      permissions: [...editSelectedPermissionIds.value],
    }))
    .put(rolesRoutes.update({ role: selectedRole.value.id }).url, {
      preserveScroll: true,
      onSuccess: () => { editDialogOpen.value = false; },
    });
};

const handleDelete = (role: Role) => {
  selectedRole.value = role;
  deleteDialogOpen.value = true;
};

const confirmDelete = () => {
  if (!selectedRole.value) return;

  router.delete(rolesRoutes.destroy({ role: selectedRole.value.id }).url, {
    preserveScroll: true,
    onSuccess: () => {
      deleteDialogOpen.value = false;
      selectedRole.value = null;
    },
  });
};

watch(createDialogOpen, (open: boolean) => {
  if (!open) {
    resetCreateState();
  }
});

watch(editDialogOpen, (open: boolean) => {
  if (!open) {
    resetEditState();
  }
});
</script>

<template>
  <Head :title="$t('roles.title')" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
      <!-- Header -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">
            {{ $t('roles.title') }}
          </h1>
          <p class="text-muted-foreground">
            {{ $t('roles.description') }}
          </p>
        </div>
        <Button @click="createDialogOpen = true" size="lg">
          <Plus class="mr-2 h-5 w-5" />
          {{ $t('roles.add_role') }}
        </Button>
      </div>

      <!-- Stats -->
      <div class="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle class="text-sm font-medium">
              {{ $t('roles.total_roles') }}
            </CardTitle>
            <Shield class="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">{{ roles.length }}</div>
          </CardContent>
        </Card>
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
      </div>

      <!-- Roles Table -->
      <Card>
        <CardHeader>
          <CardTitle>{{ $t('roles.role_list') }}</CardTitle>
          <CardDescription>
            {{ $t('roles.description') }}
          </CardDescription>
        </CardHeader>
        <CardContent>
          <div v-if="roles.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
            <Shield class="mb-4 h-12 w-12 text-muted-foreground" />
            <h3 class="text-lg font-semibold">{{ $t('roles.no_roles') }}</h3>
            <p class="text-sm text-muted-foreground">{{ $t('roles.no_roles_description') }}</p>
          </div>

          <div v-else class="relative overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="border-b text-xs uppercase text-muted-foreground">
                <tr>
                  <th class="px-6 py-3">{{ $t('roles.name') }}</th>
                  <th class="px-6 py-3">{{ $t('roles.permissions') }}</th>
                  <th class="px-6 py-3 text-right">{{ $t('roles.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="role in roles"
                  :key="role.id"
                  class="border-b transition-colors hover:bg-muted/50"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <Shield class="h-4 w-4 text-muted-foreground" />
                      <span class="font-medium">{{ role.name }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                      <Badge
                        v-for="permission in role.permissions"
                        :key="permission.id"
                        variant="secondary"
                        class="text-xs"
                      >
                        {{ permission.name }}
                      </Badge>
                      <Badge v-if="role.permissions.length === 0" variant="outline">
                        {{ $t('roles.no_permissions') }}
                      </Badge>
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
                        <DropdownMenuLabel>{{ $t('roles.actions') }}</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem @click="handleEdit(role)">
                          <Edit class="mr-2 h-4 w-4" />
                          {{ $t('roles.edit') }}
                        </DropdownMenuItem>
                        <DropdownMenuItem
                          @click="handleDelete(role)"
                          class="text-red-600"
                        >
                          <Trash2 class="mr-2 h-4 w-4" />
                          {{ $t('roles.delete') }}
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

    <!-- Create Role Dialog -->
    <Dialog v-model:open="createDialogOpen">
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>{{ $t('roles.create_role') }}</DialogTitle>
          <DialogDescription>
            {{ $t('roles.description') }}
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleCreate" class="space-y-4">
          <div class="space-y-2">
            <Label for="create-name">{{ $t('roles.name') }}</Label>
            <Input
              id="create-name"
              v-model="createForm.name"
              :placeholder="$t('roles.name_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label>{{ $t('roles.permissions') }}</Label>
            <div class="grid grid-cols-2 gap-3 rounded-md border p-4 max-h-64 overflow-y-auto">
              <div
                v-for="permission in permissions"
                :key="permission.id"
                class="flex items-center space-x-2"
              >
                <input
                  type="checkbox"
                  :id="`create-perm-${permission.id}`"
                  :value="permission.id"
                  v-model="createSelectedPermissionIds"
                  class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                />
                <label
                  :for="`create-perm-${permission.id}`"
                  class="text-sm font-normal cursor-pointer"
                >
                  {{ permission.name }}
                </label>
              </div>
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="createDialogOpen = false">
              {{ $t('roles.cancel') }}
            </Button>
            <Button type="submit" :disabled="createForm.processing">
              {{ createForm.processing ? $t('roles.creating') : $t('roles.create') }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Edit Role Dialog -->
    <Dialog v-model:open="editDialogOpen">
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>{{ $t('roles.edit_role') }}</DialogTitle>
          <DialogDescription>
            {{ $t('roles.description') }}
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleUpdate" class="space-y-4">
          <div class="space-y-2">
            <Label for="edit-name">{{ $t('roles.name') }}</Label>
            <Input
              id="edit-name"
              v-model="editForm.name"
              :placeholder="$t('roles.name_placeholder')"
              required
            />
          </div>

          <div class="space-y-2">
            <Label>{{ $t('roles.permissions') }}</Label>
            <div class="grid grid-cols-2 gap-3 rounded-md border p-4 max-h-64 overflow-y-auto">
              <div
                v-for="permission in permissions"
                :key="permission.id"
                class="flex items-center space-x-2"
              >
                <input
                  type="checkbox"
                  :id="`edit-perm-${permission.id}`"
                  :value="permission.id"
                  v-model="editSelectedPermissionIds"
                  class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                />
                <label
                  :for="`edit-perm-${permission.id}`"
                  class="text-sm font-normal cursor-pointer"
                >
                  {{ permission.name }}
                </label>
              </div>
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="editDialogOpen = false">
              {{ $t('roles.cancel') }}
            </Button>
            <Button type="submit" :disabled="editForm.processing">
              {{ editForm.processing ? $t('roles.updating') : $t('roles.update') }}
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
              <DialogTitle>{{ $t('roles.delete_role') }}</DialogTitle>
              <DialogDescription>
                {{ $t('roles.delete_warning') }}
              </DialogDescription>
            </div>
          </div>
        </DialogHeader>

        <div class="rounded-lg border border-destructive/20 bg-destructive/5 p-4">
          <p class="text-sm">
            {{ $t('roles.delete_confirm_message', { name: selectedRole?.name || '' }) }}
          </p>
        </div>

        <DialogFooter class="flex-row justify-end gap-2 sm:gap-2">
          <Button type="button" variant="outline" @click="deleteDialogOpen = false">
            {{ $t('roles.cancel') }}
          </Button>
          <Button type="button" variant="destructive" @click="confirmDelete">
            {{ $t('roles.delete_confirm') }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
