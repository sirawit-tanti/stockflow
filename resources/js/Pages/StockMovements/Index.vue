<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    stockMovements: {
        type: Object,
        required: true,
    },
    warehouses: {
        type: Array,
        default: () => [],
    },
    movementTypes: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            warehouse_id: '',
            movement_type: '',
        }),
    },
});

const search = ref(props.filters.search ?? '');
const warehouseId = ref(props.filters.warehouse_id ?? '');
const movementType = ref(props.filters.movement_type ?? '');

const submitFilter = () => {
    router.get(
        '/stock-movements',
        {
            search: search.value,
            warehouse_id: warehouseId.value,
            movement_type: movementType.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilter = () => {
    search.value = '';
    warehouseId.value = '';
    movementType.value = '';
    router.get('/stock-movements');
};

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
        minute: '2-digit',
    });
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

const quantityChangedClass = (value) => {
    return Number(value || 0) >= 0 ? 'text-emerald-700' : 'text-red-700';
};
</script>

<template>
    <Head title="Stock Movements" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                    Inventory
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">
                    Stock Movements
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    View inventory movement history. Every stock quantity change must have a movement record.
                </p>
            </section>

            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="text-sm font-semibold text-amber-900">
                    Important Stock Rule
                </div>

                <p class="mt-1 text-sm text-amber-800">
                    Stock movement is the audit trail of inventory quantity changes.
                    Warehouse stock balance should never be updated without creating a movement record.
                </p>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <form
                    class="grid gap-3 lg:grid-cols-[1fr_240px_220px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search product, warehouse, or note..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                    <select
                        v-model="warehouseId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Warehouses
                        </option>

                        <option
                            v-for="warehouse in warehouses"
                            :key="warehouse.id"
                            :value="warehouse.id"
                        >
                            {{ warehouse.code }} - {{ warehouse.name }}
                        </option>
                    </select>

                    <select
                        v-model="movementType"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Movement Types
                        </option>

                        <option
                            v-for="(label, value) in movementTypes"
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

            <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Warehouse
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Before
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Change
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    After
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="movement in stockMovements.data"
                                :key="movement.id"
                                class="hover:bg-slate-50"
                            >
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ formatDateTime(movement.movement_date) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="movementTypeClass(movement.movement_type)"
                                    >
                                        {{ movementTypes[movement.movement_type] || movement.movement_type }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-950">
                                        {{ movement.warehouse?.code }}
                                    </div>

                                    <div class="mt-1 text-sm text-slate-500">
                                        {{ movement.warehouse?.name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-950">
                                        {{ movement.product?.sku }}
                                    </div>

                                    <div class="mt-1 max-w-md truncate text-sm text-slate-500">
                                        {{ movement.product?.name }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600">
                                    {{ formatQuantity(movement.quantity_before) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold"
                                    :class="quantityChangedClass(movement.quantity_changed)"
                                >
                                    {{ Number(movement.quantity_changed) >= 0 ? '+' : '' }}{{ formatQuantity(movement.quantity_changed) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-slate-950">
                                    {{ formatQuantity(movement.quantity_after) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <Link
                                        :href="`/stock-movements/${movement.id}`"
                                        class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="stockMovements.data.length === 0">
                                <td
                                    colspan="8"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No stock movements found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="stockMovements.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{ stockMovements.from }}</span>
                        to
                        <span class="font-medium">{{ stockMovements.to }}</span>
                        of
                        <span class="font-medium">{{ stockMovements.total }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in stockMovements.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="rounded-lg px-3 py-1.5 text-sm"
                            :class="[
                                link.active
                                    ? 'bg-slate-950 font-semibold text-white'
                                    : 'border border-slate-200 text-slate-700 hover:bg-slate-50',
                                !link.url ? 'pointer-events-none opacity-50' : '',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>