<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    city: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="إنشاء حساب" />

        <h1 class="mb-4 text-xl font-bold text-slate-900">إنشاء حساب جديد</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="الاسم الكامل" />

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
                    <InputLabel for="phone" value="رقم الهاتف (اختياري)" />

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
                    <InputLabel for="city" value="المدينة (اختياري)" />

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

            <div>
                <InputLabel for="password" value="كلمة المرور" />

                <TextInput
                    id="password"
                    type="password"
                    class="input-modern mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="تأكيد كلمة المرور"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="input-modern mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                >
                    لديك حساب بالفعل؟
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    تسجيل
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
