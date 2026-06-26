<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const summaryCards = [
    {
        label: 'Total Products',
        value: '0',
        description: 'Active products in inventory',
    },
    {
        label: 'Pending Purchase Orders',
        value: '0',
        description: 'Waiting for manager approval',
    },
    {
        label: 'Low Stock Items',
        value: '0',
        description: 'Products below minimum stock',
    },
    {
        label: 'Stock Movements',
        value: '0',
        description: 'Recent inventory transactions',
    },
];

const workflowSteps = [
    {
        title: 'Create Purchase Order',
        role: 'Purchasing',
        description: 'Create PO with supplier and product items.',
    },
    {
        title: 'Submit for Approval',
        role: 'Purchasing',
        description: 'Move PO status from DRAFT to PENDING_APPROVAL.',
    },
    {
        title: 'Approve Purchase Order',
        role: 'Manager',
        description: 'Review and approve purchase order.',
    },
    {
        title: 'Receive Stock',
        role: 'Warehouse',
        description: 'Receive approved PO items into warehouse stock.',
    },
    {
        title: 'Record Stock Movement',
        role: 'System',
        description: 'Every stock change must create movement history.',
    },
];

const upcomingModules = [
    'Product Management',
    'Supplier Management',
    'Warehouse Management',
    'Purchase Order Workflow',
    'Receive Stock',
    'Stock Movement History',
    'Low Stock Alert',
    'Reports & Export',
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                            Portfolio Project
                        </p>

                        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">
                            StockFlow Dashboard
                        </h1>

                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                            Inventory and purchase management system built with Laravel,
                            Vue, Inertia, Tailwind CSS, and role-based access control.
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-950 px-4 py-3 text-white">
                        <div class="text-xs text-slate-400">
                            Current Phase
                        </div>
                        <div class="text-sm font-semibold">
                            Step 2: Base Layout
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div
                    v-for="card in summaryCards"
                    :key="card.label"
                    class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200"
                >
                    <div class="text-sm font-medium text-slate-500">
                        {{ card.label }}
                    </div>

                    <div class="mt-3 text-3xl font-bold text-slate-950">
                        {{ card.value }}
                    </div>

                    <div class="mt-2 text-sm text-slate-500">
                        {{ card.description }}
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-3">
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">
                                Core Workflow
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Main purchase and stock receiving process.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div
                            v-for="(step, index) in workflowSteps"
                            :key="step.title"
                            class="flex gap-4 rounded-xl border border-slate-200 p-4"
                        >
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-950 text-sm font-bold text-white">
                                {{ index + 1 }}
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-semibold text-slate-950">
                                        {{ step.title }}
                                    </h3>

                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                        {{ step.role }}
                                    </span>
                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ step.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-semibold text-slate-950">
                        Upcoming Modules
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Modules planned for the MVP roadmap.
                    </p>

                    <div class="mt-6 space-y-3">
                        <div
                            v-for="module in upcomingModules"
                            :key="module"
                            class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3"
                        >
                            <span class="text-sm font-medium text-slate-700">
                                {{ module }}
                            </span>

                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-slate-500 ring-1 ring-slate-200">
                                Planned
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <div class="text-sm font-semibold text-amber-900">
                    Important Stock Rule
                </div>

                <p class="mt-1 text-sm text-amber-800">
                    Every time warehouse stock quantity changes, the system must create
                    a stock movement record. This rule will be implemented in the stock
                    receiving service later.
                </p>
            </section>
        </div>
    </AuthenticatedLayout>
</template>