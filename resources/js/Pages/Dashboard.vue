<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    summary: {
        type: Object,
        required: true,
    },
    lowStockItems: {
        type: Array,
        default: () => [],
    },
    recentPurchaseOrders: {
        type: Array,
        default: () => [],
    },
    recentStockMovements: {
        type: Array,
        default: () => [],
    },
});

const formatNumber = (value) => {
    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    });
};

const formatMoney = (value) => {
    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDateTime = (value) => {
    if (!value) {
        return '-';
    }

    return new Date(value).toLocaleString('th-TH', {
        timeZone: 'Asia/Bangkok',
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const statusClass = (status) => {
    if (status === 'DRAFT') {
        return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
    }

    if (status === 'PENDING_APPROVAL') {
        return 'bg-amber-50 text-amber-700 ring-1 ring-amber-200';
    }

    if (status === 'APPROVED') {
        return 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200';
    }

    if (status === 'PARTIALLY_RECEIVED') {
        return 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200';
    }

    if (status === 'COMPLETED') {
        return 'bg-green-50 text-green-700 ring-1 ring-green-200';
    }

    if (status === 'REJECTED' || status === 'CANCELLED') {
        return 'bg-red-50 text-red-700 ring-1 ring-red-200';
    }

    return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
};

const movementTypeClass = (type) => {
    if (type === 'RECEIVE') {
        return 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200';
    }

    if (type === 'ADJUST_IN') {
        return 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200';
    }

    if (type === 'ADJUST_OUT') {
        return 'bg-red-50 text-red-700 ring-1 ring-red-200';
    }

    return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
};

const formatStatus = (status) => {
    return String(status || '-')
        .replaceAll('_', ' ')
        .toLowerCase()
        .replace(/\b\w/g, (char) => char.toUpperCase());
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                    StockFlow
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">
                    Dashboard
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Overview of inventory, purchase orders, stock receipts, and movement history.
                </p>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-medium text-slate-500">
                        Total Products
                    </div>

                    <div class="mt-3 text-3xl font-bold text-slate-950">
                        {{ formatNumber(summary.total_products) }}
                    </div>

                    <Link
                        href="/products"
                        class="mt-4 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                    >
                        View products
                    </Link>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-medium text-slate-500">
                        Pending PO
                    </div>

                    <div class="mt-3 text-3xl font-bold text-amber-600">
                        {{ formatNumber(summary.pending_purchase_orders) }}
                    </div>

                    <Link
                        href="/purchase-orders?status=PENDING_APPROVAL"
                        class="mt-4 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                    >
                        Review purchase orders
                    </Link>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-medium text-slate-500">
                        Low Stock Items
                    </div>

                    <div class="mt-3 text-3xl font-bold text-red-600">
                        {{ formatNumber(summary.low_stock_items) }}
                    </div>

                    <Link
                        href="/warehouse-stocks?stock_status=low_stock"
                        class="mt-4 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                    >
                        View low stock
                    </Link>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="text-sm font-medium text-slate-500">
                        Stock Receipts Today
                    </div>

                    <div class="mt-3 text-3xl font-bold text-emerald-600">
                        {{ formatNumber(summary.stock_receipts_today) }}
                    </div>

                    <Link
                        href="/stock-receipts"
                        class="mt-4 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                    >
                        View receipts
                    </Link>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <div class="text-sm font-medium text-emerald-700">
                        Approved PO
                    </div>

                    <div class="mt-3 text-2xl font-bold text-emerald-900">
                        {{ formatNumber(summary.approved_purchase_orders) }}
                    </div>
                </div>

                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5">
                    <div class="text-sm font-medium text-indigo-700">
                        Partially Received PO
                    </div>

                    <div class="mt-3 text-2xl font-bold text-indigo-900">
                        {{ formatNumber(summary.partially_received_purchase_orders) }}
                    </div>
                </div>

                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                    <div class="text-sm font-medium text-red-700">
                        Out of Stock
                    </div>

                    <div class="mt-3 text-2xl font-bold text-red-900">
                        {{ formatNumber(summary.out_of_stock_items) }}
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <div class="text-sm font-medium text-slate-600">
                        Total Stock Quantity
                    </div>

                    <div class="mt-3 text-2xl font-bold text-slate-950">
                        {{ formatNumber(summary.total_stock_quantity) }}
                    </div>
                </div>
            </section>

            <section
                v-if="lowStockItems.length > 0"
                class="rounded-2xl border border-red-200 bg-red-50 p-6"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-red-950">
                            Low Stock Alert
                        </h2>

                        <p class="mt-1 text-sm text-red-700">
                            These items are below or equal to their minimum stock level.
                        </p>
                    </div>

                    <Link
                        href="/warehouse-stocks?stock_status=low_stock"
                        class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                    >
                        View All
                    </Link>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-red-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">
                                        Product
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-red-700">
                                        Warehouse
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-red-700">
                                        Current Stock
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-red-700">
                                        Minimum Stock
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200">
                                <tr
                                    v-for="stock in lowStockItems"
                                    :key="stock.id"
                                >
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-slate-950">
                                            {{ stock.product?.sku }}
                                        </div>

                                        <div class="mt-1 text-sm text-slate-500">
                                            {{ stock.product?.name }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ stock.warehouse?.code }} - {{ stock.warehouse?.name }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm font-semibold text-red-700">
                                        {{ formatNumber(stock.quantity) }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm text-slate-600">
                                        {{ formatNumber(stock.product?.minimum_stock) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-2">
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">
                                Recent Purchase Orders
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Latest purchase documents.
                            </p>
                        </div>

                        <Link
                            href="/purchase-orders"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                        >
                            View all
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-200">
                        <div
                            v-for="purchaseOrder in recentPurchaseOrders"
                            :key="purchaseOrder.id"
                            class="flex items-center justify-between gap-4 px-6 py-4"
                        >
                            <div>
                                <Link
                                    :href="`/purchase-orders/${purchaseOrder.id}`"
                                    class="text-sm font-semibold text-slate-950 hover:text-indigo-600"
                                >
                                    {{ purchaseOrder.po_number }}
                                </Link>

                                <div class="mt-1 text-sm text-slate-500">
                                    {{ purchaseOrder.supplier?.name || '-' }}
                                </div>
                            </div>

                            <div class="text-right">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="statusClass(purchaseOrder.status)"
                                >
                                    {{ formatStatus(purchaseOrder.status) }}
                                </span>

                                <div class="mt-2 text-sm font-semibold text-slate-950">
                                    {{ formatMoney(purchaseOrder.total_amount) }}
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="recentPurchaseOrders.length === 0"
                            class="px-6 py-10 text-center text-sm text-slate-500"
                        >
                            No purchase orders found.
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">
                                Recent Stock Movements
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Latest stock quantity changes.
                            </p>
                        </div>

                        <Link
                            href="/stock-movements"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-500"
                        >
                            View all
                        </Link>
                    </div>

                    <div class="divide-y divide-slate-200">
                        <div
                            v-for="movement in recentStockMovements"
                            :key="movement.id"
                            class="flex items-center justify-between gap-4 px-6 py-4"
                        >
                            <div>
                                <Link
                                    :href="`/stock-movements/${movement.id}`"
                                    class="text-sm font-semibold text-slate-950 hover:text-indigo-600"
                                >
                                    {{ movement.product?.sku }} - {{ movement.product?.name }}
                                </Link>

                                <div class="mt-1 text-sm text-slate-500">
                                    {{ movement.warehouse?.code }} · {{ formatDateTime(movement.movement_date) }}
                                </div>
                            </div>

                            <div class="text-right">
                                <span
                                    class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="movementTypeClass(movement.movement_type)"
                                >
                                    {{ formatStatus(movement.movement_type) }}
                                </span>

                                <div class="mt-2 text-sm font-semibold text-emerald-700">
                                    +{{ formatNumber(movement.quantity_changed) }}
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="recentStockMovements.length === 0"
                            class="px-6 py-10 text-center text-sm text-slate-500"
                        >
                            No stock movements found.
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>