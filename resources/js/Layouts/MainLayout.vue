<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage();
const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);

const user = computed(() => page.props.auth?.user);
const appName = computed(() => page.props.app?.name || 'سوق الإعلانات');
const isAdmin = computed(() => user.value?.role === 'admin');

const navLinks = computed(() => [
    { label: 'الرئيسية', href: route('home') },
    { label: 'الإعلانات', href: route('ads.index') },
]);

const userMenuLinks = computed(() => [
    { label: 'لوحتي', href: route('dashboard') },
    { label: 'إعلاناتي', href: route('ads.my') },
    { label: 'المفضلة', href: route('favorites.index') },
    { label: 'الرسائل', href: route('messages.index') },
]);

const addAdHref = computed(() => (user.value ? route('ads.create') : route('login')));

const userInitials = computed(() => {
    const name = String(user.value?.name || '').trim();
    if (!name) {
        return 'م';
    }

    const parts = name.split(' ').filter(Boolean).slice(0, 2);
    return parts.map((part) => part.charAt(0)).join('').toUpperCase();
});

const closeUserMenuOnOutside = (event) => {
    if (!userMenuRef.value) {
        return;
    }

    if (!userMenuRef.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeUserMenuOnOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeUserMenuOnOutside);
});
</script>

<template>
    <div class="min-h-screen bg-brand-background">
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="mx-auto grid max-w-7xl grid-cols-[auto,1fr,auto] items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <Link :href="route('home')" class="text-xl font-black text-blue-950 sm:text-2xl">
                        {{ appName }}
                    </Link>
                </div>

                <nav class="hidden items-center justify-center gap-2 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-xl px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 hover:text-blue-900"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <div class="hidden items-center gap-2 md:flex">
                    <Link :href="addAdHref" class="btn-accent">
                        + أضف إعلان
                    </Link>

                    <template v-if="user">
                        <div ref="userMenuRef" class="relative">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 py-1.5 text-sm font-bold text-slate-700 hover:bg-slate-50"
                                @click.stop="userMenuOpen = !userMenuOpen"
                            >
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-black text-blue-900">
                                    {{ userInitials }}
                                </span>
                                <span class="max-w-[120px] truncate">{{ user.name }}</span>
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="m6 9 6 6 6-6" />
                                </svg>
                            </button>

                            <div
                                v-show="userMenuOpen"
                                class="absolute left-0 top-full z-30 mt-2 w-48 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
                            >
                                <Link
                                    v-for="link in userMenuLinks"
                                    :key="link.href"
                                    :href="link.href"
                                    class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-blue-900"
                                    @click="userMenuOpen = false"
                                >
                                    {{ link.label }}
                                </Link>
                                <Link
                                    v-if="isAdmin"
                                    :href="route('admin.dashboard')"
                                    class="mt-1 block rounded-lg bg-blue-50 px-3 py-2 text-sm font-bold text-blue-900 hover:bg-blue-100"
                                    @click="userMenuOpen = false"
                                >
                                    لوحة الإدارة
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="mt-1 block w-full rounded-lg px-3 py-2 text-right text-sm font-semibold text-rose-700 hover:bg-rose-50"
                                >
                                    تسجيل الخروج
                                </Link>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <Link :href="route('login')" class="rounded-xl px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100">
                            تسجيل الدخول
                        </Link>
                    </template>
                </div>

                <button
                    type="button"
                    class="justify-self-end rounded-xl border border-slate-200 p-2 text-slate-600 md:hidden"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <span class="sr-only">القائمة</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>
            </div>

            <div v-if="mobileMenuOpen" class="border-t border-slate-200 bg-white p-3 md:hidden">
                <div class="flex flex-col gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="`m-${link.href}`"
                        :href="link.href"
                        class="rounded-xl px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-100"
                    >
                        {{ link.label }}
                    </Link>

                    <Link :href="addAdHref" class="btn-accent mt-2 w-full">
                        + أضف إعلان
                    </Link>

                    <template v-if="user">
                        <Link
                            v-for="link in userMenuLinks"
                            :key="`u-${link.href}`"
                            :href="link.href"
                            class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                        >
                            {{ link.label }}
                        </Link>
                        <Link
                            v-if="isAdmin"
                            :href="route('admin.dashboard')"
                            class="rounded-xl px-3 py-2 text-sm font-bold text-blue-900 hover:bg-blue-50"
                        >
                            لوحة الإدارة
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="rounded-xl px-3 py-2 text-right text-sm font-semibold text-rose-700 hover:bg-rose-50"
                        >
                            تسجيل الخروج
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            تسجيل الدخول
                        </Link>
                        <Link :href="route('register')" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            إنشاء حساب
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
            <FlashMessage />
            <slot />
        </main>

        <Link
            :href="addAdHref"
            class="fixed bottom-5 left-5 z-40 inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-3 text-sm font-extrabold text-slate-900 shadow-xl transition hover:bg-amber-400 md:hidden"
        >
            <span>+</span>
            <span>أضف إعلان</span>
        </Link>

        <footer class="border-t border-slate-200 bg-white py-5 text-center text-xs text-slate-500">
            جميع الحقوق محفوظة © {{ new Date().getFullYear() }} {{ appName }}
        </footer>
    </div>
</template>
