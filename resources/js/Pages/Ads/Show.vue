<script setup>
import AdCard from '@/Components/AdCard.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { formatCurrency, formatDate } from '@/utils/formatters';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    ad: {
        type: Object,
        required: true,
    },
    similarAds: {
        type: Array,
        default: () => [],
    },
    isFavorite: {
        type: Boolean,
        default: false,
    },
    canMessageSeller: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const showReportForm = ref(false);
const fallbackImage = '/images/placeholder-ad.svg';
const selectedImage = ref(props.ad.images?.[0]?.url || props.ad.primary_image?.url || fallbackImage);

const messageForm = useForm({
    ad_id: props.ad.id,
    receiver_id: props.ad.user?.id,
    body: '',
});

const reportForm = useForm({
    ad_id: props.ad.id,
    reason: '',
    details: '',
});

const whatsappLink = computed(() => props.ad.whatsapp_number ? `https://wa.me/${props.ad.whatsapp_number}` : null);
const contactPhone = computed(() => props.ad.contact_phone || props.ad.user?.phone);

const sendMessage = () => {
    messageForm.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => messageForm.reset('body'),
    });
};

const submitReport = () => {
    reportForm.post(route('reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            reportForm.reset();
            showReportForm.value = false;
        },
    });
};
</script>

<template>
    <Head :title="ad.title" />

    <MainLayout>
        <section class="grid gap-6 lg:grid-cols-[1fr,320px]">
            <article class="space-y-5">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    <img
                        :src="selectedImage || fallbackImage"
                        :alt="ad.title"
                        class="h-[380px] w-full object-cover"
                        @error="selectedImage = fallbackImage"
                    >
                    <div v-if="ad.images?.length" class="grid grid-cols-4 gap-2 border-t border-slate-100 p-3">
                        <button
                            v-for="image in ad.images"
                            :key="image.id"
                            type="button"
                            class="overflow-hidden rounded-lg border"
                            :class="selectedImage === image.url ? 'border-blue-500' : 'border-slate-200'"
                            @click="selectedImage = image.url"
                        >
                            <img :src="image.url" :alt="image.alt_text || ad.title" class="h-20 w-full object-cover" @error="$event.target.src = fallbackImage">
                        </button>
                    </div>
                </div>

                <div class="card-surface space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h1 class="text-2xl font-extrabold text-slate-900">{{ ad.title }}</h1>
                        <span class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">
                            {{ ad.category?.name }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-500">
                        <span>المدينة: {{ ad.city }}</span>
                        <span>الحالة: {{ ad.condition === 'new' ? 'جديد' : 'مستعمل' }}</span>
                        <span>نُشر في {{ formatDate(ad.published_at || ad.created_at) }}</span>
                    </div>

                    <p class="text-xl font-extrabold text-blue-700">
                        {{ formatCurrency(ad.price, ad.currency) }}
                    </p>

                    <div class="rounded-xl bg-slate-50 p-4 leading-8 text-slate-700">
                        {{ ad.description }}
                    </div>
                </div>

                <div v-if="canMessageSeller" class="card-surface space-y-3">
                    <h3 class="text-lg font-bold text-slate-900">راسل البائع</h3>
                    <form class="space-y-3" @submit.prevent="sendMessage">
                        <textarea
                            v-model="messageForm.body"
                            class="input-modern min-h-[120px]"
                            placeholder="اكتب رسالتك هنا..."
                        ></textarea>
                        <button type="submit" class="btn-primary" :disabled="messageForm.processing">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>

                <section class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900">إعلانات مشابهة</h2>
                    <div v-if="similarAds.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <AdCard v-for="item in similarAds" :key="item.id" :ad="item" />
                    </div>
                    <div v-else class="card-surface text-slate-500">لا توجد إعلانات مشابهة حاليًا.</div>
                </section>
            </article>

            <aside class="space-y-4">
                <section class="card-surface space-y-3">
                    <h3 class="text-lg font-bold text-slate-900">معلومات البائع</h3>
                    <p class="text-sm text-slate-700">الاسم: {{ ad.user?.name }}</p>
                    <p class="text-sm text-slate-700">المدينة: {{ ad.user?.city || 'غير محدد' }}</p>

                    <a
                        v-if="contactPhone"
                        :href="`tel:${contactPhone}`"
                        class="btn-primary w-full"
                    >
                        اتصال مباشر
                    </a>

                    <a
                        v-if="whatsappLink"
                        :href="whatsappLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-accent w-full"
                    >
                        واتساب
                    </a>
                </section>

                <section v-if="page.props.auth.user" class="card-surface space-y-2">
                    <Link
                        v-if="!isFavorite"
                        :href="route('favorites.store', ad.slug)"
                        method="post"
                        as="button"
                        class="btn-primary w-full"
                    >
                        إضافة للمفضلة
                    </Link>
                    <Link
                        v-else
                        :href="route('favorites.destroy', ad.slug)"
                        method="delete"
                        as="button"
                        class="w-full rounded-xl border border-rose-200 px-4 py-2 font-semibold text-rose-700 hover:bg-rose-50"
                    >
                        إزالة من المفضلة
                    </Link>

                    <button
                        type="button"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50"
                        @click="showReportForm = !showReportForm"
                    >
                        الإبلاغ عن هذا الإعلان
                    </button>
                </section>

                <section v-if="showReportForm" class="card-surface space-y-3">
                    <h4 class="font-bold text-slate-900">إرسال بلاغ</h4>
                    <form class="space-y-3" @submit.prevent="submitReport">
                        <select v-model="reportForm.reason" class="input-modern" required>
                            <option value="">اختر السبب</option>
                            <option value="محتوى مكرر">محتوى مكرر</option>
                            <option value="بيانات مضللة">بيانات مضللة</option>
                            <option value="سلوك مخالف">سلوك مخالف</option>
                            <option value="سعر غير واقعي">سعر غير واقعي</option>
                        </select>
                        <textarea
                            v-model="reportForm.details"
                            class="input-modern min-h-[100px]"
                            placeholder="تفاصيل إضافية (اختياري)"
                        ></textarea>
                        <button type="submit" class="btn-primary w-full" :disabled="reportForm.processing">
                            إرسال البلاغ
                        </button>
                    </form>
                </section>
            </aside>
        </section>
    </MainLayout>
</template>
