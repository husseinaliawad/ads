<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { formatCurrency, formatNumber, formatRelativeTime } from '@/utils/formatters';

const props = defineProps({
    ad: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: false,
    },
});

const localFallback = '/images/placeholder-ad.svg';
const currentImage = ref(props.ad.primary_image?.url || localFallback);
const hasFeaturedBadge = computed(() => props.ad.is_featured === true);
</script>

<template>
    <article :class="hasFeaturedBadge ? 'card-surface-featured overflow-hidden p-0' : 'card-surface overflow-hidden p-0'">
        <Link :href="route('ads.show', ad.slug)" class="relative block overflow-hidden bg-slate-100">
            <img
                :src="currentImage"
                :alt="ad.title"
                class="h-56 w-full object-cover transition duration-500 hover:scale-[1.05]"
                loading="lazy"
                @error="currentImage = localFallback"
            >
            <span
                v-if="hasFeaturedBadge"
                class="absolute right-3 top-3 rounded-full bg-amber-400 px-3.5 py-1.5 text-xs font-black text-slate-900 shadow-lg"
            >
                إعلان مميز
            </span>
        </Link>

        <div class="space-y-3 p-4">
            <div class="text-3xl font-black tracking-tight text-blue-950">
                {{ formatCurrency(ad.price, ad.currency) }}
            </div>

            <h3 class="title-2line text-[1.05rem] font-extrabold text-slate-900">
                <Link :href="route('ads.show', ad.slug)" class="transition hover:text-blue-900">
                    {{ ad.title }}
                </Link>
            </h3>

            <div class="flex items-center justify-between gap-2 text-sm text-slate-600">
                <span class="truncate">{{ ad.city }}</span>
                <span>{{ formatRelativeTime(ad.published_at || ad.created_at) }}</span>
            </div>

            <div class="flex items-center justify-between gap-2 border-t border-slate-100 pt-2 text-xs font-semibold text-slate-500">
                <span>{{ formatNumber(ad.views_count) }} مشاهدة</span>
                <span class="rounded-lg bg-slate-100 px-2 py-1 text-slate-700">
                    {{ ad.category?.name || 'بدون تصنيف' }}
                </span>
            </div>

            <div v-if="showActions" class="flex flex-wrap gap-2 pt-1">
                <Link
                    :href="route('ads.edit', ad.slug)"
                    class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-900"
                >
                    تعديل
                </Link>
                <Link
                    :href="route('ads.destroy', ad.slug)"
                    method="delete"
                    as="button"
                    class="rounded-xl border border-rose-200 px-3 py-1.5 text-sm font-semibold text-rose-600 hover:bg-rose-50"
                >
                    حذف
                </Link>
            </div>
        </div>
    </article>
</template>
