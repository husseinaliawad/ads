<script setup>
import AdCard from '@/Components/AdCard.vue';
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    ads: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="إعلاناتي" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">إعلاناتي</h2>
                <Link :href="route('ads.create')" class="btn-primary">إضافة إعلان</Link>
            </div>
        </template>

        <section v-if="ads.data.length" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <AdCard
                    v-for="ad in ads.data"
                    :key="ad.id"
                    :ad="ad"
                    show-actions
                />
            </div>
            <Pagination :links="ads.links" />
        </section>

        <section v-else class="card-surface text-center">
            <p class="text-slate-600">لا توجد لديك إعلانات حاليًا.</p>
            <Link :href="route('ads.create')" class="mt-4 inline-flex btn-primary">أنشئ إعلانك الأول</Link>
        </section>
    </AuthenticatedLayout>
</template>

