# سوق الإعلانات - Laravel 12 + Inertia Vue 3 (RTL)

منصة إعلانات مبوبة عربية احترافية (RTL) مشابهة لـ OLX/حراج مع لوحة تحكم أدمن كاملة.

## التقنيات

- Laravel 12
- Laravel Breeze + Inertia + Vue 3
- Tailwind CSS
- MySQL (افتراضي في `.env`)
- سياسات Laravel Policies + Gate للأمان
- API Resources + RESTful routes

## الميزات المنفذة

- الصفحة الرئيسية: بحث كبير + تصنيفات + إعلانات مميزة + أحدث الإعلانات + المدن
- صفحة الإعلانات: فلترة متقدمة + ترتيب + Grid/List + Pagination
- تفاصيل الإعلان: معرض صور + بيانات البائع + واتساب/اتصال + إعلانات مشابهة + بلاغ
- إضافة/تعديل/حذف إعلان مع رفع صور متعددة
- المفضلة
- رسائل بين المستخدمين
- حساب المستخدم (لوحة شخصية + إعلاناتي)
- لوحة أدمن:
  - إدارة الإعلانات (قبول/رفض/تمييز/حذف)
  - إدارة المستخدمين (تفعيل/حظر/تغيير دور)
  - إدارة التصنيفات (CRUD)
  - إدارة البلاغات
  - إعدادات عامة (اسم الموقع، الألوان، عدد الإعلانات المميزة)
- API:
  - `GET /api/ads`
  - `GET /api/ads/{ad}`
  - `GET /api/messages` (Sanctum)
  - `POST /api/messages` (Sanctum)

## بنية قاعدة البيانات

الجداول الأساسية:

- `users`
- `categories`
- `ads`
- `images`
- `messages`
- `favorites`
- `reports`
- `settings`

## التشغيل المحلي

1. تثبيت الاعتمادات:

```bash
composer install
```

2. ضبط البيئة:

```bash
cp .env.example .env
php artisan key:generate
```

3. أنشئ قاعدة MySQL باسم `classifieds` (أو عدّل `.env`).

4. ترحيل الجداول + بيانات تجريبية:

```bash
php artisan migrate --seed
php artisan storage:link
```

5. شغّل الخادم:

```bash
php artisan serve
```

6. شغّل الواجهة:

```bash
npm install
npm run dev
```

## حسابات جاهزة بعد Seeder

- Admin:
  - `admin@classifieds.local`
  - `password`

## ملاحظات

- تم اختبار الـ backend بـ `php artisan test` وجميع الاختبارات الأساسية مرت.
- في بيئتك الحالية كانت حزمة `npm` معطلة على مستوى النظام. بعد إصلاح Node/npm شغّل:
  - `npm install`
  - `npm run dev` أو `npm run build`

