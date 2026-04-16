<?php

namespace Database\Factories;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ad>
 */
class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $samples = [
            [
                'title' => 'تويوتا كامري 2022 فل كامل بحالة ممتازة',
                'description' => 'سيارة نظيفة جدًا، فحص كامل، سيرفس منتظم في الوكالة، بدون حوادث، جاهزة للنقل.',
            ],
            [
                'title' => 'شقة للإيجار السنوي في المزة تشطيب حديث',
                'description' => 'شقة غرفتين وصالة، مطبخ راكب، تهوية ممتازة، قريبة من الخدمات والمواصلات.',
            ],
            [
                'title' => 'مطلوب موظف مبيعات دوام كامل بخبرة سنة',
                'description' => 'فرصة عمل ضمن فريق نشيط، راتب ثابت مع عمولات، يفضل خبرة سابقة في المبيعات.',
            ],
            [
                'title' => 'آيفون 14 برو 256 جيجا ضمان ساري',
                'description' => 'الجهاز بحالة ممتازة، مع العلبة الأصلية، البطارية 89%، استخدام شخصي خفيف.',
            ],
            [
                'title' => 'طقم كنب مودرن 7 مقاعد مع طاولة وسط',
                'description' => 'كنب بحالة ممتازة، قماش مقاوم للبقع، اللون رمادي فاتح، مناسب لغرف الجلوس الواسعة.',
            ],
            [
                'title' => 'خدمة نقل عفش داخل المدينة مع الفك والتركيب',
                'description' => 'سيارات مجهزة وفريق محترف، تغليف كامل، مواعيد دقيقة، أسعار مناسبة.',
            ],
        ];

        $sample = fake()->randomElement($samples);
        $title = $sample['title'];

        $slugBase = Str::slug($title);
        if ($slugBase === '') {
            $slugBase = fake()->randomElement([
                'cars',
                'real-estate',
                'jobs',
                'electronics',
                'furniture',
                'services',
            ]);
        }

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => $slugBase.'-'.fake()->unique()->numberBetween(100, 9999),
            'description' => $sample['description'],
            'price' => fake()->randomFloat(2, 100, 750000),
            'currency' => 'SAR',
            'city' => fake()->randomElement(['دمشق', 'حلب', 'حمص', 'اللاذقية', 'حماة']),
            'area' => fake()->citySuffix(),
            'condition' => fake()->randomElement(['new', 'used']),
            'status' => 'approved',
            'is_featured' => fake()->boolean(20),
            'views_count' => fake()->numberBetween(0, 300),
            'contact_phone' => fake()->numerify('05########'),
            'whatsapp_number' => fake()->boolean(70) ? fake()->numerify('05########') : null,
            'approved_at' => now(),
            'published_at' => now()->subDays(fake()->numberBetween(0, 20)),
        ];
    }
}
