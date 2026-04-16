<script setup>
import AdCard from '@/Components/AdCard.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    latestMyAds: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="لوحتي" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-slate-900">لوحة المستخدم</h2>
                <Link :href="route('ads.create')" class="btn-primary">إضافة إعلان جديد</Link>
            </div>
        </template>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="card-surface">
                <p class="text-sm text-slate-500">إجمالي إعلاناتي</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-900">{{ stats.ads_count }}</h3>
            </article>
            <article class="card-surface">
                <p class="text-sm text-slate-500">إعلاناتي المقبولة</p>
                <h3 class="mt-1 text-2xl font-extrabold text-emerald-600">{{ stats.approved_ads_count }}</h3>
            </article>
            <article class="card-surface">
                <p class="text-sm text-slate-500">المفضلة</p>
                <h3 class="mt-1 text-2xl font-extrabold text-blue-600">{{ stats.favorites_count }}</h3>
            </article>
            <article class="card-surface">
                <p class="text-sm text-slate-500">رسائل غير مقروءة</p>
                <h3 class="mt-1 text-2xl font-extrabold text-amber-600">{{ stats.unread_messages_count }}</h3>
            </article>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">أحدث إعلاناتي</h3>
                <Link :href="route('ads.my')" class="text-sm font-semibold text-blue-700 hover:text-blue-800">
                    عرض الكل
                </Link>
            </div>

            <div v-if="latestMyAds.length" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <AdCard v-for="ad in latestMyAds" :key="ad.id" :ad="ad" show-actions />
            </div>

            <div v-else class="card-surface text-center">
                <p class="text-slate-600">لم تقم بإضافة أي إعلان حتى الآن.</p>
                <Link :href="route('ads.create')" class="mt-4 inline-flex btn-primary">أضف أول إعلان الآن</Link>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

