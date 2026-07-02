<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            role: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");
const role = ref(props.filters.role ?? "");

const submitFilter = () => {
    router.get(
        "/users",
        {
            search: search.value,
            role: role.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilter = () => {
    search.value = "";
    role.value = "";
    router.get("/users");
};

const formatDateTime = (value) => {
    if (!value) {
        return "-";
    }

    return new Date(value).toLocaleString("th-TH", {
        timeZone: "Asia/Bangkok",
        year: "numeric",
        month: "short",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const roleClass = (roleName) => {
    if (roleName === "ADMIN") {
        return "bg-slate-950 text-white";
    }

    if (roleName === "PURCHASING") {
        return "bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200";
    }

    if (roleName === "MANAGER") {
        return "bg-amber-50 text-amber-700 ring-1 ring-amber-200";
    }

    if (roleName === "WAREHOUSE") {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
    }

    return "bg-slate-100 text-slate-700 ring-1 ring-slate-200";
};
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Administration
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Users
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Manage system users and assign roles.
                    </p>
                </div>

                <Link
                    href="/users/create"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Create User
                </Link>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 lg:grid-cols-[1fr_240px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search name or email..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <select
                        v-model="role"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Roles</option>

                        <option
                            v-for="roleOption in roles"
                            :key="roleOption.id"
                            :value="roleOption.name"
                        >
                            {{ roleOption.name }}
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500"
                        >
                            Search
                        </button>

                        <button
                            type="button"
                            class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            @click="clearFilter"
                        >
                            Clear
                        </button>
                    </div>
                </form>
            </section>

            <section
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
            >
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Roles
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Created At
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="hover:bg-slate-50"
                            >
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ user.name }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        ID: {{ user.id }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ user.email }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="userRole in user.roles"
                                            :key="userRole.id"
                                            class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="roleClass(userRole.name)"
                                        >
                                            {{ userRole.name }}
                                        </span>

                                        <span
                                            v-if="user.roles.length === 0"
                                            class="text-sm text-slate-400"
                                        >
                                            No role
                                        </span>
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatDateTime(user.created_at) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm"
                                >
                                    <Link
                                        :href="`/users/${user.id}/edit`"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        Edit
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="users.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="users.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{ users.from }}</span>
                        to
                        <span class="font-medium">{{ users.to }}</span>
                        of
                        <span class="font-medium">{{ users.total }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="rounded-lg px-3 py-1.5 text-sm"
                            :class="[
                                link.active
                                    ? 'bg-slate-950 font-semibold text-white'
                                    : 'border border-slate-200 text-slate-700 hover:bg-slate-50',
                                !link.url
                                    ? 'pointer-events-none opacity-50'
                                    : '',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
