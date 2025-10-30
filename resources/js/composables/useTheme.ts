import { ref, watch, onMounted } from 'vue';

export type Theme = 'light' | 'dark' | 'system';

const theme = ref<Theme>('system');
const isDark = ref(false);

export function useTheme() {
    const setTheme = (newTheme: Theme) => {
        theme.value = newTheme;
        localStorage.setItem('theme', newTheme);
        applyTheme();
    };

    const applyTheme = () => {
        const root = document.documentElement;
        
        if (theme.value === 'system') {
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            isDark.value = systemPrefersDark;
            root.classList.toggle('dark', systemPrefersDark);
        } else {
            isDark.value = theme.value === 'dark';
            root.classList.toggle('dark', theme.value === 'dark');
        }
    };

    const toggleTheme = () => {
        if (theme.value === 'light') {
            setTheme('dark');
        } else if (theme.value === 'dark') {
            setTheme('system');
        } else {
            setTheme('light');
        }
    };

    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme') as Theme;
        if (savedTheme && ['light', 'dark', 'system'].includes(savedTheme)) {
            theme.value = savedTheme;
        }
        applyTheme();

        // Listen for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', () => {
            if (theme.value === 'system') {
                applyTheme();
            }
        });
    };

    onMounted(() => {
        initTheme();
    });

    watch(theme, () => {
        applyTheme();
    });

    return {
        theme,
        isDark,
        setTheme,
        toggleTheme,
        initTheme,
    };
}
