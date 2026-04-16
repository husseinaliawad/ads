<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Image;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAdsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@classifieds.local'],
            [
                'name' => 'مدير المنصة',
                'phone' => '0500000000',
                'city' => 'دمشق',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $seller = User::query()->updateOrCreate(
            ['email' => 'seller@classifieds.local'],
            [
                'name' => 'أحمد البائع',
                'phone' => '0501112233',
                'city' => 'دمشق',
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        $categories = collect([
            ['name' => 'سيارات', 'slug' => 'cars', 'icon' => 'car', 'sort_order' => 1],
            ['name' => 'عقارات', 'slug' => 'real-estate', 'icon' => 'building', 'sort_order' => 2],
            ['name' => 'إلكترونيات', 'slug' => 'electronics', 'icon' => 'cpu', 'sort_order' => 3],
            ['name' => 'خدمات', 'slug' => 'services', 'icon' => 'wrench', 'sort_order' => 4],
        ])->mapWithKeys(function (array $item): array {
            $category = Category::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'icon' => $item['icon'],
                    'description' => 'تصنيف '.$item['name'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                    'parent_id' => null,
                ]
            );

            return [$item['slug'] => $category];
        });

        $ads = [
            [
                'slug' => 'toyota-camry-2022-damascus',
                'category_slug' => 'cars',
                'title' => 'تويوتا كامري 2022 فل كامل بحالة ممتازة',
                'description' => 'سيارة استخدام شخصي، صيانة دورية، بدون حوادث، جاهزة للنقل.',
                'price' => 92000,
                'city' => 'دمشق',
                'area' => 'المزة',
                'condition' => 'used',
                'is_featured' => true,
                'image' => 'https://picsum.photos/seed/demo-car/1200/800',
            ],
            [
                'slug' => 'apartment-rent-latakia-modern',
                'category_slug' => 'real-estate',
                'title' => 'شقة للإيجار السنوي في اللاذقية تشطيب جديد',
                'description' => 'غرفتان وصالة، مطبخ راكب، تهوية ممتازة، قرب الخدمات والمواصلات.',
                'price' => 4500,
                'city' => 'اللاذقية',
                'area' => 'مشروع الزراعة',
                'condition' => 'used',
                'is_featured' => true,
                'image' => 'https://picsum.photos/seed/demo-home/1200/800',
            ],
            [
                'slug' => 'iphone-14-pro-256gb-warranty',
                'category_slug' => 'electronics',
                'title' => 'آيفون 14 برو 256 جيجا مع كفالة',
                'description' => 'الجهاز بحالة ممتازة مع العلبة الأصلية، بطارية ممتازة، استخدام خفيف.',
                'price' => 3200,
                'city' => 'حلب',
                'area' => 'الجميلية',
                'condition' => 'used',
                'is_featured' => false,
                'image' => 'https://picsum.photos/seed/demo-phone/1200/800',
            ],
            [
                'slug' => 'moving-service-damascus-fast',
                'category_slug' => 'services',
                'title' => 'خدمة نقل عفش داخل دمشق مع فك وتركيب',
                'description' => 'فريق محترف مع تغليف كامل ومواعيد دقيقة وأسعار مناسبة.',
                'price' => 350,
                'city' => 'دمشق',
                'area' => 'كافة المناطق',
                'condition' => 'new',
                'is_featured' => false,
                'image' => 'https://picsum.photos/seed/demo-service/1200/800',
            ],
        ];

        foreach ($ads as $item) {
            $ad = Ad::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'user_id' => $seller->id,
                    'category_id' => $categories[$item['category_slug']]->id,
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'currency' => 'SAR',
                    'city' => $item['city'],
                    'area' => $item['area'],
                    'condition' => $item['condition'],
                    'status' => 'approved',
                    'is_featured' => $item['is_featured'],
                    'views_count' => 0,
                    'contact_phone' => $seller->phone,
                    'whatsapp_number' => $seller->phone,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                    'published_at' => now()->subDays(1),
                    'featured_until' => $item['is_featured'] ? now()->addDays(30) : null,
                ]
            );

            Image::query()->updateOrCreate(
                ['ad_id' => $ad->id, 'sort_order' => 0],
                [
                    'path' => $item['image'],
                    'alt_text' => $item['title'],
                    'is_primary' => true,
                ]
            );
        }

        Setting::setValue('site_name', 'سوق الإعلانات');
        Setting::setValue('primary_color', '#1E3A8A');
        Setting::setValue('secondary_color', '#F59E0B');
        Setting::setValue('featured_ads_limit', '8', 'integer');
    }
}
