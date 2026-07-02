<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    status: {
        type: Number,
        required: true,
    },
});

const page = usePage();

const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const title = computed(() => {
    return (
        {
            403: "Access Denied",
            404: "Page Not Found",
            419: "Page Expired",
            500: "Server Error",
            503: "Service Unavailable",
        }[props.status] || "Something Went Wrong"
    );
});

const description = computed(() => {
    return (
        {
            403: "You do not have permission to access this page. Please contact an administrator if you think this is a mistake.",
            404: "The page you are looking for does not exist or may have been moved.",
            419: "Your session has expired. Please refresh the page and try again.",
            500: "Something went wrong on the server. Please try again later.",
            503: "The system is temporarily unavailable. Please check back soon.",
        }[props.status] || "An unexpected error occurred"
    );
});

const statusLabel = computed(() => {
    return `${props.status}`;
});

const primaryLink = computed(() => {
    if (isAuthenticated.value) {
        return "/dashboard";
    }

    return "/login";
});

const primaryLabel = computed(() => {
    if (isAuthenticated.value) {
        return "Back to Dashboard";
    }

    return "Back to Login";
});

const goBack = () => {
    window.history.length > 1
        ? window.history.back()
        : router.visit(primaryLink.value);
};
</script>

<template>
    <Head :title="title" />

    <AuthenticatedLayout v-if="isAuthenticated">
        <div class="flex min-h-[70vh] items-center justify-center">
            <section
                class="w-full max-w-2xl rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200"
            >
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-red-50 text-3xl font-bold text-red-600 ring-1 ring-red-100"
                >
                    {{ statusLabel }}
                </div>

                <h1
                    class="mt-6 text-3xl font-bold tracking-tight text-slate-950"
                >
                    {{ title }}
                </h1>

                <p class="mt-3 text-sm leading-6 text-slate-500">
                    {{ description }}
                </p>

                <div
                    v-if="status === 403"
                    class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-left text-sm text-amber-800"
                >
                    <div class="font-semibold text-amber-900">
                        Permission Required
                    </div>

                    <p class="mt-1">
                        Your current role does not include the permission needed
                        for this action.
                    </p>
                </div>

                <div
                    class="mt-8 flex flex-col justify-center gap-3 sm:flex-row"
                >
                    <button
                        type="button"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        @click="goBack"
                    >
                        Go Back
                    </button>

                    <Link
                        :href="primaryLink"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                    >
                        {{ primaryLabel }}
                    </Link>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>

    <div
        v-else
        class="flex min-h-screen items-center justify-center bg-slate-100 px-4"
    >
        <section
            class="w-full max-w-2xl rounded-3xl bg-white p-8 text-center shadow-sm ring-1 ring-slate-200"
        >
            <div
                class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-red-50 text-3xl font-bold text-red-600 ring-1 ring-red-100"
            >
                {{ statusLabel }}
            </div>

            <h1 class="mt-6 text-3xl font-bold tracking-tight text-slate-950">
                {{ title }}
            </h1>

            <p class="mt-3 text-sm leading-6 text-slate-500">
                {{ description }}
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <button
                    type="button"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    @click="goBack"
                >
                    Go Back
                </button>

                <Link
                    :href="primaryLink"
                    class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    {{ primaryLabel }}
                </Link>
            </div>
        </section>
    </div>
</template>
