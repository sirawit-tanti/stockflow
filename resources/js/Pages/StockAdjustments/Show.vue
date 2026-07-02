<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    stockAdjustment: {
        type: Object,
        required: true,
    },
    stockMovements: {
        type: Array,
        default: () => [],
    },
    adjustmentTypes: {
        type: Object,
        default: () => ({}),
    },
});

const formatQuantity = (value) => {
    return Number(value || 0).toLocaleString("en-US", {
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
    <Head :title="stockAdjustment.adjustment_number" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Stock Adjustment Detail
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-950"
                        >
                            {{ stockAdjustment.adjustment_number }}
                        </h1>

                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="
                                adjustmentTypeClass(
                                    stockAdjustment.adjustment_type,
                                )
                            "
                        >
                            {{
                                adjustmentTypes[
                                    stockAdjustment.adjustment_type
                                ] || stockAdjustment.adjustment_type
                            }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-slate-500">
                        View stock adjustment information and related stock
                        movements.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        href="/stock-adjustments"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Back
                    </Link>

                    <Link
                        href="/stock-adjustments/create"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Create New
                    </Link>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2"
                >
                    <h2 class="text-lg font-semibold text-slate-950">
                        Adjustment Information
                    </h2>

                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-slate-500">Warehouse</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockAdjustment.warehouse?.code }} -
                                {{ stockAdjustment.warehouse?.name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Adjusted By</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockAdjustment.adjuster?.name || "-" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Adjusted At</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{
                                    formatDateTime(stockAdjustment.adjusted_at)
                                }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Items</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockAdjustment.items.length }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <dt class="text-sm text-slate-500">Reason</dt>
                        <dd
                            class="mt-1 rounded-xl bg-slate-50 p-4 text-sm text-slate-700"
                        >
                            {{ stockAdjustment.reason }}
                        </dd>
                    </div>

                    <div class="mt-5">
                        <dt class="text-sm text-slate-500">Note</dt>
                        <dd
                            class="mt-1 rounded-xl bg-slate-50 p-4 text-sm text-slate-700"
                        >
                            {{ stockAdjustment.note || "-" }}
                        </dd>
                    </div>
                </div>

                <div
                    class="rounded-2xl p-6 text-white shadow-sm"
                    :class="
                        stockAdjustment.adjustment_type === 'ADJUST_OUT'
                            ? 'bg-red-700'
                            : 'bg-emerald-700'
                    "
                >
                    <div class="text-sm opacity-80">Adjustment Type</div>

                    <div class="mt-3 text-3xl font-bold">
                        {{
                            adjustmentTypes[stockAdjustment.adjustment_type] ||
                            stockAdjustment.adjustment_type
                        }}
                    </div>

                    <p class="mt-4 text-sm opacity-90">
                        Stock movements were created automatically from this
                        adjustment.
                    </p>
                </div>
            </section>

            <section
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
            >
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Adjustment Items
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
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
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="item in stockAdjustment.items"
                                :key="item.id"
                            >
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ item.product?.sku }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        {{ item.product?.name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ item.product?.category?.name || "-" }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-sm font-semibold text-slate-950"
                                >
                                    {{
                                        stockAdjustment.adjustment_type ===
                                        "ADJUST_OUT"
                                            ? "-"
                                            : "+"
                                    }}{{ formatQuantity(item.quantity) }}
                                    {{ item.product?.unit }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
            >
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Related Stock Movements
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Product
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Before
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Change
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    After
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
                                v-for="movement in stockMovements"
                                :key="movement.id"
                            >
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ movement.product?.sku }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        {{ movement.product?.name }}
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{
                                        formatQuantity(movement.quantity_before)
                                    }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-sm font-semibold"
                                    :class="
                                        Number(movement.quantity_changed) < 0
                                            ? 'text-red-700'
                                            : 'text-emerald-700'
                                    "
                                >
                                    {{
                                        Number(movement.quantity_changed) >= 0
                                            ? "+"
                                            : ""
                                    }}{{
                                        formatQuantity(
                                            movement.quantity_changed,
                                        )
                                    }}
                                </td>

                                <td
                                    class="px-6 py-4 text-right text-sm font-semibold text-slate-950"
                                >
                                    {{
                                        formatQuantity(movement.quantity_after)
                                    }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm">
                                    <Link
                                        :href="`/stock-movements/${movement.id}`"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="stockMovements.length === 0">
                                <td
                                    colspan="5"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No related stock movements found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
