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
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputError from '@/components/InputError.vue';
import type { User } from '@/types';
import * as usersRoutes from '@/routes/users';

interface UserData extends User {
    locale: string;
}

const props = defineProps<{
    open: boolean;
    user: UserData;
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

const submit = () => {
    form.put(usersRoutes.update(props.user.id).url, {
        preserveScroll: true,
        onSuccess: () => {
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
        }
    }
);
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[500px]">
            <DialogHeader>
                <DialogTitle>{{ $t('users.edit_user') }}</DialogTitle>
                <DialogDescription>
                    {{ $t('users.edit_user_description') }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                    <Label for="edit-name">{{ $t('users.name') }}</Label>
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
                    <Label for="edit-email">{{ $t('users.email') }}</Label>
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
                    <Label for="edit-password">
                        {{ $t('users.password') }}
                        <span class="text-muted-foreground text-xs ml-1">
                            ({{ $t('users.leave_blank_to_keep') }})
                        </span>
                    </Label>
                    <Input
                        id="edit-password"
                        v-model="form.password"
                        type="password"
                        :placeholder="$t('users.password_placeholder')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="space-y-2">
                    <Label for="edit-password_confirmation">{{
                        $t('users.password_confirmation')
                    }}</Label>
                    <Input
                        id="edit-password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        :placeholder="$t('users.password_confirmation_placeholder')"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="space-y-2">
                    <Label for="edit-locale">{{ $t('users.language') }}</Label>
                    <select
                        id="edit-locale"
                        v-model="form.locale"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="en">English</option>
                        <option value="lt">Lietuvių</option>
                    </select>
                    <InputError :message="form.errors.locale" />
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
                        {{ form.processing ? $t('users.updating') : $t('users.update') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
