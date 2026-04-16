<script setup>
import AdCard from '@/Components/AdCard.vue';
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    ads: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="المفضلة" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-900">الإعلانات المفضلة</h2>
        </template>

        <section v-if="ads.data.length" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <AdCard v-for="ad in ads.data" :key="ad.id" :ad="ad" />
            </div>
            <Pagination :links="ads.links" />
        </section>

        <section v-else class="card-surface text-center text-slate-600">
            لا توجد إعلانات في المفضلة بعد.
        </section>
    </AuthenticatedLayout>
</template>

