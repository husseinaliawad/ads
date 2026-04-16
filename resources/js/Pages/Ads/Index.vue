<script setup>
import AdCard from '@/Components/AdCard.vue';
import AdCardSkeleton from '@/Components/AdCardSkeleton.vue';
import Pagination from '@/Components/Pagination.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    ads: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Array,
        default: () => [],
    },
    cities: {
        type: Array,
        default: () => [],
    },
    favoriteAdIds: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isGrid = ref(true);
const isFiltering = ref(false);

const defaultFilters = {
    search: '',
    category: '',
    city: '',
    condition: '',
    min_price: '',
    max_price: '',
    sort: 'newest',
    view: 'grid',
};

const localFilters = reactive({
    ...defaultFilters,
});

watch(
    () => props.filters,
    (filters) => {
        Object.assign(localFilters, defaultFilters, filters || {});
        isGrid.value = (localFilters.view || 'grid') !== 'list';
    },
    { immediate: true, deep: true },
);

const buildPayload = () => {
    const payload = {
        ...localFilters,
        view: isGrid.value ? 'grid' : 'list',
    };

    return Object.fromEntries(
        Object.entries(payload).filter(([, value]) => value !== '' && value !== null && value !== undefined),
    );
};

const applyFilters = () => {
    const payload = buildPayload();

    router.get(route('ads.index'), payload, {
        preserveState: false,
        replace: true,
        onStart: () => {
            isFiltering.value = true;
        },
        onFinish: () => {
            isFiltering.value = false;
        },
    });
};

const resetFilters = () => {
    Object.assign(localFilters, defaultFilters);
    isGrid.value = true;
    router.get(route('ads.index'), {}, {
        preserveState: false,
        replace: true,
    });
};

const isFavorite = (adId) => props.favoriteAdIds.includes(adId);
const hasResults = computed(() => (props.ads?.data?.length || 0) > 0);

const categoryNameBySlug = computed(() => {
    return Object.fromEntries(props.categories.map((category) => [category.slug, category.name]));
});

const activeFilters = computed(() => {
    const chips = [];

    if (localFilters.search) chips.push({ key: 'search', label: `بحث: ${localFilters.search}` });
    if (localFilters.category) chips.push({ key: 'category', label: `التصنيف: ${categoryNameBySlug.value[localFilters.category] || localFilters.category}` });
    if (localFilters.city) chips.push({ key: 'city', label: `المدينة: ${localFilters.city}` });
    if (localFilters.condition) chips.push({ key: 'condition', label: `الحالة: ${localFilters.condition === 'new' ? 'جديد' : 'مستعمل'}` });
    if (localFilters.min_price) chips.push({ key: 'min_price', label: `من: ${localFilters.min_price}` });
    if (localFilters.max_price) chips.push({ key: 'max_price', label: `إلى: ${localFilters.max_price}` });

    return chips;
});

const removeFilter = (key) => {
    localFilters[key] = '';
    applyFilters();
};
</script>

<template>
    <Head title="تصفح الإعلانات" />

    <MainLayout>
        <section class="grid gap-6 lg:grid-cols-[290px,1fr]">
            <aside class="card-surface sticky top-24 space-y-4 self-start">
                <h2 class="text-lg font-extrabold text-slate-900">فلترة متقدمة</h2>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">بحث</label>
                    <input v-model="localFilters.search" type="text" class="input-modern" placeholder="عنوان أو كلمة مفتاحية">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">التصنيف</label>
                    <select v-model="localFilters.category" class="input-modern">
                        <option value="">كل التصنيفات</option>
                        <option v-for="category in categories" :key="category.id" :value="category.slug">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">المدينة</label>
                    <select v-model="localFilters.city" class="input-modern">
                        <option value="">كل المدن</option>
                        <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-slate-600">الحالة</label>
                    <select v-model="localFilters.condition" class="input-modern">
                        <option value="">الكل</option>
                        <option value="new">جديد</option>
                        <option value="used">مستعمل</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-slate-600">من</label>
                        <input v-model="localFilters.min_price" type="number" min="0" class="input-modern" placeholder="0">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-slate-600">إلى</label>
                        <input v-model="localFilters.max_price" type="number" min="0" class="input-modern" placeholder="100000">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" class="btn-primary flex-1" @click="applyFilters">تطبيق</button>
                    <button type="button" class="btn-secondary flex-1" @click="resetFilters">إعادة</button>
                </div>
            </aside>

            <div class="space-y-4">
                <section class="card-surface flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-lg font-extrabold text-slate-900">نتائج البحث</h1>
                        <p class="text-sm text-slate-500">
                            عدد النتائج: {{ ads.total }}
                        </p>
                        <div v-if="activeFilters.length" class="mt-2 flex flex-wrap gap-2">
                            <button
                                v-for="chip in activeFilters"
                                :key="chip.key"
                                type="button"
                                class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:border-amber-300 hover:bg-amber-50"
                                @click="removeFilter(chip.key)"
                            >
                                {{ chip.label }} ×
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select v-model="localFilters.sort" class="input-modern min-w-[170px]" @change="applyFilters">
                            <option value="newest">الأحدث</option>
                            <option value="oldest">الأقدم</option>
                            <option value="price_low">السعر: الأقل أولًا</option>
                            <option value="price_high">السعر: الأعلى أولًا</option>
                            <option value="most_viewed">الأكثر مشاهدة</option>
                        </select>

                        <div class="flex overflow-hidden rounded-xl border border-slate-200">
                            <button
                                type="button"
                                class="px-3 py-2 text-sm font-semibold transition"
                                :class="isGrid ? 'bg-blue-900 text-white' : 'bg-white text-slate-600'"
                                @click="isGrid = true; applyFilters()"
                            >
                                شبكة
                            </button>
                            <button
                                type="button"
                                class="px-3 py-2 text-sm font-semibold transition"
                                :class="!isGrid ? 'bg-blue-900 text-white' : 'bg-white text-slate-600'"
                                @click="isGrid = false; applyFilters()"
                            >
                                قائمة
                            </button>
                        </div>
                    </div>
                </section>

                <section v-if="isFiltering" class="grid gap-4" :class="isGrid ? 'sm:grid-cols-2 xl:grid-cols-3' : 'grid-cols-1'">
                    <AdCardSkeleton v-for="item in 6" :key="item" />
                </section>

                <section v-else-if="hasResults" class="grid gap-4" :class="isGrid ? 'sm:grid-cols-2 xl:grid-cols-3' : 'grid-cols-1'">
                    <article v-for="ad in ads.data" :key="ad.id" class="relative">
                        <AdCard :ad="ad" />
                        <div v-if="page.props.auth.user" class="absolute left-4 top-4">
                            <Link
                                v-if="isFavorite(ad.id)"
                                :href="route('favorites.destroy', ad.slug)"
                                method="delete"
                                as="button"
                                class="rounded-full bg-rose-500 px-3 py-1 text-xs font-bold text-white shadow"
                            >
                                حفظت
                            </Link>
                            <Link
                                v-else
                                :href="route('favorites.store', ad.slug)"
                                method="post"
                                as="button"
                                class="rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-slate-700 shadow"
                            >
                                حفظ
                            </Link>
                        </div>
                    </article>
                </section>

                <section v-else class="card-surface text-center">
                    <p class="text-base font-bold text-slate-800">لا توجد نتائج مطابقة للفلاتر الحالية.</p>
                    <p class="mt-1 text-sm text-slate-500">جرّب إزالة بعض الفلاتر أو البحث بكلمات أبسط.</p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <button type="button" class="btn-secondary" @click="resetFilters">إعادة ضبط الفلاتر</button>
                        <Link :href="route('ads.create')" class="btn-accent">+ أضف إعلان جديد</Link>
                    </div>
                </section>

                <Pagination v-if="!isFiltering && hasResults" :links="ads.links" />
            </div>
        </section>
    </MainLayout>
</template>
