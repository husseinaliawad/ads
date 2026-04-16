<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <nav v-if="links.length > 3" class="mt-8 flex flex-wrap items-center justify-center gap-2">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                class="rounded-lg border px-3 py-2 text-sm transition"
                :class="link.active
                    ? 'border-blue-600 bg-blue-600 text-white'
                    : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:text-blue-600'"
                v-html="link.label"
            />
            <span
                v-else
                class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400"
                v-html="link.label"
            />
        </template>
    </nav>
</template>

