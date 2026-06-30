<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();

const user = computed(() => page.props.auth.user);

const navigationItems = [
    {
        label: "Dashboard",
        href: "/dashboard",
        activePattern: "/dashboard",
    },
    {
        label: "Product Categories",
        href: "/product-categories",
        activePattern: "/product-categories",
    },
    {
        label: "Products",
        href: "/products",
        activePattern: "/products",
    },
    {
        label: "Suppliers",
        href: "/suppliers",
        activePattern: "/suppliers",
    },
    {
        label: "Warehouses",
        href: "/warehouses",
        activePattern: "/warehouses",
    },
    {
        label: "Stock Balance",
        href: "/warehouse-stocks",
        activePattern: "/warehouse-stocks",
    },
    {
        label: "Purchase Orders",
        href: "/purchase-orders",
        activePattern: "/purchase-orders",
    },
    {
        label: "Stock Receipts",
        href: "/stock-receipts",
        activePattern: "/stock-receipts",
    },
    {
        label: "Stock Movements",
        href: "#",
        activePattern: "/stock-movements",
    },
    {
        label: "Reports",
        href: "#",
        activePattern: "/reports",
    },
];

const currentPath = computed(() => window.location.pathname);

const isActive = (pattern) => {
    return currentPath.value.startsWith(pattern);
};

const flash = computed(() => page.props.flash ?? {});
</script>

<template>
    <div class="min-h-screen bg-slate-100">
        <div class="flex min-h-screen">
            <aside
                class="hidden w-72 border-r border-slate-200 bg-slate-950 text-white lg:block"
            >
                <div
                    class="flex h-16 items-center border-b border-slate-800 px-6"
                >
                    <div>
                        <div class="text-lg font-bold tracking-tight">
                            StockFlow
                        </div>
                        <div class="text-xs text-slate-400">
                            Inventory & Purchase
                        </div>
                    </div>
                </div>

                <nav class="space-y-1 px-4 py-6">
                    <template v-for="item in navigationItems" :key="item.label">
                        <Link
                            v-if="item.href !== '#'"
                            :href="item.href"
                            class="flex items-center rounded-lg px-4 py-2.5 text-sm font-medium transition"
                            :class="
                                isActive(item.activePattern)
                                    ? 'bg-white text-slate-950'
                                    : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                            "
                        >
                            {{ item.label }}
                        </Link>

                        <div
                            v-else
                            class="flex cursor-not-allowed items-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-500"
                            title="Coming soon"
                        >
                            {{ item.label }}
                            <span
                                class="ml-auto rounded-full bg-slate-800 px-2 py-0.5 text-[10px] text-slate-400"
                            >
                                Soon
                            </span>
                        </div>
                    </template>
                </nav>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header
                    class="sticky top-0 z-20 border-b border-slate-200 bg-white"
                >
                    <div
                        class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8"
                    >
                        <div>
                            <div class="text-sm font-semibold text-slate-900">
                                StockFlow Management System
                            </div>
                            <div class="text-xs text-slate-500">
                                Laravel + Vue + Inertia Portfolio Project
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="hidden text-right sm:block">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ user.name }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ user.email }}
                                </div>
                            </div>

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div
                        v-if="flash.success"
                        class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                    >
                        {{ flash.success }}
                    </div>
                    <div
                        v-if="flash.error"
                        class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800"
                    >
                        {{ flash.error }}
                    </div>

                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
