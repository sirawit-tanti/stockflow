<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
            date_from: '',
            date_to: '',
        }),
    },
});

const search = ref(props.filters.search ?? '');
const warehouseId = ref(props.filters.warehouse_id ?? '');
const movementType = ref(props.filters.movement_type ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');

const queryParams = computed(() => {
    const params = new URLSearchParams();

    if (search.value) params.set('search', search.value);
    if (warehouseId.value) params.set('warehouse_id', warehouseId.value);
    if (movementType.value) params.set('movement_type', movementType.value);
    if (dateFrom.value) params.set('date_from', dateFrom.value);
    if (dateTo.value) params.set('date_to', dateTo.value);

    return params.toString();
});

const exportUrl = computed(() => {
    const query = queryParams.value;

    return query
        ? `/reports/stock-movements/export?${query}`
        : '/reports/stock-movements/export';
});

const submitFilter = () => {
    router.get(
        '/reports/stock-movements',
        {
            search: search.value,
            warehouse_id: warehouseId.value,
            movement_type: movementType.value,
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
    search.value = '';
    warehouseId.value = '';
    movementType.value = '';
    dateFrom.value = '';
    dateTo.value = '';

    router.get('/reports/stock-movements');
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

const formatQuantity = (value) => {
    return Number(value ?? 0).toLocaleString('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

const formatClassName = (value) => {
    if (!value) {
        return '-';
    }

    return String(value).split('\\').pop();
};

const movementTypeClass = (value) => {
    if (value === 'RECEIVE') {
        return 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200';
    }

    if (value === 'ADJUST_IN') {
        return 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200';
    }

    if (value === 'ADJUST_OUT') {
        return 'bg-red-50 text-red-700 ring-1 ring-red-200';
    }

    return 'bg-slate-100 text-slate-700 ring-1 ring-slate-200';
};

const changedQuantityClass = (value) => {
    const quantity = Number(value ?? 0);

    if (quantity > 0) {
        return 'text-emerald-700';
    }

    if (quantity < 0) {
        return 'text-red-700';
    }

    return 'text-slate-700';
};
</script>

<template>
    <Head title="Stock Movement Report" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-amber-600">
                        Reports
                    </p>

                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950">
                        Stock Movement Report
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Review stock movement history from receiving and stock adjustments.
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

            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="text-sm font-semibold text-amber-900">
                    Movement History
                </div>

                <p class="mt-1 text-sm text-amber-800">
                    This report helps verify every stock quantity change with before, changed, and after quantity.
                </p>
            </section>

            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <form
                    class="grid gap-3 xl:grid-cols-[1fr_220px_180px_160px_160px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search product, SKU, or note..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >

                    <select
                        v-model="warehouseId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
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
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="">
                            All Types
                        </option>

                        <option
                            v-for="(label, value) in movementTypes"
                            :key="value"
                            :value="value"
                        >
                            {{ label }}
                        </option>
                    </select>

                    <input
                        v-model="dateFrom"
                        type="date"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >

                    <input
                        v-model="dateTo"
                        type="date"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-500"
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
                                    Movement Date
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Warehouse
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Product
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Type
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Before
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Changed
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    After
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Reference
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Created By
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="stockMovement in stockMovements.data"
                                :key="stockMovement.id"
                                class="hover:bg-slate-50"
                            >
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ formatDateTime(stockMovement.movement_date) }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-950">
                                        {{ stockMovement.warehouse?.name || '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ stockMovement.warehouse?.code || '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-950">
                                        {{ stockMovement.product?.name || '-' }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        SKU: {{ stockMovement.product?.sku || '-' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="movementTypeClass(stockMovement.movement_type)"
                                    >
                                        {{ movementTypes[stockMovement.movement_type] || stockMovement.movement_type }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600">
                                    {{ formatQuantity(stockMovement.quantity_before) }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold"
                                    :class="changedQuantityClass(stockMovement.quantity_changed)"
                                >
                                    {{ formatQuantity(stockMovement.quantity_changed) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-bold text-slate-950">
                                    {{ formatQuantity(stockMovement.quantity_after) }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                                    {{ formatClassName(stockMovement.reference_type) }}
                                    <span v-if="stockMovement.reference_id">
                                        #{{ stockMovement.reference_id }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ stockMovement.creator?.name || '-' }}
                                </td>
                            </tr>

                            <tr v-if="stockMovements.data.length === 0">
                                <td
                                    colspan="9"
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