<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    warehouses: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    adjustmentTypes: {
        type: Object,
        default: () => ({}),
    },
});

const now = new Date();
now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
const currentDateTime = now.toISOString().slice(0, 16);

const form = useForm({
    warehouse_id: "",
    adjustment_type: "ADJUST_IN",
    adjusted_at: currentDateTime,
    reason: "",
    note: "",
    items: [
        {
            product_id: "",
            quantity: 1,
        },
    ],
});

const addItem = () => {
    form.items.push({
        product_id: "",
        quantity: 1,
    });
};

const removeItem = (index) => {
    if (form.items.length === 1) {
        return;
    }

    form.items.splice(index, 1);
};

const findProduct = (productId) => {
    return props.products.find(
        (product) => Number(product.id) === Number(productId),
    );
};

const totalQuantity = computed(() => {
    return form.items.reduce((total, item) => {
        return total + Number(item.quantity || 0);
    }, 0);
});

const submit = () => {
    form.post("/stock-adjustments");
};

const formatQuantity = (value) => {
    return Number(value || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
</script>

<template>
    <Head title="Create Stock Adjustment" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-6xl space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Inventory
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Create Stock Adjustment
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Adjust warehouse stock quantity and create stock
                        movement automatically.
                    </p>
                </div>

                <Link
                    href="/stock-adjustments"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Back
                </Link>
            </section>

            <section
                class="rounded-2xl border border-amber-200 bg-amber-50 p-5"
            >
                <div class="text-sm font-semibold text-amber-900">
                    Important
                </div>

                <p class="mt-1 text-sm text-amber-800">
                    Adjustment out cannot make stock quantity below zero. Every
                    adjustment will create stock movement records.
                </p>
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
                            Adjustment Type
                        </label>

                        <select
                            v-model="form.adjustment_type"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option
                                v-for="(label, value) in adjustmentTypes"
                                :key="value"
                                :value="value"
                            >
                                {{ label }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.adjustment_type"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.adjustment_type }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Adjusted At
                        </label>

                        <input
                            v-model="form.adjusted_at"
                            type="datetime-local"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                        <p
                            v-if="form.errors.adjusted_at"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.adjusted_at }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Reason
                        </label>

                        <textarea
                            v-model="form.reason"
                            rows="3"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: Stock count difference, damaged goods, initial stock adjustment"
                        ></textarea>

                        <p
                            v-if="form.errors.reason"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.reason }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Note
                        </label>

                        <textarea
                            v-model="form.note"
                            rows="2"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional note"
                        ></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">
                            Adjustment Items
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Select products and quantity to adjust.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        @click="addItem"
                    >
                        Add Item
                    </button>
                </div>

                <p v-if="form.errors.items" class="text-sm text-red-600">
                    {{ form.errors.items }}
                </p>

                <div class="space-y-4">
                    <div
                        v-for="(item, index) in form.items"
                        :key="index"
                        class="rounded-2xl border border-slate-200 p-4"
                    >
                        <div class="grid gap-4 lg:grid-cols-[1fr_180px_auto]">
                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Product
                                </label>

                                <select
                                    v-model="item.product_id"
                                    class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Select product</option>

                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                    >
                                        {{ product.sku }} - {{ product.name }}
                                    </option>
                                </select>

                                <div
                                    v-if="findProduct(item.product_id)"
                                    class="mt-1 text-xs text-slate-500"
                                >
                                    Unit:
                                    {{ findProduct(item.product_id).unit }}
                                    <span
                                        v-if="
                                            findProduct(item.product_id)
                                                .category
                                        "
                                    >
                                        · Category:
                                        {{
                                            findProduct(item.product_id)
                                                .category.name
                                        }}
                                    </span>
                                </div>

                                <p
                                    v-if="
                                        form.errors[`items.${index}.product_id`]
                                    "
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{
                                        form.errors[`items.${index}.product_id`]
                                    }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    Quantity
                                </label>

                                <input
                                    v-model="item.quantity"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    class="mt-1 w-full rounded-xl border-slate-300 text-right text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="
                                        form.errors[`items.${index}.quantity`]
                                    "
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors[`items.${index}.quantity`] }}
                                </p>
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="button"
                                    class="rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="form.items.length === 1"
                                    @click="removeItem(index)"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <div
                        class="w-full max-w-sm rounded-2xl p-5 text-white"
                        :class="
                            form.adjustment_type === 'ADJUST_OUT'
                                ? 'bg-red-700'
                                : 'bg-emerald-700'
                        "
                    >
                        <div class="text-sm opacity-80">Total Quantity</div>

                        <div class="mt-2 text-3xl font-bold">
                            {{
                                form.adjustment_type === "ADJUST_OUT"
                                    ? "-"
                                    : "+"
                            }}{{ formatQuantity(totalQuantity) }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link
                        href="/stock-adjustments"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
