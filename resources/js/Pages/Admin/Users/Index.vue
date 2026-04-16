<script setup>
import Pagination from '@/Components/Pagination.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const localFilters = reactive({
    search: props.filters.search || '',
    role: props.filters.role || '',
    is_active: props.filters.is_active ?? '',
});

const applyFilters = () => {
    router.get(route('admin.users.index'), localFilters, { preserveState: true, replace: true });
};

const updateUser = (user, payload = {}) => {
    router.patch(route('admin.users.update', user.id), {
        name: user.name,
        phone: user.phone,
        city: user.city,
        role: payload.role ?? user.role,
        is_active: payload.is_active ?? user.is_active,
    }, { preserveScroll: true });
};
</script>

<template>
    <Head title="إدارة المستخدمين" />

    <AdminLayout>
        <section class="card-surface space-y-3">
            <div class="grid gap-2 sm:grid-cols-3">
                <input v-model="localFilters.search" type="text" class="input-modern" placeholder="اسم / بريد / هاتف">
                <select v-model="localFilters.role" class="input-modern">
                    <option value="">كل الأدوار</option>
                    <option value="user">مستخدم</option>
                    <option value="admin">أدمن</option>
                </select>
                <select v-model="localFilters.is_active" class="input-modern">
                    <option value="">الحالة</option>
                    <option :value="1">نشط</option>
                    <option :value="0">محظور</option>
                </select>
            </div>
            <button type="button" class="btn-primary" @click="applyFilters">تطبيق</button>
        </section>

        <section class="card-surface overflow-x-auto">
            <table class="min-w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="py-2">الاسم</th>
                        <th class="py-2">البريد</th>
                        <th class="py-2">الدور</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id" class="border-b border-slate-100">
                        <td class="py-2 font-semibold">{{ user.name }}</td>
                        <td class="py-2">{{ user.email }}</td>
                        <td class="py-2">
                            <select class="input-modern text-xs" :value="user.role" @change="updateUser(user, { role: $event.target.value })">
                                <option value="user">مستخدم</option>
                                <option value="admin">أدمن</option>
                            </select>
                        </td>
                        <td class="py-2">
                            <span
                                class="rounded-lg px-2 py-1 text-xs font-semibold"
                                :class="user.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                            >
                                {{ user.is_active ? 'نشط' : 'محظور' }}
                            </span>
                        </td>
                        <td class="py-2">
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                @click="updateUser(user, { is_active: !user.is_active })"
                            >
                                {{ user.is_active ? 'حظر' : 'تفعيل' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <Pagination :links="users.links" />
        </section>
    </AdminLayout>
</template>

