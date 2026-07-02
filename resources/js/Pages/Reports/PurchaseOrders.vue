<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

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
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            status: "",
            supplier_id: "",
            date_from: "",
            date_to: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");
const status = ref(props.filters.status ?? "");
const supplierId = ref(props.filters.supplier_id ?? "");
const dateFrom = ref(props.filters.date_from ?? "");
const dateTo = ref(props.filters.date_to ?? "");

const queryParams = computed(() => {
    const params = new URLSearchParams();

    if (search.value) params.set("search", search.value);
    if (status.value) params.set("status", status.value);
    if (supplierId.value) params.set("supplier_id", supplierId.value);
    if (dateFrom.value) params.set("date_from", dateFrom.value);
    if (dateTo.value) params.set("date_to", dateTo.value);

    return params.toString();
});

const exportUrl = computed(() => {
    const query = queryParams.value;

    return query
        ? `/reports/purchase-orders/export?${query}`
        : "/reports/purchase-orders/export";
});

const submitFilter = () => {
    router.get(
        "/reports/purchase-orders",
        {
            search: search.value,
            status: status.value,
            supplier_id: supplierId.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
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
    dateFrom.value = "";
    dateTo.value = "";

    router.get("/reports/purchase-orders");
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

const formatMoney = (value) => {
    return Number(value ?? 0).toLocaleString("th-TH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const statusClass = (value) => {
    if (value === "APPROVED") {
        return "bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200";
    }

    if (value === "PENDING_APPROVAL") {
        return "bg-amber-50 text-amber-700 ring-1 ring-amber-200";
    }

    if (value === "COMPLETED") {
        return "bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200";
    }

    if (["REJECTED", "CANCELLED"].includes(value)) {
        return "bg-red-50 text-red-700 ring-1 ring-red-200";
    }

    return "bg-slate-100 text-slate-700 ring-1 ring-slate-200";
};
</script>

<template>
    <Head title="Purchase Order Report" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Reports
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Purchase Order Report
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Filter and export purchase order data.
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
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 xl:grid-cols-[1fr_180px_220px_160px_160px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search PO number or supplier..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <select
                        v-model="status"
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
                            {{ supplier.name }}
                        </option>
                    </select>

                    <input
                        v-model="dateFrom"
                        type="date"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <input
                        v-model="dateTo"
                        type="date"
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
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Order Date
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Total
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Created By
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
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-950"
                                >
                                    {{ purchaseOrder.po_number }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ purchaseOrder.supplier?.name || "-" }}
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
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ formatDate(purchaseOrder.order_date) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-950"
                                >
                                    {{
                                        formatMoney(purchaseOrder.total_amount)
                                    }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ purchaseOrder.creator?.name || "-" }}
                                </td>
                            </tr>

                            <tr v-if="purchaseOrders.data.length === 0">
                                <td
                                    colspan="6"
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
