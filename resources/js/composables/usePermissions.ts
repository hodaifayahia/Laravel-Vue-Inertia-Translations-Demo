import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermissions() {
    const page = usePage();
    
    const user = computed(() => page.props.auth.user);
    const permissions = computed(() => user.value?.permissions || []);
    const roles = computed(() => user.value?.roles || []);

    /**
     * Check if the user has a specific permission
     */
    const hasPermission = (permission: string): boolean => {
        return permissions.value.includes(permission);
    };

    /**
     * Check if the user has any of the given permissions
     */
    const hasAnyPermission = (permissionList: string[]): boolean => {
        return permissionList.some(permission => permissions.value.includes(permission));
    };

    /**
     * Check if the user has all of the given permissions
     */
    const hasAllPermissions = (permissionList: string[]): boolean => {
        return permissionList.every(permission => permissions.value.includes(permission));
    };

    /**
     * Check if the user has a specific role
     */
    const hasRole = (role: string): boolean => {
        return roles.value.includes(role);
    };

    /**
     * Check if the user has any of the given roles
     */
    const hasAnyRole = (roleList: string[]): boolean => {
        return roleList.some(role => roles.value.includes(role));
    };

    /**
     * Check if the user has all of the given roles
     */
    const hasAllRoles = (roleList: string[]): boolean => {
        return roleList.every(role => roles.value.includes(role));
    };

    /**
     * Check if the user is a super admin
     */
    const isSuperAdmin = (): boolean => {
        return hasRole('super-admin');
    };

    return {
        user,
        permissions,
        roles,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
        hasRole,
        hasAnyRole,
        hasAllRoles,
        isSuperAdmin,
    };
}
