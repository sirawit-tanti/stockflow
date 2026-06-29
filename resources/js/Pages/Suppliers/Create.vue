<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    code: "",
    name: "",
    contact_name: "",
    phone: "",
    email: "",
    address: "",
    is_active: true,
});

const submit = () => {
    form.post("/suppliers");
};
</script>
<template>
    <Head title="Create Supplier" />

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
                    Create Supplier
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Add a new supplier for purchase order workflow.
                </p>
            </section>

            <form
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                @submit.prevent="submit"
            >
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Supplier Code
                        </label>

                        <input
                            v-model="form.code"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: SUP-001"
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
                            Supplier Name
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: ABC Trading Co., Ltd."
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
                            Contact Name
                        </label>

                        <input
                            v-model="form.contact_name"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: Somchai"
                        />

                        <p
                            v-if="form.errors.contact_name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.contact_name }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Phone
                        </label>

                        <input
                            v-model="form.phone"
                            type="text"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: 02-000-0000"
                        />

                        <p
                            v-if="form.errors.phone"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.phone }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Email
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Example: contact@example.com"
                        />

                        <p
                            v-if="form.errors.email"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">
                            Address
                        </label>

                        <textarea
                            v-model="form.address"
                            rows="4"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Optional supplier address"
                        ></textarea>

                        <p
                            v-if="form.errors.address"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.address }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
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
                        href="/suppliers"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
