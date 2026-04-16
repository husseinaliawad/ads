<script setup>
defineProps({
    form: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    conditions: {
        type: Array,
        default: () => [],
    },
    submitLabel: {
        type: String,
        default: 'حفظ الإعلان',
    },
    existingImages: {
        type: Array,
        default: () => [],
    },
});
defineEmits(['submit']);

const setFiles = (event, form) => {
    form.images = Array.from(event.target.files || []);
};

const toggleImageRemoval = (imageId, form) => {
    if (!Array.isArray(form.removed_image_ids)) {
        form.removed_image_ids = [];
    }

    if (form.removed_image_ids.includes(imageId)) {
        form.removed_image_ids = form.removed_image_ids.filter((id) => id !== imageId);
    } else {
        form.removed_image_ids.push(imageId);
    }
};
</script>

<template>
    <form class="space-y-4" @submit.prevent="$emit('submit')">
        <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-600">العنوان</label>
            <input v-model="form.title" type="text" class="input-modern" placeholder="مثال: تويوتا كامري 2022">
            <p v-if="form.errors.title" class="text-xs text-rose-600">{{ form.errors.title }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">التصنيف</label>
                <select v-model="form.category_id" class="input-modern">
                    <option value="">اختر التصنيف</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <p v-if="form.errors.category_id" class="text-xs text-rose-600">{{ form.errors.category_id }}</p>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">الحالة</label>
                <select v-model="form.condition" class="input-modern">
                    <option value="">اختر الحالة</option>
                    <option v-for="condition in conditions" :key="condition.value" :value="condition.value">
                        {{ condition.label }}
                    </option>
                </select>
                <p v-if="form.errors.condition" class="text-xs text-rose-600">{{ form.errors.condition }}</p>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-600">الوصف</label>
            <textarea v-model="form.description" class="input-modern min-h-[160px]" placeholder="اكتب تفاصيل الإعلان بشكل واضح"></textarea>
            <p v-if="form.errors.description" class="text-xs text-rose-600">{{ form.errors.description }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">السعر</label>
                <input v-model="form.price" type="number" min="0" class="input-modern" placeholder="25000">
                <p v-if="form.errors.price" class="text-xs text-rose-600">{{ form.errors.price }}</p>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">المدينة</label>
                <input v-model="form.city" type="text" class="input-modern" placeholder="دمشق">
                <p v-if="form.errors.city" class="text-xs text-rose-600">{{ form.errors.city }}</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">المنطقة (اختياري)</label>
                <input v-model="form.area" type="text" class="input-modern" placeholder="المزة">
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600">رقم الاتصال</label>
                <input v-model="form.contact_phone" type="text" class="input-modern" placeholder="05xxxxxxxx">
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-600">رقم واتساب (اختياري)</label>
            <input v-model="form.whatsapp_number" type="text" class="input-modern" placeholder="05xxxxxxxx">
        </div>

        <div v-if="existingImages.length" class="space-y-2">
            <p class="text-sm font-semibold text-slate-600">الصور الحالية</p>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <label
                    v-for="image in existingImages"
                    :key="image.id"
                    class="relative overflow-hidden rounded-lg border border-slate-200"
                >
                    <img :src="image.url" :alt="image.alt_text || 'صورة'" class="h-24 w-full object-cover">
                    <div class="absolute inset-x-0 bottom-0 bg-black/60 px-2 py-1 text-xs text-white">
                        <input
                            type="checkbox"
                            :checked="form.removed_image_ids?.includes(image.id)"
                            class="ml-1"
                            @change="toggleImageRemoval(image.id, form)"
                        >
                        حذف
                    </div>
                </label>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-semibold text-slate-600">صور الإعلان</label>
            <input type="file" multiple accept="image/*" class="input-modern file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:font-semibold" @change="setFiles($event, form)">
            <p v-if="form.errors.images" class="text-xs text-rose-600">{{ form.errors.images }}</p>
            <p v-if="form.errors['images.*']" class="text-xs text-rose-600">{{ form.errors['images.*'] }}</p>
        </div>

        <button type="submit" class="btn-primary" :disabled="form.processing">
            {{ submitLabel }}
        </button>
    </form>
</template>
