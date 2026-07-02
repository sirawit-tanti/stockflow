<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    stockMovement: {
        type: Object,
        required: true,
    },
    movementTypes: {
        type: Object,
        default: () => ({}),
    },
    referenceReceipt: {
        type: Object,
        default: null,
    },
    referenceAdjustment: {
        type: Object,
        default: null,
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

const movementTypeClass = (type) => {
    if (type === "RECEIVE") {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
    }

    if (type === "ADJUST_IN") {
        return "bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200";
    }

    if (type === "ADJUST_OUT") {
        return "bg-red-50 text-red-700 ring-1 ring-red-200";
    }

    return "bg-slate-100 text-slate-700 ring-1 ring-slate-200";
};
</script>

<template>
    <Head title="Stock Movement Detail" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Stock Movement Detail
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-950"
                        >
                            Movement #{{ stockMovement.id }}
                        </h1>

                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="
                                movementTypeClass(stockMovement.movement_type)
                            "
                        >
                            {{
                                movementTypes[stockMovement.movement_type] ||
                                stockMovement.movement_type
                            }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-slate-500">
                        View stock quantity before, changed quantity, and
                        quantity after movement.
                    </p>
                </div>

                <Link
                    href="/stock-movements"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Back
                </Link>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2"
                >
                    <h2 class="text-lg font-semibold text-slate-950">
                        Movement Information
                    </h2>

                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-slate-500">
                                Movement Date
                            </dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{
                                    formatDateTime(stockMovement.movement_date)
                                }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Created By</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockMovement.creator?.name || "-" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Warehouse</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockMovement.warehouse?.code }} -
                                {{ stockMovement.warehouse?.name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Product</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockMovement.product?.sku }} -
                                {{ stockMovement.product?.name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">
                                Product Category
                            </dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{
                                    stockMovement.product?.category?.name || "-"
                                }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">
                                Reference Type
                            </dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ stockMovement.reference_type || "-" }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <dt class="text-sm text-slate-500">Note</dt>
                        <dd
                            class="mt-1 rounded-xl bg-slate-50 p-4 text-sm text-slate-700"
                        >
                            {{ stockMovement.note || "-" }}
                        </dd>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm">
                    <div class="text-sm text-slate-300">Quantity Flow</div>

                    <div class="mt-6 space-y-4">
                        <div>
                            <div
                                class="text-xs uppercase tracking-wide text-slate-400"
                            >
                                Before
                            </div>
                            <div class="mt-1 text-2xl font-bold">
                                {{
                                    formatQuantity(
                                        stockMovement.quantity_before,
                                    )
                                }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-xs uppercase tracking-wide text-slate-400"
                            >
                                Changed
                            </div>
                            <div
                                class="mt-1 text-2xl font-bold text-emerald-300"
                            >
                                {{
                                    Number(stockMovement.quantity_changed) >= 0
                                        ? "+"
                                        : ""
                                }}{{
                                    formatQuantity(
                                        stockMovement.quantity_changed,
                                    )
                                }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="text-xs uppercase tracking-wide text-slate-400"
                            >
                                After
                            </div>
                            <div class="mt-1 text-2xl font-bold">
                                {{
                                    formatQuantity(stockMovement.quantity_after)
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section
                v-if="referenceReceipt"
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">
                            Reference Stock Receipt
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            This movement was created from a stock receipt
                            document.
                        </p>
                    </div>

                    <Link
                        :href="`/stock-receipts/${referenceReceipt.id}`"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        View Receipt
                    </Link>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-sm text-slate-500">Receipt Number</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ referenceReceipt.receipt_number }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">PO Number</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{
                                referenceReceipt.purchase_order?.po_number ||
                                "-"
                            }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Supplier</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{
                                referenceReceipt.purchase_order?.supplier
                                    ?.name || "-"
                            }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Received At</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ formatDateTime(referenceReceipt.received_at) }}
                        </dd>
                    </div>
                </dl>
            </section>

            <section
                v-if="referenceAdjustment"
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">
                            Reference Stock Adjustment
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            This movement was created from a stock adjustment
                            document.
                        </p>
                    </div>

                    <Link
                        :href="`/stock-adjustments/${referenceAdjustment.id}`"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        View Adjustment
                    </Link>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-sm text-slate-500">
                            Adjustment Number
                        </dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ referenceAdjustment.adjustment_number }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Type</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ referenceAdjustment.adjustment_type }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Warehouse</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ referenceAdjustment.warehouse?.code }} -
                            {{ referenceAdjustment.warehouse?.name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Adjusted At</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{
                                formatDateTime(referenceAdjustment.adjusted_at)
                            }}
                        </dd>
                    </div>
                </dl>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
