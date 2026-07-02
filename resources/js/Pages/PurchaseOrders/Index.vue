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
    purchaseOrders: {
        type: Object,
        required: true,
    },
    suppliers: {
        type: Array,
        default: () => [],
    },
    statuses: {
        type: Object,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => [
            {
                search: "",
                status: "",
                supplier_id: "",
            },
        ],
    },
});

const search = ref(props.filters.search ?? "");
const status = ref(props.filters.status ?? "");
const supplierId = ref(props.filters.supplier_id ?? "");

const submitFilter = () => {
    router.get(
        "/purchase-orders",
        {
            search: search.value,
            status: status.value,
            supplier_id: supplierId.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilter = () => {
    search.value = "";
    status.value = "";
    supplierId.value = "";
    router.get("purchase-orders");
};

const deletePurchaseOrder = (purchaseOrder) => {
    if (!confirm(`Delete purchase order "${purchaseOrder.po_number}"?`)) {
        return;
    }

    router.delete(`/purchase-orders/${purchaseOrder.id}`);
};

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
</script>

<template>
    <Head title="Purchase Orders" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Purchasing
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Purchase Orders
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Manage draft purchase orders before approval workflow.
                    </p>
                </div>

                <Link
                    v-if="can('purchase-order.create')"
                    href="/purchase-orders/create"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Create PO
                </Link>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 lg:grid-cols-[1fr_220px_220px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search PO number or supplier..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <select
                        v-model="supplierId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Suppliers</option>

                        <option
                            v-for="supplier in suppliers"
                            :key="supplier.id"
                            :value="supplier.id"
                        >
                            {{ supplier.code ? `${supplier.code} - ` : ""
                            }}{{ supplier.name }}
                        </option>
                    </select>

                    <select
                        v-model="supplierId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Status</option>

                        <option
                            v-for="(label, value) in statuses"
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
                    <table class="min-w-full divide-y devide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    PO Number
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Supplier
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Order Date
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Items
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Total Amount
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="purchaseOrder in purchaseOrders.data"
                                :key="purchaseOrder.id"
                                class="hover:bg-slate-50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900"
                                >
                                    <Link
                                        :href="`/purchase-orders/${purchaseOrder.id}`"
                                        class="hover:text-indigo-600"
                                        >{{ purchaseOrder.po_number }}</Link
                                    >
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{
                                            purchaseOrder.supplier?.name || "-"
                                        }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        {{
                                            purchaseOrder.supplier?.code || "-"
                                        }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatDate(purchaseOrder.order_date) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            statusClass(purchaseOrder.status)
                                        "
                                    >
                                        {{
                                            statuses[purchaseOrder.status] ||
                                            purchaseOrder.status
                                        }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{ purchaseOrder.items_count }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-950"
                                >
                                    {{
                                        formatMoney(purchaseOrder.total_amount)
                                    }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm"
                                >
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/purchase-orders/${purchaseOrder.id}`"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                        >
                                            View
                                        </Link>

                                        <Link
                                            v-if="
                                                purchaseOrder.status ===
                                                    'DRAFT' &&
                                                can('purchase-order.edit')
                                            "
                                            :href="`/purchase-orders/${purchaseOrder.id}/edit`"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            v-if="
                                                purchaseOrder.status ===
                                                    'DRAFT' &&
                                                can('purchase-order.submit')
                                            "
                                            type="button"
                                            class="rounded-lg border border-indigo-200 px-3 py-1.5 font-medium text-indigo-700 transition hover:bg-indigo-50"
                                            @click="
                                                submitPurchaseOrder(
                                                    purchaseOrder,
                                                )
                                            "
                                        >
                                            Submit
                                        </button>

                                        <button
                                            v-if="
                                                purchaseOrder.status ===
                                                    'PENDING_APPROVAL' &&
                                                can('purchase-order.approve')
                                            "
                                            type="button"
                                            class="rounded-lg border border-emerald-200 px-3 py-1.5 font-medium text-emerald-700 transition hover:bg-emerald-50"
                                            @click="
                                                approvePurchaseOrder(
                                                    purchaseOrder,
                                                )
                                            "
                                        >
                                            Approve
                                        </button>

                                        <button
                                            v-if="
                                                purchaseOrder.status ===
                                                    'PENDING_APPROVAL' &&
                                                can('purchase-order.reject')
                                            "
                                            type="button"
                                            class="rounded-lg border border-red-200 px-3 py-1.5 font-medium text-red-700 transition hover:bg-red-50"
                                            @click="
                                                rejectPurchaseOrder(
                                                    purchaseOrder,
                                                )
                                            "
                                        >
                                            Reject
                                        </button>

                                        <button
                                            v-if="
                                                purchaseOrder.status ===
                                                    'DRAFT' &&
                                                can('purchase-order.delete')
                                            "
                                            type="button"
                                            class="rounded-lg border border-red-200 px-3 py-1.5 font-medium text-red-700 transition hover:bg-red-50"
                                            @click="
                                                deletePurchaseOrder(
                                                    purchaseOrder,
                                                )
                                            "
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="purchaseOrders.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No purchase orders found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="purchaseOrders.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{
                            purchaseOrders.from
                        }}</span>
                        to
                        <span class="font-medium">{{ purchaseOrders.to }}</span>
                        of
                        <span class="font-medium">{{
                            purchaseOrders.total
                        }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in purchaseOrders.links"
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
