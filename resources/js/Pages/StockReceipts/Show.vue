<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stockReceipt: {
        type: Object,
        required: true,
    }
});

const formatQuantity = (value) => {
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
        minute: '2-digit'
    });
}
</script>

<template>
    <Head :title="stockReceipt.receipt_number" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                        Stock Receipt Detail
                    </p>

                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">
                        {{ stockReceipt.receipt_number }}
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Stock receiving document from purchase order {{ stockReceipt.purchase_order?.po_number }}.
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link 
                        href="/stock-receipts"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Back
                    </Link>

                    <Link
                        :href="`/purchase-orders/${stockReceipt.purchase_order_id}`" 
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        View PO
                    </Link>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Receipt Information
                    </h2>

                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-slate-500">PO Number</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ stockReceipt.purchase_order?.po_number || '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Supplier</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ stockReceipt.purchase_order?.supplier?.name || '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Warehouse</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ stockReceipt.warehouse?.code }} - {{ stockReceipt.warehouse?.name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Received By</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ stockReceipt.receiver?.name || '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Received At</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ formatDateTime(stockReceipt.received_at) }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <dt class="text-sm text-slate-500">Note</dt>
                        <dd class="mt-1 rounded-xl bg-slate-50 p-4 text-sm text-slate-700">
                            {{ stockReceipt.note || '-' }}
                        </dd>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                    <div class="text-sm font-semibold text-emerald-900">
                        Stock Movement Created
                    </div>

                    <p class="mt-2 text-sm text-emerald-800">
                        This receipt has updated warehouse stock and created RECEIVE stock movement records.
                    </p>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Received Items
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Quantity
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="item in stockReceipt.items"
                                :key="item.id"
                            >
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-950">
                                        {{ item.product?.sku }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        {{ item.product?.name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ item.product?.category?.name || '-' }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-semibold text-slate-950">
                                    {{ formatQuantity(item.quantity) }}
                                    {{ item.product?.unit }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>