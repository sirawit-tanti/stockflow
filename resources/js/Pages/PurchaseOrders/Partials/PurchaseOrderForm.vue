<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    suppliers: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
    submitLabel: {
        type: String,
        default: "Save",
    },
});

const emit = defineEmits(["submit"]);

const addItem = () => {
    props.form.items.push({
        product_id: "",
        quantity: 1,
        unit_price: 0,
    });
};

const removeItem = (index) => {
    if (props.form.items.length === 1) {
        return;
    }

    props.form.items.splice(index, 1);
};

const findProduct = (productId) => {
    return props.products.find(
        (product) => Number(product.id) === Number(productId),
    );
};

const getItemTotal = (item) => {
    const quantity = Number(item.quantity || 0);
    const unitPrice = Number(item.unit_price || 0);

    return quantity * unitPrice;
};

const totalAmount = computed(() => {
    return props.form.items.reduce((total, item) => {
        return total + getItemTotal(item);
    }, 0);
});

const formatMoney = (value) => {
    return Number(value || 0).toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
</script>
<template>
    <form
        class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
        @submit.prevent="emit('submit')"
    >
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Supplier
                </label>

                <select
                    v-model="form.supplier_id"
                    class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Select supplier</option>

                    <option
                        v-for="supplier in suppliers"
                        :key="supplier.id"
                        :value="supplier.id"
                    >
                        {{ supplier.code ? `${supplier.code} - ` : ""
                        }}{{ supplier.name }}
                    </option>
                </select>

                <p
                    v-if="form.errors.supplier_id"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.supplier_id }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Order Date
                </label>

                <input
                    v-model="form.order_date"
                    type="date"
                    class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />

                <p
                    v-if="form.errors.order_date"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.order_date }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">
                    Expected Date
                </label>

                <input
                    v-model="form.expected_date"
                    type="date"
                    class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />

                <p
                    v-if="form.errors.expected_date"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.expected_date }}
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
                    placeholder="Optional purchase order note"
                ></textarea>

                <p v-if="form.errors.note" class="mt-1 text-sm text-red-600">
                    {{ form.errors.note }}
                </p>
            </div>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">
                        Purchase Items
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Add products, quantity, and unit price.
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

            <p v-if="form.errors.items" class="mt-3 text-sm text-red-600">
                {{ form.errors.items }}
            </p>

            <div class="mt-4 space-y-4">
                <div
                    v-for="(item, index) in form.items"
                    :key="index"
                    class="rounded-2xl border border-slate-200 p-4"
                >
                    <div
                        class="grid gap-4 lg:grid-cols-[1fr_140px_160px_160px_auto]"
                    >
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
                                Unit: {{ findProduct(item.product_id).unit }}
                                <span
                                    v-if="findProduct(item.product_id).category"
                                >
                                    · Category:
                                    {{
                                        findProduct(item.product_id).category
                                            .name
                                    }}
                                </span>
                            </div>

                            <p
                                v-if="form.errors[`items.${index}.product_id`]"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors[`items.${index}.product_id`] }}
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
                                class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />

                            <p
                                v-if="form.errors[`items.${index}.quantity`]"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors[`items.${index}.quantity`] }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700"
                            >
                                Unit Price
                            </label>

                            <input
                                v-model="item.unit_price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />

                            <p
                                v-if="form.errors[`items.${index}.unit_price`]"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors[`items.${index}.unit_price`] }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700"
                            >
                                Total
                            </label>

                            <div
                                class="mt-1 rounded-xl bg-slate-50 px-3 py-2 text-right text-sm font-semibold text-slate-950 ring-1 ring-slate-200"
                            >
                                {{ formatMoney(getItemTotal(item)) }}
                            </div>
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

            <div class="mt-6 flex justify-end">
                <div
                    class="w-full max-w-sm rounded-2xl bg-slate-950 p-5 text-white"
                >
                    <div class="text-sm text-slate-300">Total Amount</div>

                    <div class="mt-2 text-3xl font-bold">
                        {{ formatMoney(totalAmount) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3">
            <Link
                href="/purchase-orders"
                class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Cancel
            </Link>

            <button
                type="submit"
                class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                :disabled="form.processing"
            >
                {{ submitLabel }}
            </button>
        </div>
    </form>
</template>
