<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    description: "",
    is_active: true,
});

const submit = () => {
    form.post("/product-categories");
};
</script>
<template>
    <Head title="Create Product Category" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-3xl space-y-6">
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
                    Create Product Category
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Add a new product category for inventory items.
                </p>
            </section>

            <form
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                @submit.prevent="submit"
            >
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Category Name
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: Office Supplies"
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
                            Description
                        </label>

                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional category description"
                        >
                        </textarea>

                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

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

                    <p
                        v-if="form.errors.is_active"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.is_active }}
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <Link
                        href="/product-categories"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
