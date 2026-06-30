<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    purchaseOrder: {
        type: Object,
        required: true,
    },
    warehouses: {
        type: Array,
        default: () => [],
    }
});

const now = new Date();
now.setMinutes(now.getMinutes() - now().getTimezoneOffset());
const currentDateTime = now.toISOString().slice(0, 16);

const getRemainingQuantity = (item) => {
    return Number(item.quantity || 0) - Number(item.received_quantity || 0);
};

const form = useForm({
    warehouse_id: '',
    received_at: currentDateTime,
    note: '',
    items: props.purchaseOrder.items.map((item) => ({
        purchase_order_item_id: item.id,
        quantity: getRemainingQuantity(item),
    }));
});

const reveivableItems = computed(() => {
    return props.purchaseOrder.items.filter((item) => getRemainingQuantity(item) > 0)
});

const submit = () => {
    form.post(`/purchase-orders/${props.purchaseOrder.id}/receive`);
};

const formatQuantity = (value) => {
    return Number(value || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>

<template>
    <Head title="Receive Stock" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-6xl space-y-6">
            <section class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                        Warehouse
                    </p>

                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">
                        Receive Stock
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Receive stock from purchase order {{ purchaseOrder.po_number }}.
                    </p>
                </div>

                <Link
                    :href="`/purchase-orders/${purchaseOrder.id}`"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Back to PO
                </Link>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Purchase Order
                    </h2>

                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-slate-500">PO Number</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ purchaseOrder.po_number }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Supplier</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-950">
                                {{ purchaseOrder.supplier?.name || '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6">
                    <div class="text-sm font-semibold text-amber-900">
                        Stock Rule
                    </div>

                    <p class="mt-2 text-sm text-amber-800">
                        Receiving stock will update warehouse stock and create stock movement records automatically.
                    </p>
                </div>
            </section>

            <form
                class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                @submit.prevent="submit"
            >
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Warehouse
                        </label>

                        <select
                            v-model="form.warehouse_id"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Select warehouse</option>

                            <option
                                v-for="warehouse in warehouses"
                                :key="warehouse.id"
                                :value="warehouse.id"
                            >
                                {{ warehouse.code }} - {{ warehouse.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.warehouse_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.warehouse_id }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Received At
                        </label>

                        <input
                            v-model="form.received_at"
                            type="datetime-local"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p
                            v-if="form.errors.received_at"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.received_at }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Note
                        </label>

                        <textarea
                            v-model="form.note"
                            rows="3"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional receiving note"
                        ></textarea>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-slate-950">
                        Receive Items
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Enter received quantity. Quantity cannot exceed remaining quantity.
                    </p>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Ordered
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Received
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Remaining
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Receive Now
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="(item, index) in purchaseOrder.items"
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

                                <td class="px-6 py-4 text-right text-sm text-slate-600">
                                    {{ formatQuantity(item.quantity) }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm text-slate-600">
                                    {{ formatQuantity(item.received_quantity) }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-semibold text-slate-950">
                                    {{ formatQuantity(getRemainingQuantity(item)) }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <input
                                        v-model="form.items[index].quantity"
                                        type="number"
                                        min="0"
                                        :max="getRemainingQuantity(item)"
                                        step="0.01"
                                        class="w-36 rounded-xl border-slate-300 text-right text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        :disabled="getRemainingQuantity(item) <= 0"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p
                    v-if="form.errors.items"
                    class="text-sm text-red-600"
                >
                    {{ form.errors.items }}
                </p>

                <div class="flex items-center justify-end gap-3">
                    <Link
                        :href="`/purchase-orders/${purchaseOrder.id}`"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing || receivableItems.length === 0"
                    >
                        Receive Stock
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>