<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import PurchaseOrderForm from "./Partials/PurchaseOrderForm.vue";

defineProps({
    suppliers: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
});

const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    supplier_id: "",
    order_date: today,
    expected_date: "",
    note: "",
    items: [
        {
            product_id: "",
            quantity: 1,
            unit_price: 0,
        },
    ],
});

const submit = () => {
    form.post("/purchase-orders");
};
</script>

<template>
    <Head title="Create Purchase Order" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-6xl space-y-6">
            <section
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200"
            >
                <p
                    class="text-sm font-semibold uppercase tracking-wide text-indigo-600"
                >
                    Purchasing
                </p>

                <h1
                    class="mt-2 text-2xl font-bold tracking-tight text-slate-950"
                >
                    Create Purchase Order
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Create a draft purchase order with supplier and product
                    items.
                </p>
            </section>

            <PurchaseOrderForm
                :form="form"
                :suppliers="suppliers"
                :products="products"
                submit-label="Save Draft"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
