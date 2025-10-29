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
import { useForm } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import * as usersRoutes from '@/routes/users';

const selectedRoleIds = ref<number[]>([]);

interface Role {
    id: number;
    name: string;
}

const props = defineProps<{
    open: boolean;
    roles?: Role[];
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    locale: 'en',
    roles: [] as number[],
});

const submit = () => {
    // Update form roles before submission
    form.roles = [...selectedRoleIds.value];
    
    form.post(usersRoutes.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
            form.reset();
            selectedRoleIds.value = [];
        },
    });
};

// Reset form when dialog closes
watch(
    () => props.open,
    (isOpen: boolean) => {
        if (!isOpen) {
            form.reset();
            form.clearErrors();
            selectedRoleIds.value = [];
        }
    }
);
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[500px]">
            <DialogHeader>
                <DialogTitle>{{ $t('users.create_user') }}</DialogTitle>
                <DialogDescription>
                    {{ $t('users.create_user_description') }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.name') }}
                    </label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        :placeholder="$t('users.name_placeholder')"
                        required
                        autofocus
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.email') }}
                    </label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        :placeholder="$t('users.email_placeholder')"
                        required
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.password') }}
                    </label>
                    <Input
                        id="password"
                        v-model="form.password"
                        type="password"
                        :placeholder="$t('users.password_placeholder')"
                        required
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.password_confirmation') }}
                    </label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        :placeholder="$t('users.password_confirmation_placeholder')"
                        required
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="space-y-2">
                    <label for="locale" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.language') }}
                    </label>
                    <select
                        id="locale"
                        v-model="form.locale"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="en">English</option>
                        <option value="lt">Lietuvių</option>
                    </select>
                    <InputError :message="form.errors.locale" />
                </div>

                <div v-if="roles && roles.length > 0" class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ $t('users.roles') }}
                    </label>
                    <div class="grid grid-cols-2 gap-3 rounded-md border p-4 max-h-48 overflow-y-auto">
                        <label
                            v-for="role in roles"
                            :key="role.id"
                            :for="`create-role-${role.id}`"
                            class="flex items-center space-x-2 cursor-pointer hover:bg-accent/50 rounded p-2 -m-2"
                        >
                            <input
                                type="checkbox"
                                :id="`create-role-${role.id}`"
                                :value="role.id"
                                v-model="selectedRoleIds"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary focus:ring-offset-2 cursor-pointer"
                            />
                            <span class="text-sm select-none">
                                {{ role.name }}
                            </span>
                        </label>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ $t('users.roles_description') }}
                    </p>
                    <InputError :message="form.errors.roles" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('update:open', false)"
                        :disabled="form.processing"
                    >
                        {{ $t('users.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? $t('users.creating') : $t('users.create') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
