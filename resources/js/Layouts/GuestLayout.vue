<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-8">
        <div class="mb-6 text-center">
            <Link href="/" class="text-3xl font-extrabold text-blue-700">
                سوق الإعلانات
            </Link>
            <p class="mt-2 text-sm text-slate-500">منصة عربية احترافية للإعلانات المبوبة</p>
        </div>

        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white px-6 py-5 shadow-sm">
            <slot />
        </div>
    </div>
</template>
