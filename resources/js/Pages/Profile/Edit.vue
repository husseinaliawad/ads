<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="الملف الشخصي" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-900">الملف الشخصي</h2>
        </template>

        <div class="space-y-6">
            <div class="card-surface p-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
            </div>

            <div class="card-surface p-6">
                    <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="card-surface p-6">
                    <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
