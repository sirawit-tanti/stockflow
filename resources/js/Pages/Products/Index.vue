<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            search: "",
            category_id: "",
        }),
    },
});

const search = ref(props.filters.search ?? "");
const categoryId = ref(props.filters.category_id ?? "");

const submitFilter = () => {
    router.get(
        "/products",
        {
            search: search.value,
            category_id: categoryId.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const clearFilter = () => {
    search.value = "";
    categoryId.value = "";
    router.get("/products");
};

const deleteProduct = (product) => {
    if (!confirm(`Delete product "${product.name}"?`)) {
        return;
    }

    router.delete(`/products/${product.id}`);
};
</script>
<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Master Data
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Products
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Manage inventory product master data, SKU, unit, and
                        minimum stock.
                    </p>
                </div>

                <Link
                    href="/products/create"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Add Product
                </Link>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <form
                    class="grid gap-3 lg:grid-cols-[1fr_240px_auto]"
                    @submit.prevent="submitFilter"
                >
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search SKU, product name, or description..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <select
                        v-model="categoryId"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Categories</option>

                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
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
                                    SKU
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Product
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Category
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Unit
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Min Stock
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr
                                v-for="product in products.data"
                                :key="product.id"
                                class="hover:bg-slate-50"
                            >
                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-950"
                                >
                                    {{ product.sku }}
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-semibold text-slate-950"
                                    >
                                        {{ product.name }}
                                    </div>

                                    <div
                                        class="mt-1 max-w-md truncate text-sm text-slate-500"
                                    >
                                        {{ product.description || "-" }}
                                    </div>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ product.category?.name || "-" }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                                >
                                    {{ product.unit }}
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm text-slate-600"
                                >
                                    {{ product.minimum_stock }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <span
                                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="
                                            product.is_active
                                                ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200'
                                                : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200'
                                        "
                                    >
                                        {{
                                            product.is_active
                                                ? "Active"
                                                : "Inactive"
                                        }}
                                    </span>
                                </td>

                                <td
                                    class="whitespace-nowrap px-6 py-4 text-right text-sm"
                                >
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/products/${product.id}/edit`"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 font-medium text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            class="rounded-lg border border-red-200 px-3 py-1.5 font-medium text-red-700 transition hover:bg-red-50"
                                            @click="deleteProduct(product)"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="products.data.length === 0">
                                <td
                                    colspan="7"
                                    class="px-6 py-10 text-center text-sm text-slate-500"
                                >
                                    No products found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="products.links.length > 3"
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-6 py-4"
                >
                    <div class="text-sm text-slate-500">
                        Showing
                        <span class="font-medium">{{ products.from }}</span>
                        to
                        <span class="font-medium">{{ products.to }}</span>
                        of
                        <span class="font-medium">{{ products.total }}</span>
                        results
                    </div>

                    <div class="flex flex-wrap gap-1">
                        <Link
                            v-for="link in products.links"
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
