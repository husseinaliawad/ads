<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="تأكيد البريد الإلكتروني" />

        <div class="mb-4 text-sm text-gray-600">
            شكرًا لتسجيلك. يرجى تأكيد بريدك الإلكتروني عبر الرابط الذي أرسلناه لك.
        </div>

        <div
            class="mb-4 text-sm font-medium text-green-600"
            v-if="verificationLinkSent"
        >
            تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    إعادة إرسال رابط التحقق
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                    >تسجيل الخروج</Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
