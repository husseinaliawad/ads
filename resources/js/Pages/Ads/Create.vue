<script setup>
import AdForm from '@/Pages/Ads/Partials/AdForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    conditions: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    title: '',
    description: '',
    price: '',
    city: '',
    area: '',
    category_id: '',
    condition: '',
    contact_phone: '',
    whatsapp_number: '',
    images: [],
});

const submit = () => {
    form.post(route('ads.store'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="إضافة إعلان" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-900">إضافة إعلان جديد</h1>
        </template>

        <section class="card-surface">
            <AdForm
                :form="form"
                :categories="categories"
                :conditions="conditions"
                submit-label="نشر الإعلان"
                @submit="submit"
            />
        </section>
    </AuthenticatedLayout>
</template>

