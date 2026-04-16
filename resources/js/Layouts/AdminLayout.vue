<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const links = [
    { label: 'نظرة عامة', href: route('admin.dashboard') },
    { label: 'إدارة الإعلانات', href: route('admin.ads.index') },
    { label: 'إدارة المستخدمين', href: route('admin.users.index') },
    { label: 'إدارة التصنيفات', href: route('admin.categories.index') },
    { label: 'إدارة البلاغات', href: route('admin.reports.index') },
    { label: 'الإعدادات', href: route('admin.settings.index') },
];
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-900">لوحة تحكم الإدارة</h2>
        </template>

        <section class="grid gap-4 lg:grid-cols-[240px,1fr]">
            <aside class="card-surface space-y-1">
                <Link
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-blue-700"
                >
                    {{ link.label }}
                </Link>
            </aside>
            <div class="space-y-4">
                <slot />
            </div>
        </section>
    </AuthenticatedLayout>
</template>

