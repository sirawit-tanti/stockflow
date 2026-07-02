<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    auditLog: {
        type: Object,
        required: true,
    },
});

const formatDateTime = (value) => {
    if (!value) {
        return "-";
    }

    return new Date(value).toLocaleString("th-TH", {
        timeZone: "Asia/Bangkok",
        year: "numeric",
        month: "short",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const formatClassName = (value) => {
    if (!value) {
        return "-";
    }

    return String(value).split("\\").pop();
};

const formatJson = (value) => {
    if (!value) {
        return "-";
    }

    return JSON.stringify(value, null, 2);
};
</script>

<template>
    <Head title="Audit Log Detail" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <section
                class="flex flex-col gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <p
                        class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                    >
                        Audit Log Detail
                    </p>

                    <h1
                        class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                    >
                        Log #{{ auditLog.id }}
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Review detailed information about this system activity.
                    </p>
                </div>

                <Link
                    href="/audit-logs"
                    class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Back
                </Link>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2"
                >
                    <h2 class="text-lg font-semibold text-slate-950">
                        Activity Information
                    </h2>

                    <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-slate-500">Date</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ formatDateTime(auditLog.created_at) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">User</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ auditLog.user?.name || "System" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Email</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ auditLog.user?.email || "-" }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Module</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ auditLog.module }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Action</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ auditLog.action }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">Target</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ formatClassName(auditLog.auditable_type) }}
                                <span v-if="auditLog.auditable_id">
                                    #{{ auditLog.auditable_id }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-slate-500">IP Address</dt>
                            <dd
                                class="mt-1 text-sm font-semibold text-slate-950"
                            >
                                {{ auditLog.ip_address || "-" }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <dt class="text-sm text-slate-500">Description</dt>

                        <dd
                            class="mt-1 rounded-xl bg-slate-50 p-4 text-sm text-slate-700"
                        >
                            {{ auditLog.description || "-" }}
                        </dd>
                    </div>
                </div>

                <div class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm">
                    <div class="text-sm text-slate-300">Audit Summary</div>

                    <div class="mt-4 text-3xl font-bold">
                        {{ auditLog.action }}
                    </div>

                    <div class="mt-2 text-sm text-slate-400">
                        {{ auditLog.module }}
                    </div>

                    <div
                        class="mt-6 rounded-2xl bg-white/10 p-4 text-sm text-slate-300 ring-1 ring-white/10"
                    >
                        {{ formatDateTime(auditLog.created_at) }}
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                >
                    <h2 class="text-lg font-semibold text-slate-950">
                        Old Values
                    </h2>

                    <pre
                        class="mt-4 max-h-96 overflow-auto rounded-2xl bg-slate-950 p-4 text-xs text-slate-100"
                        >{{ formatJson(auditLog.old_values) }}</pre
                    >
                </div>

                <div
                    class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                >
                    <h2 class="text-lg font-semibold text-slate-950">
                        New Values
                    </h2>

                    <pre
                        class="mt-4 max-h-96 overflow-auto rounded-2xl bg-slate-950 p-4 text-xs text-slate-100"
                        >{{ formatJson(auditLog.new_values) }}</pre
                    >
                </div>
            </section>

            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <h2 class="text-lg font-semibold text-slate-950">User Agent</h2>

                <p
                    class="mt-3 break-words rounded-xl bg-slate-50 p-4 text-sm text-slate-600"
                >
                    {{ auditLog.user_agent || "-" }}
                </p>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
