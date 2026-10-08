<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $location = Location::first();
        if (!$location) {
            $location = Location::create([
                'name' => 'Địa điểm mẫu',
                'address' => '123 Đường Mẫu, Quận Mẫu',
                'city' => 'Hà Nội'
            ]);
        }

        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Âm nhạc',
                'slug' => 'am-nhac',
                'is_active' => true
            ]);
        }

        for ($i = 1; $i <= 20; $i++) {
            $name = "Sự kiện âm nhạc số $i " . Str::random(5);
            $event = Event::create([
                'location_id' => $location->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Mô tả cho sự kiện $i",
                'image' => null,
                'starts_at' => now()->addDays(rand(5, 30)),
                'ends_at' => now()->addDays(rand(31, 35)),
                'sale_mode' => collect(['assigned_seat', 'general_admission', 'free_sale'])->random(),
                'is_published' => true,
            ]);

            // Add categories
            $event->categories()->attach($category->id);

            // Add Ticket Type if not free
            if ($event->sale_mode !== 'free_sale') {
                $event->ticketTypes()->create([
                    'name' => 'Vé thường',
                    'price' => rand(1, 10) * 100000,
                    'quantity' => 100
                ]);
            }
        }
    }
}
