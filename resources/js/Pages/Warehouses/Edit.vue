<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    warehouse: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    code: props.warehouse.code,
    name: props.warehouse.name,
    location: props.warehouse.location ?? "",
    is_active: props.warehouse.is_active,
});

const submit = () => {
    form.put(`/warehouses/${props.warehouse.id}`);
};
</script>

<template>
    <Head title="Edit Warehouse" />

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
                    Edit Warehouse
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Update warehouse master data.
                </p>
            </section>

            <form
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                @submit.prevent="submit"
            >
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Warehouse Code
                        </label>

                        <input
                            v-model="form.code"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                        <p
                            v-if="form.errors.code"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Warehouse Name
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                            Location
                        </label>

                        <input
                            v-model="form.location"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                        <p
                            v-if="form.errors.location"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.location }}
                        </p>
                    </div>

                    <div>
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
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.is_active }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <Link
                        href="/warehouses"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Update Warehouse
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
