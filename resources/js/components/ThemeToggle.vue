<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { Sun, Moon, Monitor } from 'lucide-vue-next';
import { computed } from 'vue';

const { theme, setTheme, isDark } = useTheme();

const icons = {
    light: Sun,
    dark: Moon,
    system: Monitor,
};

const CurrentIcon = computed(() => icons[theme.value]);

const nextTheme = () => {
    if (theme.value === 'light') return 'dark';
    if (theme.value === 'dark') return 'system';
    return 'light';
};

const cycleTheme = () => {
    setTheme(nextTheme());
};
</script>

<template>
    <button
        @click="cycleTheme"
        class="group relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-background transition-all duration-200 hover:bg-accent hover:scale-110 active:scale-95"
        :title="`Current theme: ${theme}`"
    >
        <Transition name="theme-icon" mode="out-in">
            <component
                :is="CurrentIcon"
                :key="theme"
                class="h-5 w-5 text-foreground transition-transform duration-200 group-hover:rotate-12"
            />
        </Transition>

        <!-- Ripple effect on click -->
        <span
            class="absolute inset-0 rounded-lg bg-primary/20 opacity-0 transition-opacity group-active:opacity-100"
        ></span>
    </button>
</template>

<style scoped>
.theme-icon-enter-active,
.theme-icon-leave-active {
    transition: all 0.2s ease;
}

.theme-icon-enter-from {
    opacity: 0;
    transform: rotate(-180deg) scale(0.5);
}

.theme-icon-leave-to {
    opacity: 0;
    transform: rotate(180deg) scale(0.5);
}
</style>
