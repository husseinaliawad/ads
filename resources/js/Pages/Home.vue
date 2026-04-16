<script setup>
import AdCard from '@/Components/AdCard.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    featuredAds: {
        type: Array,
        default: () => [],
    },
    latestAds: {
        type: Array,
        default: () => [],
    },
    cities: {
        type: Array,
        default: () => [],
    },
});

const form = reactive({
    search: '',
    city: '',
    category: '',
});

const isSearching = ref(false);

const iconMap = {
    car: '🚗',
    building: '🏠',
    briefcase: '💼',
    cpu: '📱',
    sofa: '🛋️',
    wrench: '🛠️',
};

const mainCategories = computed(() => {
    const seen = new Set();

    return props.categories.filter((category) => {
        const key = String(category.name || '').toLowerCase();
        if (seen.has(key)) {
            return false;
        }
        seen.add(key);
        return true;
    }).slice(0, 8);
});

const submitSearch = () => {
    router.get(route('ads.index'), { ...form }, {
        preserveState: true,
        onStart: () => {
            isSearching.value = true;
        },
        onFinish: () => {
            isSearching.value = false;
        },
    });
};
</script>

<template>
    <Head title="الرئيسية" />

    <MainLayout>
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-l from-slate-900 via-blue-900/90 to-blue-800/85 px-6 py-12 text-white sm:px-10 lg:px-12">
            <div class="absolute -left-16 top-8 h-48 w-48 rounded-full bg-amber-300/20 blur-3xl"></div>
            <div class="absolute -bottom-20 right-0 h-56 w-56 rounded-full bg-blue-300/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(255,255,255,0.03),rgba(255,255,255,0.0))]"></div>

            <div class="relative z-10 max-w-5xl space-y-6">
                <span class="inline-flex rounded-full border border-amber-300/40 bg-amber-300/20 px-3 py-1 text-xs font-bold text-amber-100">
                    منصة إعلانات مبوبة حديثة
                </span>

                <h1 class="text-3xl font-black leading-tight sm:text-4xl lg:text-5xl">
                    بيع واشترِ بسهولة عبر سوق الإعلانات
                </h1>

                <p class="max-w-2xl text-sm text-slate-200 sm:text-base">
                    تجربة عربية سريعة وواضحة للبحث عن أفضل العروض، والتواصل المباشر مع البائعين بثقة.
                </p>

                <form class="soft-panel grid gap-3 p-4 text-slate-900 sm:grid-cols-12" @submit.prevent="submitSearch">
                    <div class="sm:col-span-12 lg:col-span-5">
                        <label class="mb-1 block text-sm font-bold text-slate-700">ماذا تبحث؟</label>
                        <input
                            v-model="form.search"
                            type="text"
                            placeholder="مثال: آيفون 14 أو شقة للإيجار"
                            class="input-modern h-12"
                        >
                    </div>

                    <div class="sm:col-span-6 lg:col-span-2">
                        <label class="mb-1 block text-sm font-bold text-slate-700">المدينة</label>
                        <select v-model="form.city" class="input-modern h-12">
                            <option value="">كل المدن</option>
                            <option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
                        </select>
                    </div>

                    <div class="sm:col-span-6 lg:col-span-2">
                        <label class="mb-1 block text-sm font-bold text-slate-700">التصنيف</label>
                        <select v-model="form.category" class="input-modern h-12">
                            <option value="">كل التصنيفات</option>
                            <option v-for="category in categories" :key="category.id" :value="category.slug">{{ category.name }}</option>
                        </select>
                    </div>

                    <div class="sm:col-span-12 lg:col-span-3 lg:self-end">
                        <button type="submit" class="btn-accent h-12 w-full text-base" :disabled="isSearching">
                            {{ isSearching ? 'جاري البحث...' : 'بحث' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="section-title">التصنيفات الرئيسية</h2>
                <Link :href="route('ads.index')" class="text-sm font-bold text-blue-900 hover:text-blue-700">تصفح كل الإعلانات</Link>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8">
                <a
                    v-for="category in mainCategories"
                    :key="category.id"
                    :href="route('ads.index', { category: category.slug })"
                    class="card-surface group flex flex-col items-center justify-center gap-2 px-3 py-4 text-center transition hover:border-blue-200"
                >
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-xl transition group-hover:scale-110 group-hover:bg-blue-100">
                        {{ iconMap[category.icon] || '📌' }}
                    </span>
                    <span class="text-sm font-bold text-slate-800">{{ category.name }}</span>
                </a>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="section-title">إعلانات مميزة</h2>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">مختارة بعناية</span>
            </div>

            <div v-if="featuredAds.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <AdCard v-for="ad in featuredAds" :key="ad.id" :ad="ad" />
            </div>
            <div v-else class="card-surface text-center text-slate-500">
                لا توجد إعلانات مميزة حاليًا.
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="section-title">أحدث الإعلانات</h2>
            <div v-if="latestAds.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <AdCard v-for="ad in latestAds" :key="ad.id" :ad="ad" />
            </div>
            <div v-else class="card-surface text-center text-slate-500">
                لا توجد نتائج حالية، جرّب تعديل البحث أو إضافة إعلان جديد.
            </div>
        </section>
    </MainLayout>
</template>
