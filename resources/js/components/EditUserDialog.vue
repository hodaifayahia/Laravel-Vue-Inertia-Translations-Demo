<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { useForm, router } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import type { User } from '@/types';
import * as usersRoutes from '@/routes/users';

interface Role {
    id: number;
    name: string;
}

interface UserData extends User {
    locale: string;
    roles?: Role[];
}

const props = defineProps<{
    open: boolean;
    user: UserData;
    roles?: Role[];
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    locale: props.user.locale || 'en',
});

const selectedRoleIds = ref<number[]>(props.user.roles?.map((r) => r.id) || []);

const submit = () => {
    form.put(usersRoutes.update(props.user.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            // Update roles separately
            if (props.roles) {
                router.post(
                    `/users/${props.user.id}/assign-roles`,
                    { roles: selectedRoleIds.value },
                    { preserveScroll: true }
                );
            }
            
            emit('update:open', false);
            form.reset('password', 'password_confirmation');
        },
    });
};

// Reset password fields when dialog closes
watch(
    () => props.open,
    (isOpen: boolean) => {
        if (!isOpen) {
            form.reset('password', 'password_confirmation');
            form.clearErrors();
        } else {
            // Update form with latest user data when dialog opens
            form.name = props.user.name;
            form.email = props.user.email;
            form.locale = props.user.locale || 'en';
            selectedRoleIds.value = props.user.roles?.map((r) => r.id) || [];
        }
    }
);
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[600px] max-h-[90vh] flex flex-col">
            <DialogHeader class="flex-shrink-0">
                <DialogTitle>{{ $t('users.edit_user') }}</DialogTitle>
                <DialogDescription>
                    {{ $t('users.edit_user_description') }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="flex flex-col flex-1 overflow-hidden">
                <div class="space-y-4 overflow-y-auto pr-2 flex-1">
                <div class="space-y-2">
                    <label for="edit-name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.name') }}
                    </label>
                    <Input
                        id="edit-name"
                        v-model="form.name"
                        type="text"
                        :placeholder="$t('users.name_placeholder')"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <label for="edit-email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.email') }}
                    </label>
                    <Input
                        id="edit-email"
                        v-model="form.email"
                        type="email"
                        :placeholder="$t('users.email_placeholder')"
                        required
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <label for="edit-password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.password') }}
                        <span class="text-muted-foreground text-xs ml-1">
                            ({{ $t('users.leave_blank_to_keep') }})
                        </span>
                    </label>
                    <Input
                        id="edit-password"
                        v-model="form.password"
                        type="password"
                        :placeholder="$t('users.password_placeholder')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <label for="edit-password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.password_confirmation') }}
                    </label>
                    <Input
                        id="edit-password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        :placeholder="$t('users.password_confirmation_placeholder')"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="space-y-2">
                    <label for="edit-locale" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.language') }}
                    </label>
                    <select
                        id="edit-locale"
                        v-model="form.locale"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="en">English</option>
                        <option value="fr">Français</option>
                        <option value="ar">العربية</option>
                        <option value="lt">Lietuvių</option>
                    </select>
                    <InputError :message="form.errors.locale" />
                </div>

                    <!-- Roles Section -->
                    <div v-if="roles && roles.length > 0" class="space-y-2">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            {{ $t('users.roles') }}
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 rounded-md border p-3 max-h-40 overflow-y-auto">
                            <label
                                v-for="role in roles"
                                :key="role.id"
                                :for="`edit-role-${role.id}`"
                                class="flex items-center space-x-2 cursor-pointer hover:bg-accent/50 rounded p-2"
                            >
                                <input
                                    type="checkbox"
                                    :id="`edit-role-${role.id}`"
                                    :value="role.id"
                                    v-model="selectedRoleIds"
                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary focus:ring-offset-2 cursor-pointer"
                                />
                                <span class="text-sm select-none">
                                    {{ role.name }}
                                </span>
                            </label>
                        </div>
                        <div v-if="selectedRoleIds.length > 0" class="flex flex-wrap gap-1 mt-2">
                            <Badge
                                v-for="roleId in selectedRoleIds"
                                :key="roleId"
                                variant="secondary"
                                class="text-xs"
                            >
                                {{ roles.find((r: Role) => r.id === roleId)?.name }}
                            </Badge>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ $t('users.roles_description') }}
                        </p>
                    </div>
                </div>

                <DialogFooter class="flex-shrink-0 mt-4 pt-4 border-t">
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('update:open', false)"
                        :disabled="form.processing"
                    >
                        {{ $t('users.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? $t('users.updating') : $t('users.update') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
