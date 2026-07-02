<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    stockAdjustments: {
        type: Object,
        required: true,
    },
    warehouses: {
        type: Array,
        default: () => [],
    },
    adjustmentTypes: {
        type: Array,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            warehouse_id: "",
            adjustment_type: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");
const warehouseId = ref(props.filters.warehouse_id ?? "");
const adjustmentType = ref(props.filters.adjustment_type ?? "");

const submitFilter = () => {
    router.get(
        "/stock-adjustments",
        {
            search: search.value,
            warehouse_id: warehouseId.value,
            adjustment_type: adjustmentType.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilter = () => {
    search.value = "";
    warehouseId.value = "";
    adjustmentType.value = "";
    router.get("/stock-adjustments");
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

const adjustmentTypeClass = (type) => {
    if (type === "ADJUST_IN") {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
    }

    if (type === "ADJUST_OUT") {
        return "bg-red-50 text-red-700 ring-1 ring-red-200";
    }

    return "bg-slate-100 text-slate-700 ring-1 ring-slate-200";
};
</script>
<template>
    <Head title="Stock Adjustments" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Inventory
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Stock Adjustments
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Adjust stock quantity for stock count differences,
                        damaged goods, or initial balance corrections.
                    </p>
                </div>

                <Link
                    href="/stock-adjustments/create"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Create Adjustment
                </Link>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 lg:grid-cols-[1fr_240px_220px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search adjustment number, warehouse, or reason..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <select
                        v-model="warehouseId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Warehouses</option>

                        <option
                            v-for="warehouse in warehouses"
                            :key="warehouse.id"
                            :value="warehouse.id"
                        >
                            {{ warehouse.code }} - {{ warehouse.name }}
                        </option>
                    </select>

                    <select
                        v-model="adjustmentType"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Types</option>

                        <option
                            v-for="(label, value) in adjustmentTypes"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
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
                                    Adjustment Number
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Warehouse
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Reason
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Items
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Adjusted At
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
                                v-for="adjustment in stockAdjustments.data"
                                :key="adjustment.id"
                                class="hover:bg-slate-50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-950"
                                >
                                    <Link
                                        :href="`/stock-adjustments/${adjustment.id}`"
                                        class="hover:text-indigo-600"
                                    >
                                        {{ adjustment.adjustment_number }}
                                    </Link>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            adjustmentTypeClass(
                                                adjustment.adjustment_type,
                                            )
                                        "
                                    >
                                        {{
                                            adjustmentTypes[
                                                adjustment.adjustment_type
                                            ] || adjustment.adjustment_type
                                        }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ adjustment.warehouse?.code }} -
                                    {{ adjustment.warehouse?.name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <div class="max-w-md truncate">
                                        {{ adjustment.reason }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{ adjustment.items_count }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatDateTime(adjustment.adjusted_at) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm"
                                >
                                    <Link
                                        :href="`/stock-adjustments/${adjustment.id}`"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="stockAdjustments.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No stock adjustments found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="stockAdjustments.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{
                            stockAdjustments.from
                        }}</span>
                        to
                        <span class="font-medium">{{
                            stockAdjustments.to
                        }}</span>
                        of
                        <span class="font-medium">{{
                            stockAdjustments.total
                        }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in stockAdjustments.links"
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
