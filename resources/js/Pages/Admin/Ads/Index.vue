<script setup>
import Pagination from '@/Components/Pagination.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    ads: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    statuses: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const localFilters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    category: props.filters.category || '',
});

const applyFilters = () => {
    router.get(route('admin.ads.index'), localFilters, { preserveState: true, replace: true });
};

const updateAd = (ad, event) => {
    router.patch(route('admin.ads.update', ad.id), {
        status: event.target.value,
        is_featured: ad.is_featured,
    }, { preserveScroll: true });
};

const toggleFeatured = (ad) => {
    router.patch(route('admin.ads.update', ad.id), {
        status: ad.status,
        is_featured: !ad.is_featured,
    }, { preserveScroll: true });
};
</script>

<template>
    <Head title="إدارة الإعلانات" />

    <AdminLayout>
        <section class="card-surface space-y-3">
            <div class="grid gap-2 sm:grid-cols-3">
                <input v-model="localFilters.search" type="text" class="input-modern" placeholder="بحث...">
                <select v-model="localFilters.status" class="input-modern">
                    <option value="">كل الحالات</option>
                    <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                </select>
                <select v-model="localFilters.category" class="input-modern">
                    <option value="">كل التصنيفات</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
            </div>
            <button type="button" class="btn-primary" @click="applyFilters">تطبيق الفلترة</button>
        </section>

        <section class="card-surface overflow-x-auto">
            <table class="min-w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="py-2">العنوان</th>
                        <th class="py-2">المالك</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">مميز</th>
                        <th class="py-2">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ad in ads.data" :key="ad.id" class="border-b border-slate-100">
                        <td class="py-2">
                            <Link :href="route('ads.show', ad.slug)" class="font-semibold text-blue-700">{{ ad.title }}</Link>
                        </td>
                        <td class="py-2">{{ ad.user?.name }}</td>
                        <td class="py-2">
                            <select class="input-modern text-xs" :value="ad.status" @change="updateAd(ad, $event)">
                                <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                            </select>
                        </td>
                        <td class="py-2">
                            <button
                                type="button"
                                class="rounded-lg px-2 py-1 text-xs font-semibold"
                                :class="ad.is_featured ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                @click="toggleFeatured(ad)"
                            >
                                {{ ad.is_featured ? 'نعم' : 'لا' }}
                            </button>
                        </td>
                        <td class="py-2">
                            <Link
                                :href="route('admin.ads.destroy', ad.id)"
                                method="delete"
                                as="button"
                                class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                            >
                                حذف
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="ads.links" />
        </section>
    </AdminLayout>
</template>

