<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    site_name: props.settings.site_name || '',
    site_logo: props.settings.site_logo || '',
    primary_color: props.settings.primary_color || '#2563EB',
    secondary_color: props.settings.secondary_color || '#10B981',
    featured_ads_limit: props.settings.featured_ads_limit || 8,
});

const submit = () => {
    form.patch(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="الإعدادات العامة" />

    <AdminLayout>
        <section class="card-surface space-y-4">
            <h3 class="text-lg font-bold text-slate-900">الإعدادات العامة</h3>

            <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="submit">
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-semibold text-slate-600">اسم الموقع</label>
                    <input v-model="form.site_name" type="text" class="input-modern">
                </div>

                <div class="space-y-1 sm:col-span-2">
                    <label class="text-sm font-semibold text-slate-600">رابط الشعار (اختياري)</label>
                    <input v-model="form.site_logo" type="text" class="input-modern">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">اللون الأساسي</label>
                    <input v-model="form.primary_color" type="text" class="input-modern">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">اللون الثانوي</label>
                    <input v-model="form.secondary_color" type="text" class="input-modern">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">عدد الإعلانات المميزة في الرئيسية</label>
                    <input v-model="form.featured_ads_limit" type="number" min="1" max="30" class="input-modern">
                </div>

                <button type="submit" class="btn-primary sm:col-span-2" :disabled="form.processing">
                    حفظ الإعدادات
                </button>
            </form>
        </section>
    </AdminLayout>
</template>

