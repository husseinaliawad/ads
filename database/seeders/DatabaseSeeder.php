<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Image;
use App\Models\Message;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
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

        $categoryDefinitions = [
            ['name' => 'سيارات', 'slug' => 'cars', 'icon' => 'car', 'sort_order' => 1],
            ['name' => 'عقارات', 'slug' => 'real-estate', 'icon' => 'building', 'sort_order' => 2],
            ['name' => 'وظائف', 'slug' => 'jobs', 'icon' => 'briefcase', 'sort_order' => 3],
            ['name' => 'إلكترونيات', 'slug' => 'electronics', 'icon' => 'cpu', 'sort_order' => 4],
            ['name' => 'أثاث منزلي', 'slug' => 'furniture', 'icon' => 'sofa', 'sort_order' => 5],
            ['name' => 'خدمات', 'slug' => 'services', 'icon' => 'wrench', 'sort_order' => 6],
        ];

        $categories = collect($categoryDefinitions)->map(function (array $item): Category {
            return Category::query()->updateOrCreate(
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
        });

        $users = User::factory(20)->create();

        $ads = Ad::factory(60)->make()->map(function (Ad $ad) use ($users, $categories, $admin) {
            $ad->user_id = $users->random()->id;
            $ad->category_id = $categories->random()->id;
            $ad->status = fake()->randomElement(['approved', 'approved', 'approved', 'pending', 'rejected']);
            $ad->published_at = $ad->status === 'approved' ? now()->subDays(fake()->numberBetween(0, 15)) : null;
            $ad->approved_at = $ad->status === 'approved' ? now() : null;
            $ad->approved_by = $ad->status === 'approved' ? $admin->id : null;

            return $ad;
        });

        $ads->each(fn (Ad $ad) => $ad->save());

        $samplePhotos = [
            'https://picsum.photos/seed/classified-01/1200/800',
            'https://picsum.photos/seed/classified-02/1200/800',
            'https://picsum.photos/seed/classified-03/1200/800',
            'https://picsum.photos/seed/classified-04/1200/800',
            'https://picsum.photos/seed/classified-05/1200/800',
            'https://picsum.photos/seed/classified-06/1200/800',
            'https://picsum.photos/seed/classified-07/1200/800',
            'https://picsum.photos/seed/classified-08/1200/800',
        ];

        $ads->each(function (Ad $ad) use ($samplePhotos): void {
            $imagesCount = fake()->numberBetween(1, 4);

            for ($i = 0; $i < $imagesCount; $i++) {
                Image::query()->create([
                    'ad_id' => $ad->id,
                    'path' => fake()->randomElement($samplePhotos),
                    'alt_text' => $ad->title,
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }
        });

        foreach ($users as $user) {
            $approvedIds = $ads->where('status', 'approved')->pluck('id');
            if ($approvedIds->isNotEmpty()) {
                $favoriteIds = $approvedIds->random(min($approvedIds->count(), fake()->numberBetween(1, 5)))->all();
                $user->favorites()->syncWithoutDetaching($favoriteIds);
            }
        }

        $ads->where('status', 'approved')->take(20)->each(function (Ad $ad) use ($users): void {
            $sender = $users->where('id', '!=', $ad->user_id)->random();

            Message::query()->create([
                'ad_id' => $ad->id,
                'sender_id' => $sender->id,
                'receiver_id' => $ad->user_id,
                'body' => fake()->sentence(12),
                'read_at' => fake()->boolean(50) ? now() : null,
            ]);
        });

        $ads->where('status', 'approved')->take(12)->each(function (Ad $ad) use ($users): void {
            $reporter = $users->where('id', '!=', $ad->user_id)->random();

            Report::query()->create([
                'ad_id' => $ad->id,
                'user_id' => $reporter->id,
                'reason' => fake()->randomElement(['محتوى مكرر', 'سعر غير واقعي', 'بيانات مضللة', 'سلوك مخالف']),
                'details' => fake()->sentence(10),
                'status' => fake()->randomElement(['pending', 'reviewed', 'resolved']),
            ]);
        });

        Setting::setValue('site_name', 'سوق الإعلانات');
        Setting::setValue('primary_color', '#1E3A8A');
        Setting::setValue('secondary_color', '#F59E0B');
        Setting::setValue('featured_ads_limit', '8', 'integer');
        Setting::setValue('site_logo', '');
    }
}
