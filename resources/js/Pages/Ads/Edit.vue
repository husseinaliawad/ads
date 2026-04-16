<script setup>
import AdForm from '@/Pages/Ads/Partials/AdForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    ad: {
        type: Object,
        required: true,
    },
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
    title: props.ad.title || '',
    description: props.ad.description || '',
    price: props.ad.price || '',
    city: props.ad.city || '',
    area: props.ad.area || '',
    category_id: props.ad.category_id || '',
    condition: props.ad.condition || '',
    contact_phone: props.ad.contact_phone || '',
    whatsapp_number: props.ad.whatsapp_number || '',
    status: props.ad.status === 'draft' ? 'draft' : 'pending',
    images: [],
    removed_image_ids: [],
});

const submit = () => {
    form
        .transform((data) => ({ ...data, _method: 'put' }))
        .post(route('ads.update', props.ad.slug), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="تعديل الإعلان" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-900">تعديل الإعلان</h1>
        </template>

        <section class="card-surface">
            <AdForm
                :form="form"
                :categories="categories"
                :conditions="conditions"
                :existing-images="ad.images || []"
                submit-label="حفظ التعديلات"
                @submit="submit"
            />
        </section>
    </AuthenticatedLayout>
</template>
