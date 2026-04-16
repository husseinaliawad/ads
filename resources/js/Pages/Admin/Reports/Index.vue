<script setup>
import Pagination from '@/Components/Pagination.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { formatDate } from '@/utils/formatters';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    reports: {
        type: Object,
        required: true,
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
    status: props.filters.status || '',
});

const applyFilters = () => {
    router.get(route('admin.reports.index'), localFilters, { preserveState: true, replace: true });
};

const updateStatus = (reportId, status) => {
    router.patch(route('admin.reports.update', reportId), { status }, { preserveScroll: true });
};
</script>

<template>
    <Head title="إدارة البلاغات" />

    <AdminLayout>
        <section class="card-surface space-y-3">
            <div class="flex items-center gap-2">
                <select v-model="localFilters.status" class="input-modern max-w-xs">
                    <option value="">كل الحالات</option>
                    <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                </select>
                <button type="button" class="btn-primary" @click="applyFilters">فلترة</button>
            </div>
        </section>

        <section class="card-surface overflow-x-auto">
            <table class="min-w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="py-2">الإعلان</th>
                        <th class="py-2">المبلّغ</th>
                        <th class="py-2">السبب</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="report in reports.data" :key="report.id" class="border-b border-slate-100">
                        <td class="py-2 font-semibold text-slate-800">{{ report.ad?.title }}</td>
                        <td class="py-2">{{ report.user?.name || 'مستخدم محذوف' }}</td>
                        <td class="py-2">{{ report.reason }}</td>
                        <td class="py-2">
                            <select class="input-modern text-xs" :value="report.status" @change="updateStatus(report.id, $event.target.value)">
                                <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
                            </select>
                        </td>
                        <td class="py-2 text-slate-500">{{ formatDate(report.created_at) }}</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="reports.links" />
        </section>
    </AdminLayout>
</template>

