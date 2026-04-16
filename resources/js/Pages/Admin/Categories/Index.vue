<script setup>
import Pagination from '@/Components/Pagination.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: {
        type: Object,
        required: true,
    },
    parentOptions: {
        type: Array,
        default: () => [],
    },
});

const createForm = useForm({
    name: '',
    icon: '',
    description: '',
    parent_id: '',
    is_active: true,
    sort_order: 0,
});

const editId = ref(null);
const editForm = useForm({
    name: '',
    icon: '',
    description: '',
    parent_id: '',
    is_active: true,
    sort_order: 0,
});

const createCategory = () => {
    createForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => createForm.reset(),
    });
};

const startEdit = (category) => {
    editId.value = category.id;
    editForm.name = category.name;
    editForm.icon = category.icon || '';
    editForm.description = category.description || '';
    editForm.parent_id = category.parent_id || '';
    editForm.is_active = !!category.is_active;
    editForm.sort_order = category.sort_order || 0;
};

const updateCategory = () => {
    editForm.patch(route('admin.categories.update', editId.value), {
        preserveScroll: true,
        onSuccess: () => {
            editId.value = null;
        },
    });
};

const deleteCategory = (categoryId) => {
    router.delete(route('admin.categories.destroy', categoryId), { preserveScroll: true });
};
</script>

<template>
    <Head title="إدارة التصنيفات" />

    <AdminLayout>
        <section class="card-surface space-y-4">
            <h3 class="font-bold text-slate-900">إضافة تصنيف جديد</h3>
            <form class="grid gap-2 sm:grid-cols-2" @submit.prevent="createCategory">
                <input v-model="createForm.name" type="text" class="input-modern" placeholder="اسم التصنيف">
                <input v-model="createForm.icon" type="text" class="input-modern" placeholder="أيقونة">
                <textarea v-model="createForm.description" class="input-modern sm:col-span-2" placeholder="وصف مختصر"></textarea>
                <select v-model="createForm.parent_id" class="input-modern">
                    <option value="">بدون تصنيف أب</option>
                    <option v-for="category in parentOptions" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
                <input v-model="createForm.sort_order" type="number" min="0" class="input-modern" placeholder="ترتيب العرض">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input v-model="createForm.is_active" type="checkbox">
                    تصنيف نشط
                </label>
                <button type="submit" class="btn-primary sm:col-span-2">إضافة</button>
            </form>
        </section>

        <section class="card-surface overflow-x-auto">
            <table class="min-w-full text-right text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="py-2">الاسم</th>
                        <th class="py-2">الأب</th>
                        <th class="py-2">الترتيب</th>
                        <th class="py-2">الحالة</th>
                        <th class="py-2">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories.data" :key="category.id" class="border-b border-slate-100">
                        <template v-if="editId === category.id">
                            <td class="py-2"><input v-model="editForm.name" class="input-modern text-xs"></td>
                            <td class="py-2">
                                <select v-model="editForm.parent_id" class="input-modern text-xs">
                                    <option value="">بدون أب</option>
                                    <option v-for="item in parentOptions" :key="item.id" :value="item.id">{{ item.name }}</option>
                                </select>
                            </td>
                            <td class="py-2"><input v-model="editForm.sort_order" type="number" class="input-modern text-xs"></td>
                            <td class="py-2">
                                <label class="flex items-center gap-2 text-xs">
                                    <input v-model="editForm.is_active" type="checkbox">
                                    نشط
                                </label>
                            </td>
                            <td class="py-2">
                                <button type="button" class="rounded-lg bg-emerald-500 px-2 py-1 text-xs font-semibold text-white" @click="updateCategory">
                                    حفظ
                                </button>
                            </td>
                        </template>
                        <template v-else>
                            <td class="py-2 font-semibold">{{ category.name }}</td>
                            <td class="py-2">{{ category.parent?.name || '-' }}</td>
                            <td class="py-2">{{ category.sort_order }}</td>
                            <td class="py-2">{{ category.is_active ? 'نشط' : 'متوقف' }}</td>
                            <td class="py-2 space-x-1 space-x-reverse">
                                <button type="button" class="rounded-lg border border-slate-200 px-2 py-1 text-xs" @click="startEdit(category)">تعديل</button>
                                <button type="button" class="rounded-lg border border-rose-200 px-2 py-1 text-xs text-rose-600" @click="deleteCategory(category.id)">حذف</button>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>

            <Pagination :links="categories.links" />
        </section>
    </AdminLayout>
</template>

