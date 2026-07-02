<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    summary: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Reports" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <p
                    class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                >
                    Reports
                </p>

                <h1
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                >
                    Reports & Export
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    View business reports and export data for analysis.
                </p>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="text-sm text-slate-500">Purchase Orders</div>
                    <div class="mt-2 text-3xl font-bold text-slate-950">
                        {{ summary.purchase_orders }}
                    </div>
                </div>

                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="text-sm text-slate-500">Pending Approval</div>
                    <div class="mt-2 text-3xl font-bold text-amber-600">
                        {{ summary.pending_purchase_orders }}
                    </div>
                </div>

                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="text-sm text-slate-500">Approved</div>
                    <div class="mt-2 text-3xl font-bold text-indigo-600">
                        {{ summary.approved_purchase_orders }}
                    </div>
                </div>

                <div
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="text-sm text-slate-500">Completed</div>
                    <div class="mt-2 text-3xl font-bold text-emerald-600">
                        {{ summary.completed_purchase_orders }}
                    </div>
                </div>
            </section>

            <section class="grid gap-4 lg:grid-cols-3">
                <Link
                    href="/reports/purchase-orders"
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-sm font-black text-indigo-600"
                    >
                        PO
                    </div>

                    <h2 class="mt-5 text-lg font-bold text-slate-950">
                        Purchase Order Report
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Filter purchase orders by date, supplier, and status.
                        Export results as CSV.
                    </p>

                    <div class="mt-5 text-sm font-semibold text-indigo-600">
                        Open Report →
                    </div>
                </Link>

                <Link
                    href="/reports/stock-balances"
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-sm font-black text-emerald-600"
                    >
                        ST
                    </div>

                    <h2 class="mt-5 text-lg font-bold text-slate-950">
                        Stock Balance Report
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        View warehouse stock balance, low stock, out of stock
                        items, and export results as CSV.
                    </p>

                    <div class="mt-5 text-sm font-semibold text-emerald-600">
                        Open Report →
                    </div>
                </Link>

                <Link
                    href="/reports/stock-movements"
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-sm font-black text-amber-600">
                        MV
                    </div>

                    <h2 class="mt-5 text-lg font-bold text-slate-950">
                        Stock Movement Report
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        View stock movement history from receiving and stock adjustments. Export results as CSV.
                    </p>

                    <div class="mt-5 text-sm font-semibold text-amber-600">
                        Open Report →
                    </div>
                </Link>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
