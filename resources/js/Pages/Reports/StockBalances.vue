<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    warehouseStocks: {
        type: Object,
        required: true,
    },
    warehouses: {
        type: Array,
        default: () => [],
    },
    stockStatuses: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            warehouse_id: "",
            stock_status: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");
const warehouseId = ref(props.filters.warehouse_id ?? "");
const stockStatus = ref(props.filters.stock_status ?? "");

const queryParams = computed(() => {
    const params = new URLSearchParams();

    if (search.value) params.set("search", search.value);
    if (warehouseId.value) params.set("warehouse_id", warehouseId.value);
    if (stockStatus.value) params.set("stock_status", stockStatus.value);

    return params.toString();
});

const exportUrl = computed(() => {
    const query = queryParams.value;

    return query
        ? `/reports/stock-balances/export?${query}`
        : "/reports/stock-balances/export";
});

const submitFilter = () => {
    router.get(
        "/reports/stock-balances",
        {
            search: search.value,
            warehouse_id: warehouseId.value,
            stock_status: stockStatus.value,
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
    stockStatus.value = "";

    router.get("/reports/stock-balances");
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

const formatQuantity = (value) => {
    return Number(value ?? 0).toLocaleString("th-TH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const resolveStockStatus = (warehouseStock) => {
    const quantity = Number(warehouseStock.quantity ?? 0);
    const minimumStock = Number(warehouseStock.product?.minimum_stock ?? 0);

    if (quantity <= 0) {
        return "Out of Stock";
    }

    if (minimumStock > 0 && quantity <= minimumStock) {
        return "Low Stock";
    }

    return "Available";
};

const stockStatusClass = (warehouseStock) => {
    const status = resolveStockStatus(warehouseStock);

    if (status === "Out of Stock") {
        return "bg-red-50 text-red-700 ring-1 ring-red-200";
    }

    if (status === "Low Stock") {
        return "bg-amber-50 text-amber-700 ring-1 ring-amber-200";
    }

    return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
};
</script>

<template>
    <Head title="Stock Balance Report" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-emerald-600"
                    >
                        Reports
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Stock Balance Report
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Review warehouse stock balance and export inventory
                        data.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        href="/reports"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Back
                    </Link>

                    <a
                        :href="exportUrl"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500"
                    >
                        Export CSV
                    </a>
                </div>
            </section>

            <section
                class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5"
            >
                <div class="text-sm font-semibold text-emerald-900">
                    Stock Rule
                </div>

                <p class="mt-1 text-sm text-emerald-800">
                    Every stock quantity change must have a stock movement
                    record.
                </p>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 xl:grid-cols-[1fr_240px_200px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search product name or SKU..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    />

                    <select
                        v-model="warehouseId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
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
                        v-model="stockStatus"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        <option value="">All Status</option>

                        <option
                            v-for="(label, value) in stockStatuses"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500"
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
                                    Warehouse
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Product
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Category
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Quantity
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Minimum
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Last Updated
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="warehouseStock in warehouseStocks.data"
                                :key="warehouseStock.id"
                                class="hover:bg-slate-50"
                            >
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{
                                            warehouseStock.warehouse?.name ||
                                            "-"
                                        }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{
                                            warehouseStock.warehouse?.code ||
                                            "-"
                                        }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{
                                            warehouseStock.product?.name || "-"
                                        }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        SKU:
                                        {{ warehouseStock.product?.sku || "-" }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{
                                        warehouseStock.product?.category
                                            ?.name || "-"
                                    }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold text-slate-950"
                                >
                                    {{
                                        formatQuantity(warehouseStock.quantity)
                                    }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{
                                        formatQuantity(
                                            warehouseStock.product
                                                ?.minimum_stock,
                                        )
                                    }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            stockStatusClass(warehouseStock)
                                        "
                                    >
                                        {{ resolveStockStatus(warehouseStock) }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{
                                        formatDateTime(
                                            warehouseStock.updated_at,
                                        )
                                    }}
                                </td>
                            </tr>

                            <tr v-if="warehouseStocks.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No stock balance found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="warehouseStocks.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{
                            warehouseStocks.from
                        }}</span>
                        to
                        <span class="font-medium">{{
                            warehouseStocks.to
                        }}</span>
                        of
                        <span class="font-medium">{{
                            warehouseStocks.total
                        }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in warehouseStocks.links"
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
