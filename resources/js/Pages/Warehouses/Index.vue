<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    warehouses: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");

const submitSearch = () => {
    router.get(
        "/warehouses",
        {
            search: search.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearSearch = () => {
    search.value = "";
    router.get("/warehouses");
};

const deleteWarehouse = (warehouse) => {
    if (!confirm(`Delete warehouse "${warehouse.name}"?`)) {
        return;
    }

    router.delete(`warehouses/${warehouse.id}`);
};
</script>

<template>
    <Head title="Warehouses" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Master Data
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Warehouses
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Manage warehouse master data for inventory stock
                        locations.
                    </p>
                </div>

                <Link
                    href="/warehouses/create"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Add Warehouse
                </Link>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="flex flex-col gap-3 sm:flex-row"
                    @submit.prevent="submitSearch"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search warehouse code, name, or location..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

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
                            @click="clearSearch"
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
                                    Code
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Warehouse
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Location
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Status
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
                                v-for="warehouse in warehouses.data"
                                :key="warehouse.id"
                                class="hover:bg-slate-50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-950"
                                >
                                    {{ warehouse.code }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-950"
                                >
                                    {{ warehouse.name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ warehouse.location || "-" }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            warehouse.is_active
                                                ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
                                                : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200'
                                        "
                                    >
                                        {{
                                            warehouse.is_active
                                                ? "Active"
                                                : "Inactive"
                                        }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm"
                                >
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/warehouses/${warehouse.id}/edit`"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            class="rounded-lg border border-red-200 px-3 py-1.5 font-medium text-red-700 transition hover:bg-red-50"
                                            @click="deleteWarehouse(warehouse)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="warehouses.data.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No warehouses found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="warehouses.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{ warehouses.from }}</span>
                        to
                        <span class="font-medium">{{ warehouses.to }}</span>
                        of
                        <span class="font-medium">{{ warehouses.total }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in warehouses.links"
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
