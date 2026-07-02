<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();

const permissions = computed(() => page.props.auth?.permissions ?? []);

const can = (permission) => {
    return permissions.value.includes(permission);
};

const props = defineProps({
    warehouseStocks: {
        type: Object,
        required: true,
    },
    warehouses: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            warehouse_id: "",
            stock_status: "",
        }),
    },
    summary: {
        type: Object,
        required: true,
    },
});

const search = ref(props.filters.search ?? "");
const warehouseId = ref(props.filters.warehouse_id ?? "");
const stockStatus = ref(props.filters.stock_status ?? "");

const submitFilter = () => {
    router.get(
        "/warehouse-stocks",
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
    router.get("/warehouse-stocks");
};

const syncStockBalances = () => {
    if (!confirm("Initialize missing stock balance records with quantity 0?")) {
        return;
    }

    router.post("/warehouse-stocks/sync");
};

const formatQuantity = (value) => {
    return Number(value ?? 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
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

const getStockStatus = (stock) => {
    const quantity = Number(stock.quantity ?? 0);
    const minimumStock = Number(stock.product?.minimum_stock ?? 0);

    if (quantity <= 0) {
        return {
            label: "Out of Stock",
            className: "bg-red-50 text-red-700 ring-1 ring-red-200",
        };
    }

    if (minimumStock > 0 && quantity <= minimumStock) {
        return {
            label: "Low Stock",
            className: "bg-amber-50 text-amber-700 ring-1 ring-amber-200",
        };
    }

    return {
        label: "Available",
        className: "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200",
    };
};

const summaryCards = [
    {
        label: "Stock Records",
        value: props.summary.total_records,
        description: "Product and warehouse balances",
    },
    {
        label: "Total Quantity",
        value: formatQuantity(props.summary.total_quantity),
        description: "Total current quantity",
    },
    {
        label: "Low Stock",
        value: props.summary.low_stock_count,
        description: "Below or equal minimum stock",
    },
    {
        label: "Out of Stock",
        value: props.summary.out_of_stock_count,
        description: "Quantity is zero or below",
    },
];
</script>

<template>
    <Head title="Stock Balance" />

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
                        Stock Balance
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        View current stock quantity by warehouse and product.
                        This page is read-only to protect stock movement
                        history.
                    </p>
                </div>

                <button
                    v-if="can('warehouse-stock.sync')"
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    @click="syncStockBalances"
                >
                    Sync Stock Balances
                </button>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in summaryCards"
                    :key="card.label"
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="text-sm font-medium text-slate-500">
                        {{ card.label }}
                    </div>

                    <div class="mt-3 text-3xl font-bold text-slate-950">
                        {{ card.value }}
                    </div>

                    <div class="mt-2 text-sm text-slate-500">
                        {{ card.description }}
                    </div>
                </div>
            </section>

            <section
                class="rounded-2xl border border-amber-200 bg-amber-50 p-5"
            >
                <div class="text-sm font-semibold text-amber-900">
                    Important Stock Rule
                </div>

                <p class="mt-1 text-sm text-amber-800">
                    Do not update stock quantity directly from this page. Future
                    stock changes must be created through receiving or
                    adjustment workflows, and every change must generate a stock
                    movement record.
                </p>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 lg:grid-cols-[1fr_240px_200px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search product SKU, product name, warehouse code, or warehouse name..."
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
                        v-model="stockStatus"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
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
                                    Current Quantity
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Minimum Stock
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
                                v-for="stock in warehouseStocks.data"
                                :key="stock.id"
                                class="hover:bg-slate-50"
                            >
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ stock.warehouse?.code }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        {{ stock.warehouse?.name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ stock.product?.sku }}
                                    </div>

                                    <div
                                        class="mt-1 max-w-md truncate text-sm text-slate-500"
                                    >
                                        {{ stock.product?.name }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ stock.product?.category?.name || "-" }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-950"
                                >
                                    {{ formatQuantity(stock.quantity) }}
                                    <span class="font-normal text-slate-500">
                                        {{ stock.product?.unit }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{
                                        formatQuantity(
                                            stock.product?.minimum_stock,
                                        )
                                    }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="getStockStatus(stock).className"
                                    >
                                        {{ getStockStatus(stock).label }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatDateTime(stock.updated_at) }}
                                </td>
                            </tr>

                            <tr v-if="warehouseStocks.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No stock balance records found. Click “Sync
                                    Stock Balances” after creating products and
                                    warehouses.
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
