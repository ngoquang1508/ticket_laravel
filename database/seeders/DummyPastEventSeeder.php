<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyPastEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $location = Location::first();
        if (!$location) {
            $location = Location::create([
                'name' => 'Địa điểm mẫu 2',
                'address' => '456 Đường Cũ, Quận Cũ',
                'city' => 'TP.HCM'
            ]);
        }

        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Thể thao',
                'slug' => 'the-thao',
                'is_active' => true
            ]);
        }

        for ($i = 1; $i <= 20; $i++) {
            $name = "Sự kiện đã qua số $i " . Str::random(5);
            
            // Generate random date in the past
            $startsAt = now()->subDays(rand(10, 60));
            $endsAt = (clone $startsAt)->addDays(rand(1, 3));
            
            $event = Event::create([
                'location_id' => $location->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Mô tả cho sự kiện đã qua $i",
                'image' => null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
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
