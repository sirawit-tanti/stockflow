<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post("/login", {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen bg-slate-100 px-4 py-10">
        <div
            class="mx-auto flex min-h-[calc(100vh-5rem)] max-w-6xl items-center justify-center"
        >
            <div
                class="grid w-full overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 lg:grid-cols-[0.9fr_1.1fr]"
            >
                <section class="hidden bg-slate-950 p-8 lg:block">
                    <div class="flex h-full flex-col justify-between">
                        <div>
                            <div class="inline-flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-lg font-black text-slate-950"
                                >
                                    SF
                                </div>

                                <div>
                                    <div class="text-lg font-black text-white">
                                        StockFlow
                                    </div>

                                    <div class="text-sm text-slate-400">
                                        Inventory & Purchase Management
                                    </div>
                                </div>
                            </div>

                            <div class="mt-12">
                                <p
                                    class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-300"
                                >
                                    Business Workflow
                                </p>

                                <h1
                                    class="mt-4 text-3xl font-black leading-tight tracking-tight text-white"
                                >
                                    Manage purchase orders and warehouse stock
                                    with role-based workflow.
                                </h1>

                                <p
                                    class="mt-4 text-sm leading-6 text-slate-300"
                                >
                                    A Laravel, Vue, Inertia, Tailwind CSS, and
                                    MySQL portfolio project designed for real
                                    company operations.
                                </p>
                            </div>
                        </div>

                        <div class="mt-10 grid gap-3">
                            <div
                                class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10"
                            >
                                <div class="text-sm font-bold text-white">
                                    Purchase Approval
                                </div>

                                <p class="mt-1 text-sm text-slate-300">
                                    Draft, submit, approve, reject, and track
                                    purchase orders.
                                </p>
                            </div>

                            <div
                                class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10"
                            >
                                <div class="text-sm font-bold text-white">
                                    Stock Movement
                                </div>

                                <p class="mt-1 text-sm text-slate-300">
                                    Every stock quantity change creates an audit
                                    movement record.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="flex items-center justify-center p-6 sm:p-10">
                    <div class="w-full max-w-md">
                        <div class="mb-8 text-center lg:hidden">
                            <div
                                class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-950 text-lg font-black text-white"
                            >
                                SF
                            </div>

                            <h1 class="mt-4 text-2xl font-black text-slate-950">
                                StockFlow
                            </h1>

                            <p class="mt-2 text-sm text-slate-500">
                                Inventory & Purchase Management
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                            >
                                Welcome back
                            </p>

                            <h2
                                class="mt-2 text-3xl font-black tracking-tight text-slate-950"
                            >
                                Sign in
                            </h2>

                            <p class="mt-2 text-sm text-slate-500">
                                Use your assigned account to access StockFlow.
                            </p>
                        </div>

                        <div
                            v-if="status"
                            class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700"
                        >
                            {{ status }}
                        </div>

                        <form class="mt-8 space-y-5" @submit.prevent="submit">
                            <div>
                                <label
                                    for="email"
                                    class="block text-sm font-semibold text-slate-700"
                                >
                                    Email
                                </label>

                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="username"
                                    autofocus
                                    class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="you@example.com"
                                />

                                <p
                                    v-if="form.errors.email"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label
                                        for="password"
                                        class="block text-sm font-semibold text-slate-700"
                                    >
                                        Password
                                    </label>

                                    <Link
                                        v-if="canResetPassword"
                                        href="/forgot-password"
                                        class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-500"
                                    >
                                        Forgot password?
                                    </Link>
                                </div>

                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    autocomplete="current-password"
                                    class="mt-2 w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm transition focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Enter your password"
                                />

                                <p
                                    v-if="form.errors.password"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <label class="flex items-center gap-2">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />

                                <span class="text-sm text-slate-600">
                                    Remember me
                                </span>
                            </label>

                            <button
                                type="submit"
                                class="flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="form.processing"
                            >
                                Sign in
                            </button>
                        </form>

                        <div
                            class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-4"
                        >
                            <div class="text-sm font-semibold text-slate-800">
                                Demo roles
                            </div>

                            <div class="mt-3 grid gap-2 text-xs text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span>Purchasing</span>
                                    <span class="font-semibold text-indigo-600"
                                        >Create PO</span
                                    >
                                </div>

                                <div class="flex items-center justify-between">
                                    <span>Manager</span>
                                    <span class="font-semibold text-amber-600"
                                        >Approve PO</span
                                    >
                                </div>

                                <div class="flex items-center justify-between">
                                    <span>Warehouse</span>
                                    <span class="font-semibold text-emerald-600"
                                        >Receive Stock</span
                                    >
                                </div>

                                <div class="flex items-center justify-between">
                                    <span>Admin</span>
                                    <span class="font-semibold text-slate-950"
                                        >Manage Users</span
                                    >
                                </div>
                            </div>
                        </div>

                        <p class="mt-6 text-center text-xs text-slate-500">
                            StockFlow Portfolio Project
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
