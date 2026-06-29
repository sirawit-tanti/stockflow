<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    product_category_id: "",
    sku: "",
    name: "",
    description: "",
    unit: "pcs",
    minimum_stock: 0,
    is_active: true,
});

const submit = () => {
    form.post("/products");
};
</script>

<template>
    <Head title="Create Product" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-4xl space-y-6">
            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <p
                    class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                >
                    Master Data
                </p>

                <h1
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                >
                    Create Product
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Add a new inventory product with SKU, category, unit, and
                    minimum stock.
                </p>
            </section>

            <form
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                @submit.prevent="submit"
            >
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            SKU
                        </label>

                        <input
                            v-model="form.sku"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: IT-MOUSE-001"
                        />
                        <p
                            v-if="form.errors.sku"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.sku }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Product Name
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring:indigo-500"
                            placeholder="Example: Wireless Mouse"
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Category
                        </label>

                        <select
                            v-model="form.product_category_id"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">No Category</option>

                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.product_category_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.product_category_id }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Unit
                        </label>

                        <select
                            v-model="form.unit"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="pcs">pcs</option>
                            <option value="box">box</option>
                            <option value="kg">kg</option>
                            <option value="liter">liter</option>
                            <option value="set">set</option>
                        </select>

                        <p
                            v-if="form.errors.unit"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.unit }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Minimum Stock
                        </label>

                        <input
                            v-model="form.minimum_stock"
                            type="number"
                            min="0"
                            step="0.01"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                        <p
                            v-if="form.errors.minimum_stock"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.minimum_stock }}
                        </p>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />

                            <span class="text-sm font-medium text-slate-700">
                                Active
                            </span>
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Description
                        </label>

                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional product description"
                        ></textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <Link
                        href="/products"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
