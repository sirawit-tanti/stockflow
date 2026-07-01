<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    purchaseOrder: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Object,
        default: () => ({}),
    },
});

const formatMoney = (value) => {
    return Number(value || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatDate = (value) => {
    if (!value) {
        return "-";
    }

    return new Date(value).toLocaleDateString("th-TH", {
        timeZone: "Asia/Bangkok",
        year: "numeric",
        month: "short",
        day: "2-digit",
    });
};

const formatDateTime = (value) => {
    if (!value) {
        return "-";
    }

    return new Date(value).toLocaleDateString("th-TH", {
        timeZone: "Asia/Bangkok",
        year: "numeric",
        month: "short",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};
const statusClass = (value) => {
    if (value === "DRAFT") {
        return "bg-slate-100 text-slate-700 ring-1 ring-slate-200";
    }

    if (value === "PENDING_APPROVAL") {
        return "bg-amber-50 text-amber-700 ring-1 ring-amber-200";
    }

    if (value === "APPROVED") {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
    }

    if (value === "REJECTED") {
        return "bg-red-50 text-red-700 ring-1 ring-red-200";
    }

    return "bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200";
};

const submitPurchaseOrder = (purchaseOrder) => {
    if (
        !confirm(
            `Submit purchase order "${purchaseOrder.po_number}" for approval?`,
        )
    ) {
        return;
    }

    router.post(`/purchase-orders/${purchaseOrder.id}/submit`);
};

const approvePurchaseOrder = (purchaseOrder) => {
    if (!confirm(`Approve purchase order "${purchaseOrder.po_number}"?`)) {
        return;
    }

    router.post(`/purchase-orders/${purchaseOrder.id}/approve`);
};

const rejectPurchaseOrder = (purchaseOrder) => {
    if (!confirm(`Reject purchase order "${purchaseOrder.po_number}"?`)) {
        return;
    }

    router.post(`/purchase-orders/${purchaseOrder.id}/reject`);
};

const cancelPurchaseOrder = (purchaseOrder) => {
    if (!confirm(`Cancel purchase order "${purchaseOrder.po_number}"?`)) {
        return;
    }

    router.post(`/purchase-orders/${purchaseOrder.id}/cancel`);
};

const workflowSteps = (purchaseOrder) => {
    return [
        {
            label: "Draft",
            active: true,
            completed: true,
        },
        {
            label: "Pending Approval",
            active: purchaseOrder.status === "PENDING_APPROVAL",
            completed: [
                "PENDING_APPROVAL",
                "APPROVED",
                "REJECTED",
                "PARTIALLY_RECEIVED",
                "COMPLETED",
            ].includes(purchaseOrder.status),
        },
        {
            label: "Approved",
            active: purchaseOrder.status === "APPROVED",
            completed: ["APPROVED", "PARTIALLY_RECEIVED", "COMPLETED"].includes(
                purchaseOrder.status,
            ),
        },
        {
            label: "Receive Stock",
            active: ["PARTIALLY_RECEIVED", "COMPLETED"].includes(
                purchaseOrder.status,
            ),
            completed: ["PARTIALLY_RECEIVED", "COMPLETED"].includes(
                purchaseOrder.status,
            ),
        },
    ];
};
</script>

<template>
    <Head :title="purchaseOrder.po_number" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Purchase Order Detail
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-950"
                        >
                            {{ purchaseOrder.po_number }}
                        </h1>

                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="statusClass(purchaseOrder.status)"
                        >
                            {{
                                statuses[purchaseOrder.status] ||
                                purchaseOrder.status
                            }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-slate-500">
                        View purchase order supplier, dates, items, and amount.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        href="/purchase-orders"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Back
                    </Link>

                    <Link
                        v-if="['APPROVED', 'PARTIALLY_RECEIVED'].includes(purchaseOrder.status)"
                        :href="`/purchase-orders/${purchaseOrder.id}/receive`"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        Receive Stock
                    </Link>

                    <Link
                        v-if="purchaseOrder.status === 'DRAFT'"
                        :href="`/purchase-orders/${purchaseOrder.id}/edit`"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        Edit Draft
                    </Link>

                    <button
                        v-if="purchaseOrder.status === 'DRAFT'"
                        type="button"
                        class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-500"
                        @click="submitPurchaseOrder(purchaseOrder)"
                    >
                        Submit for Approval
                    </button>

                    <button
                        v-if="purchaseOrder.status === 'PENDING_APPROVAL'"
                        type="button"
                        class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500"
                        @click="approvePurchaseOrder(purchaseOrder)"
                    >
                        Approve
                    </button>

                    <button
                        v-if="purchaseOrder.status === 'PENDING_APPROVAL'"
                        type="button"
                        class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-500"
                        @click="rejectPurchaseOrder(purchaseOrder)"
                    >
                        Reject
                    </button>

                    <button
                        v-if="
                            ['DRAFT', 'PENDING_APPROVAL'].includes(
                                purchaseOrder.status,
                            )
                        "
                        type="button"
                        class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50"
                        @click="cancelPurchaseOrder(purchaseOrder)"
                    >
                        Cancel PO
                    </button>
                </div>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <h2 class="text-lg font-semibold text-slate-950">
                    Workflow Status
                </h2>

                <div class="mt-5 grid gap-3 md:grid-cols-4">
                    <div
                        v-for="step in workflowSteps(purchaseOrder)"
                        :key="step.label"
                        class="rounded-xl border p-4"
                        :class="
                            step.completed
                                ? 'border-emerald-200 bg-emerald-50'
                                : 'border-slate-200 bg-slate-50'
                        "
                    >
                        <div
                            class="text-sm font-semibold"
                            :class="
                                step.completed
                                    ? 'text-emerald-800'
                                    : 'text-slate-500'
                            "
                        >
                            {{ step.label }}
                        </div>

                        <div
                            class="mt-1 text-xs"
                            :class="
                                step.active
                                    ? 'text-slate-950'
                                    : 'text-slate-500'
                            "
                        >
                            {{
                                step.active
                                    ? "Current step"
                                    : step.completed
                                      ? "Completed"
                                      : "Waiting"
                            }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2"
                >
                    <h2 class="text-lg font-semibold text-slate-950">
                        Purchase Information
                    </h2>

                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-slate-500">Supplier</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ purchaseOrder.supplier?.name || "-" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">
                                Supplier Code
                            </dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ purchaseOrder.supplier?.code || "-" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Order Date</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ formatDate(purchaseOrder.order_date) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">
                                Expected Date
                            </dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ formatDate(purchaseOrder.expected_date) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Created By</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ purchaseOrder.creator?.name || "-" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Approved By</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ purchaseOrder.approver?.name || "-" }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <dt class="text-sm text-slate-500">Note</dt>
                        <dd
                            class="mt-1 rounded-xl bg-slate-50 p-4 text-sm text-slate-700"
                        >
                            {{ purchaseOrder.note || "-" }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Submitted At</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ formatDateTime(purchaseOrder.submitted_at) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Approved At</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ formatDateTime(purchaseOrder.approved_at) }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-slate-500">Rejected At</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">
                            {{ formatDateTime(purchaseOrder.rejected_at) }}
                        </dd>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm">
                    <div class="text-sm text-slate-300">Total Amount</div>

                    <div class="mt-3 text-4xl font-bold">
                        {{ formatMoney(purchaseOrder.total_amount) }}
                    </div>

                    <div class="mt-2 text-sm text-slate-400">
                        {{ purchaseOrder.items.length }} item(s)
                    </div>
                </div>
            </section>

            <section
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
            >
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Purchase Items
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
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Received
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Unit Price
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Total
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="item in purchaseOrder.items"
                                :key="item.id"
                                class="hover:bg-slate-50"
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

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ item.product?.category?.name || "-" }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{ formatMoney(item.quantity) }}
                                    {{ item.product?.unit }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{ formatMoney(item.received_quantity) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{ formatMoney(item.unit_price) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-950"
                                >
                                    {{ formatMoney(item.total_price) }}
                                </td>
                            </tr>
                        </tbody>

                        <tfoot class="bg-slate-50">
                            <tr>
                                <td
                                    colspan="5"
                                    class="px-6 py-4 text-right text-sm font-semibold text-slate-700"
                                >
                                    Total Amount
                                </td>
                                <td
                                    class="px-6 py-4 text-right text-sm font-bold text-slate-950"
                                >
                                    {{
                                        formatMoney(purchaseOrder.total_amount)
                                    }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
