<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
</script>

<template>
    <MainLayout>
        <section v-if="$slots.header" class="card-surface">
            <slot name="header" />
        </section>
        <slot />
    </MainLayout>
</template>

