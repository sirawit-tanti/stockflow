<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stockReceipts: {
        type: Object,
        required: true,
    }
});

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
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Stock Receipts" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                    Warehouse
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">
                    Stock Receipts
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    View stock receiving documents created from approved purchase orders.
                </p>
            </section>

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Receipt Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    PO Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Warehouse
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Received By
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Received At
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="receipt in stockReceipts.data"
                                :key="receipt.id"
                                class="hover:bg-slate-50"
                            >
                                <td class="px-6 py-4 text-sm font-semibold text-slate-950">
                                    {{ receipt.receipt_number }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ receipt.purchase_order?.po_number || '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ receipt.warehouse?.code }} - {{ receipt.warehouse?.name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ receipt.receiver?.name || '-' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ formatDateTime(receipt.received_at) }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm">
                                    <Link
                                        href="`/stock-receipts/${receipt.id}`" 
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50">
                                        View
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="stockReceipts.data.length === 0">
                                <td class="px-6 py-10 text-center text-sm text-slate-500">
                                    No stock receipts found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>