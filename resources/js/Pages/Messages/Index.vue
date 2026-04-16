<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { formatDate } from '@/utils/formatters';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => [],
    },
    activeAd: {
        type: Object,
        default: null,
    },
    activeUser: {
        type: Object,
        default: null,
    },
    messages: {
        type: Array,
        default: () => [],
    },
});

const canSend = computed(() => props.activeAd?.id && props.activeUser?.id);

const form = useForm({
    ad_id: props.activeAd?.id || '',
    receiver_id: props.activeUser?.id || '',
    body: '',
});

const send = () => {
    form
        .transform((data) => ({
            ...data,
            ad_id: props.activeAd?.id,
            receiver_id: props.activeUser?.id,
        }))
        .post(route('messages.store'), {
            preserveScroll: true,
            onSuccess: () => form.reset('body'),
        });
};
</script>

<template>
    <Head title="الرسائل" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-slate-900">صندوق الرسائل</h2>
        </template>

        <section class="grid gap-4 lg:grid-cols-[320px,1fr]">
            <aside class="card-surface max-h-[70vh] overflow-auto p-0">
                <h3 class="border-b border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">المحادثات</h3>
                <div v-if="conversations.length" class="divide-y divide-slate-100">
                    <Link
                        v-for="conversation in conversations"
                        :key="`${conversation.ad.id}-${conversation.partner.id}`"
                        :href="route('messages.index', { ad: conversation.ad.id, with: conversation.partner.id })"
                        class="block px-4 py-3 hover:bg-slate-50"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-slate-900">{{ conversation.partner.name }}</p>
                            <span v-if="conversation.unread_count" class="rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">
                                {{ conversation.unread_count }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ conversation.ad.title }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ conversation.latest_message.body }}</p>
                    </Link>
                </div>
                <div v-else class="p-4 text-sm text-slate-500">لا توجد محادثات بعد.</div>
            </aside>

            <article class="card-surface p-0">
                <header class="border-b border-slate-200 px-4 py-3">
                    <p class="font-bold text-slate-900">
                        {{ activeUser?.name || 'اختر محادثة' }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ activeAd?.title || 'لا يوجد إعلان محدد' }}
                    </p>
                </header>

                <div class="max-h-[50vh] space-y-2 overflow-auto p-4">
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        class="max-w-[80%] rounded-xl px-3 py-2 text-sm"
                        :class="message.sender_id === $page.props.auth.user.id
                            ? 'mr-auto bg-blue-600 text-white'
                            : 'ml-auto bg-slate-100 text-slate-700'"
                    >
                        <p>{{ message.body }}</p>
                        <span class="mt-1 block text-[11px]" :class="message.sender_id === $page.props.auth.user.id ? 'text-blue-100' : 'text-slate-400'">
                            {{ formatDate(message.created_at) }}
                        </span>
                    </div>
                    <div v-if="!messages.length" class="text-center text-sm text-slate-500">
                        لا توجد رسائل في هذه المحادثة.
                    </div>
                </div>

                <form v-if="canSend" class="border-t border-slate-200 p-3" @submit.prevent="send">
                    <div class="flex gap-2">
                        <input
                            v-model="form.body"
                            type="text"
                            class="input-modern"
                            placeholder="اكتب رسالة..."
                        >
                        <button type="submit" class="btn-primary" :disabled="form.processing">إرسال</button>
                    </div>
                </form>
            </article>
        </section>
    </AuthenticatedLayout>
</template>

