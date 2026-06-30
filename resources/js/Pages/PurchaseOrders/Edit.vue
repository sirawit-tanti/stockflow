<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PurchaseOrderForm from "./Partials/PurchaseOrderForm.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    purchaseOrder: {
        type: Object,
        required: true,
    },
    suppliers: {
        type: Array,
        default: () => [],
    },
    products: {
        type: Array,
        default: () => [],
    },
});

const formatDate = (value) => {
    if (!value) {
        return "";
    }

    return String(value).slice(0, 10);
};

const form = useForm({
    supplier_id: props.purchaseOrder.supplier_id,
    order_date: formatDate(props.purchaseOrder.order_date),
    expected_date: formatDate(props.purchaseOrder.expected_date),
    note: props.purchaseOrder.note ?? "",
    items: props.purchaseOrder.items.map((item) => ({
        product_id: item.product_id,
        quantity: item.quantity,
        unit_price: item.unit_price,
    })),
});

const submit = () => {
    form.put(`/purchase-orders/${props.purchaseOrder.id}`);
};
</script>

<template>
    <Head title="Edit Purchase Order" />

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
                    Edit Purchase Order
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Update draft purchase order {{ purchaseOrder.po_number }}.
                </p>
            </section>

            <PurchaseOrderForm
                :form="form"
                :suppliers="suppliers"
                :products="products"
                submit-label="Update Draft"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
