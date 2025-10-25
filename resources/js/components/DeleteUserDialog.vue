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
import { useForm } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';
import type { User } from '@/types';
import * as usersRoutes from '@/routes/users';

const props = defineProps<{
    open: boolean;
    user: User;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const form = useForm({});

const submit = () => {
    form.delete(usersRoutes.destroy(props.user.id).url, {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
        },
    });
};
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-destructive/10">
                        <AlertTriangle class="h-5 w-5 text-destructive" />
                    </div>
                    <div>
                        <DialogTitle>{{ $t('users.delete_user') }}</DialogTitle>
                        <DialogDescription>
                            {{ $t('users.delete_user_warning') }}
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <div class="rounded-lg border border-destructive/20 bg-destructive/5 p-4">
                <p class="text-sm">
                    {{ $t('users.delete_user_confirm', { name: user.name }) }}
                </p>
                <p class="mt-2 text-sm font-semibold">
                    {{ user.email }}
                </p>
            </div>

            <DialogFooter class="flex-row justify-end gap-2 sm:gap-2">
                <Button
                    type="button"
                    variant="outline"
                    @click="emit('update:open', false)"
                    :disabled="form.processing"
                >
                    {{ $t('users.cancel') }}
                </Button>
                <Button
                    type="button"
                    variant="destructive"
                    @click="submit"
                    :disabled="form.processing"
                >
                    {{ form.processing ? $t('users.deleting') : $t('users.delete_confirm') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
