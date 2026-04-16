<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { formatDate } from '@/utils/formatters';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    latestPendingAds: {
        type: Array,
        default: () => [],
    },
    latestReports: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="لوحة الإدارة" />

    <AdminLayout>
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="card-surface">
                <p class="text-sm text-slate-500">المستخدمون</p>
                <p class="mt-1 text-2xl font-extrabold text-slate-900">{{ stats.users }}</p>
            </article>
            <article class="card-surface">
                <p class="text-sm text-slate-500">الإعلانات</p>
                <p class="mt-1 text-2xl font-extrabold text-blue-700">{{ stats.ads }}</p>
            </article>
            <article class="card-surface">
                <p class="text-sm text-slate-500">قيد المراجعة</p>
                <p class="mt-1 text-2xl font-extrabold text-amber-600">{{ stats.pending_ads }}</p>
            </article>
            <article class="card-surface">
                <p class="text-sm text-slate-500">البلاغات المعلقة</p>
                <p class="mt-1 text-2xl font-extrabold text-rose-600">{{ stats.pending_reports }}</p>
            </article>
        </section>

        <section class="card-surface space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900">أحدث الإعلانات بانتظار المراجعة</h3>
                <Link :href="route('admin.ads.index', { status: 'pending' })" class="text-sm font-semibold text-blue-700">عرض الكل</Link>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-right text-sm">
                    <thead>
                        <tr class="text-slate-500">
                            <th class="py-2">العنوان</th>
                            <th class="py-2">المالك</th>
                            <th class="py-2">التصنيف</th>
                            <th class="py-2">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ad in latestPendingAds" :key="ad.id" class="border-t border-slate-100">
                            <td class="py-2 font-semibold text-slate-800">{{ ad.title }}</td>
                            <td class="py-2">{{ ad.user?.name }}</td>
                            <td class="py-2">{{ ad.category?.name }}</td>
                            <td class="py-2 text-slate-500">{{ formatDate(ad.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card-surface space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900">آخر البلاغات</h3>
                <Link :href="route('admin.reports.index')" class="text-sm font-semibold text-blue-700">عرض الكل</Link>
            </div>
            <div class="space-y-2">
                <article
                    v-for="report in latestReports"
                    :key="report.id"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"
                >
                    <p class="font-semibold text-slate-800">{{ report.reason }}</p>
                    <p class="text-sm text-slate-600">{{ report.ad?.title }}</p>
                </article>
            </div>
        </section>
    </AdminLayout>
</template>

