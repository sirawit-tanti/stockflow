import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export function usePermissions() {
    const page = usePage();

    const roles = computed(() => page.props.auth?.roles ?? []);
    const permissions = computed(() => page.props.auth?.permissions ?? []);

    const hasRole = (role) => {
        return roles.value.includes(role);
    };

    const can = (permission) => {
        return permissions.value.includes(permission);
    };

    const canAny = (permissionList) => {
        return permissionList.some((permission) => can(permission));
    };

    const canAll = (permissionList) => {
        return permissionList.every((permission) => can(permission));
    };

    return {
        roles,
        permissions,
        hasRole,
        can,
        canAny,
        canAll,
    };
}
