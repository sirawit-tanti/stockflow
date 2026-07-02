<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    auditLogs: {
        type: Object,
        required: true,
    },
    modules: {
        type: Array,
        default: () => [],
    },
    actions: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            module: "",
            action: "",
            user_id: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");
const module = ref(props.filters.module ?? "");
const action = ref(props.filters.action ?? "");
const userId = ref(props.filters.user_id ?? "");

const submitFilter = () => {
    router.get(
        "/audit-logs",
        {
            search: search.value,
            module: module.value,
            action: action.value,
            user_id: userId.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilter = () => {
    search.value = "";
    module.value = "";
    action.value = "";
    userId.value = "";
    router.get("/audit-logs");
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

const formatClassName = (value) => {
    if (!value) {
        return "-";
    }

    return String(value).split("\\").pop();
};

const actionClass = (value) => {
    if (["created", "submitted", "approved", "received"].includes(value)) {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
    }

    if (["updated", "role_updated"].includes(value)) {
        return "bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200";
    }

    if (["rejected", "cancelled", "deleted"].includes(value)) {
        return "bg-red-50 text-red-700 ring-1 ring-red-200";
    }

    return "bg-slate-100 text-slate-700 ring-1 ring-slate-200";
};
</script>

<template>
    <Head title="Audit Logs" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <p
                    class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                >
                    Administration
                </p>

                <h1
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                >
                    Audit Logs
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Track user activities and important system actions.
                </p>
            </section>

            <section
                class="rounded-2xl border border-amber-200 bg-amber-50 p-5"
            >
                <div class="text-sm font-semibold text-amber-900">
                    Audit Trail
                </div>

                <p class="mt-1 text-sm text-amber-800">
                    This page helps administrators review who performed
                    important actions such as approving purchase orders,
                    receiving stock, adjusting stock, and updating user roles.
                </p>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 xl:grid-cols-[1fr_180px_180px_240px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search description, user, module..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <select
                        v-model="module"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Modules</option>

                        <option
                            v-for="moduleOption in modules"
                            :key="moduleOption"
                            :value="moduleOption"
                        >
                            {{ moduleOption }}
                        </option>
                    </select>

                    <select
                        v-model="action"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Actions</option>

                        <option
                            v-for="actionOption in actions"
                            :key="actionOption"
                            :value="actionOption"
                        >
                            {{ actionOption }}
                        </option>
                    </select>

                    <select
                        v-model="userId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Users</option>

                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }} - {{ user.email }}
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
                                    Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Module
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Action
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Description
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Target
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
                                v-for="log in auditLogs.data"
                                :key="log.id"
                                class="hover:bg-slate-50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatDateTime(log.created_at) }}
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ log.user?.name || "System" }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ log.user?.email || "-" }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-950"
                                >
                                    {{ log.module }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="actionClass(log.action)"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <div class="max-w-md truncate">
                                        {{ log.description || "-" }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatClassName(log.auditable_type) }}
                                    <span v-if="log.auditable_id">
                                        #{{ log.auditable_id }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm"
                                >
                                    <Link
                                        :href="`/audit-logs/${log.id}`"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="auditLogs.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No audit logs found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="auditLogs.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{ auditLogs.from }}</span>
                        to
                        <span class="font-medium">{{ auditLogs.to }}</span>
                        of
                        <span class="font-medium">{{ auditLogs.total }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in auditLogs.links"
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
