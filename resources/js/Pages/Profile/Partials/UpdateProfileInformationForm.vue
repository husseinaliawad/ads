<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone || '',
    city: user.city || '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-slate-900">
                بيانات الحساب
            </h2>

            <p class="mt-1 text-sm text-slate-600">
                حدّث معلوماتك الأساسية وبيانات التواصل.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="الاسم" />

                <TextInput
                    id="name"
                    type="text"
                    class="input-modern mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="البريد الإلكتروني" />

                <TextInput
                    id="email"
                    type="email"
                    class="input-modern mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel for="phone" value="رقم الهاتف" />

                    <TextInput
                        id="phone"
                        type="text"
                        class="input-modern mt-1 block w-full"
                        v-model="form.phone"
                        autocomplete="tel"
                    />

                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div>
                    <InputLabel for="city" value="المدينة" />

                    <TextInput
                        id="city"
                        type="text"
                        class="input-modern mt-1 block w-full"
                        v-model="form.city"
                        autocomplete="address-level2"
                    />

                    <InputError class="mt-2" :message="form.errors.city" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    البريد الإلكتروني غير موثّق.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                    >
                        اضغط هنا لإعادة إرسال رابط التحقق.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    تم إرسال رابط تحقق جديد إلى بريدك.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">حفظ</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        تم الحفظ.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
