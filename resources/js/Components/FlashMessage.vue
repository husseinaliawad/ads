<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);
</script>

<template>
    <div class="space-y-2">
        <div
            v-if="success"
            class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
        >
            {{ success }}
        </div>

        <div
            v-if="error"
            class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700"
        >
            {{ error }}
        </div>
    </div>
</template>

