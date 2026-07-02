<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    managedUser: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    name: props.managedUser.name,
    email: props.managedUser.email,
    password: "",
    password_confirmation: "",
    roles: props.managedUser.roles.map((role) => role.name),
});

const toggleRole = (roleName) => {
    if (form.roles.includes(roleName)) {
        form.roles = form.roles.filter((item) => item !== roleName);
        return;
    }

    form.roles.push(roleName);
};

const submit = () => {
    form.put(`/users/${props.managedUser.id}`);
};
</script>

<template>
    <Head title="Edit User" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-3xl space-y-6">
            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <p
                    class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                >
                    Administration
                </p>

                <h1
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                >
                    Edit User
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Update user information, reset password, or change roles.
                </p>
            </section>

            <form
                class="space-y-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
                @submit.prevent="submit"
            >
                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        Name
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
                        Email
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />

                    <p
                        v-if="form.errors.email"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"
                >
                    Leave password blank if you do not want to change it.
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            New Password
                        </label>

                        <input
                            v-model="form.password"
                            type="password"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />

                        <p
                            v-if="form.errors.password"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Confirm New Password
                        </label>

                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        Roles
                    </label>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <label
                            v-for="role in roles"
                            :key="role.id"
                            class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition"
                            :class="
                                form.roles.includes(role.name)
                                    ? 'border-indigo-500 bg-indigo-50'
                                    : 'border-slate-200 bg-white hover:bg-slate-50'
                            "
                        >
                            <input
                                type="checkbox"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                :checked="form.roles.includes(role.name)"
                                @change="toggleRole(role.name)"
                            />

                            <span class="text-sm font-semibold text-slate-800">
                                {{ role.name }}
                            </span>
                        </label>
                    </div>

                    <p
                        v-if="form.errors.roles"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.roles }}
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link
                        href="/users"
                        class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
