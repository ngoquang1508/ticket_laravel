<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FiftyEventsSeeder extends Seeder
{
    /**
     * Tạo 50 sự kiện phân bổ đều về thời gian, địa điểm, danh mục và có hình ảnh đầy đủ
     */
    public function run(): void
    {
        // 1. Danh sách các hình ảnh sự kiện đã có trong storage/app/public/events
        $availableImages = [
            'events/Dc78Q8WqxGxdbgNbyOKJQPKYxYyCcxOAR3WNtIM6.jpg',
            'events/ESvECxMAStZfTCdsb4VBqal3T2CzwSxnFQAokGVB.jpg',
            'events/Ebb2upU7xi5o00Omf48HflFUjDODKxsqJpJWVp5t.jpg',
            'events/Lc2QvaejsgtWmQ0MULthOuWjJkC0AQgnzqB3DI2P.jpg',
            'events/QVQ8ezVZwyIYWH4Gxf47WW30rYNJNLy1dVMXVhGs.jpg',
            'events/VVuzpHcAr8xJPnkunze4dXC0wePtPFBi3yRXlCVz.jpg',
            'events/i2R1tUOMR7p1zqq0PdOwa0jvojB0AWPY35PRWnqO.jpg',
        ];

        // 2. Lấy danh mục và địa điểm
        $categories = Category::all()->keyBy('slug');
        $locations = Location::all();

        $hanoiLocations = $locations->filter(fn($l) => str_contains($l->address, 'Hà Nội'))->values();
        $hcmLocations = $locations->filter(fn($l) => str_contains($l->address, 'Hồ Chí Minh'))->values();
        $danangLocations = $locations->filter(fn($l) => str_contains($l->address, 'Đà Nẵng'))->values();
        $otherLocations = $locations->filter(fn($l) => !str_contains($l->address, 'Hà Nội') && !str_contains($l->address, 'Hồ Chí Minh') && !str_contains($l->address, 'Đà Nẵng'))->values();

        // 3. Người dùng mẫu để gán đơn hàng (order)
        $defaultUser = User::first() ?? User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demouser@ticketsystem.com',
        ]);

        // 4. Định nghĩa danh sách 50 sự kiện thực tế phong phú
        $eventsData = [
            // --- NHÓM 1: CUỐI TUẦN NÀY (17/10 - 19/10/2026) ---
            [
                'name' => 'Viewing Party Chung Kết Tổng Valorant Champions 2026',
                'category' => 'the-thao',
                'region' => 'hanoi',
                'days_offset' => 1, // 18/10/2026
                'price' => 259000,
                'featured' => false,
            ],
            [
                'name' => '[LEMLAB] Mini Workshop Vẽ Ly Gốm Sứ Thỏa Sức Sáng Tạo',
                'category' => 'hoi-thao-workshop',
                'region' => 'hcm',
                'days_offset' => 0, // 17/10/2026
                'price' => 99000,
                'featured' => false,
            ],
            [
                'name' => '[LEMLAB] Workshop Trải Nghiệm Làm Gốm Thủ Công Dành Cho Trẻ Em',
                'category' => 'hoi-thao-workshop',
                'region' => 'hcm',
                'days_offset' => 0, // 17/10/2026
                'price' => 350000,
                'featured' => false,
            ],
            [
                'name' => 'City Sightseeing Saigon: Du Ngoạn Tuyến Xe Buýt 2 Tầng',
                'category' => 'tham-quan-trai-nghiem',
                'region' => 'hcm',
                'days_offset' => 0, // 17/10/2026
                'price' => 105000,
                'featured' => false,
            ],
            [
                'name' => 'FLOWER 1969s: Workshop Nước Hoa Khô & Nến Thơm Thiên Nhiên',
                'category' => 'hoi-thao-workshop',
                'region' => 'hcm',
                'days_offset' => 0, // 17/10/2026
                'price' => 279000,
                'featured' => false,
            ],
            [
                'name' => 'LÁ NHÀ GARDEN: Workshop Sáng Tạo Tranh Rêu Độc Bản',
                'category' => 'hoi-thao-workshop',
                'region' => 'hcm',
                'days_offset' => 1, // 18/10/2026
                'price' => 370000,
                'featured' => false,
            ],
            [
                'name' => 'Trải Nghiệm Bay Dù Lượn Ngắm Thung Lũng Mường Hoa Sa Pa',
                'category' => 'tham-quan-trai-nghiem',
                'region' => 'other',
                'days_offset' => 1,
                'price' => 2190000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Acoustic Dưới Ánh Trăng Bên Bờ Sông Hàn',
                'category' => 'am-nhac',
                'region' => 'danang',
                'days_offset' => 1,
                'price' => 150000,
                'featured' => false,
            ],

            // --- NHÓM 2: CÁC NGÀY TRONG THÁNG NÀY (20/10 - 31/10/2026) ---
            [
                'name' => 'Live Concert SKYNOTE Special Day - Quốc Thiên',
                'category' => 'am-nhac',
                'region' => 'hanoi',
                'days_offset' => 5,
                'price' => 850000,
                'featured' => true,
            ],
            [
                'name' => 'Mắt Nhắm Mắt Mở Concert - GERDNANG & Khách Mời Đặc Biệt',
                'category' => 'am-nhac',
                'region' => 'hanoi',
                'days_offset' => 8,
                'price' => 650000,
                'featured' => true,
            ],
            [
                'name' => 'Bùi Công Nam: The Story 2nd Live Tour in Đà Lạt 2026',
                'category' => 've-ban-lai',
                'region' => 'other',
                'days_offset' => 14,
                'price' => 550000,
                'featured' => false,
            ],
            [
                'name' => 'Giữa Một Vạn Tour (Mở Rộng) - Phùng Khánh Linh Chapter 5',
                'category' => 've-ban-lai',
                'region' => 'hcm',
                'days_offset' => 6,
                'price' => 450000,
                'featured' => false,
            ],
            [
                'name' => 'Tình Hà Say Hi Concert - Live Music Night',
                'category' => 've-ban-lai',
                'region' => 'hcm',
                'days_offset' => 7,
                'price' => 500000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Indie Hẹn Ước Cùng Chillies & Vũ',
                'category' => 've-ban-lai',
                'region' => 'hanoi',
                'days_offset' => 10,
                'price' => 400000,
                'featured' => false,
            ],
            [
                'name' => 'Hài Độc Thoại Sài Gòn Tếu: Tiếng Cười Xuyên Đêm Mùa Thu',
                'category' => 'giai-tri',
                'region' => 'hcm',
                'days_offset' => 7,
                'price' => 200000,
                'featured' => false,
            ],
            [
                'name' => 'Vở Kịch Kinh Điển: Hồn Trương Ba, Da Hàng Thịt',
                'category' => 'nghe-thuat',
                'region' => 'hanoi',
                'days_offset' => 9,
                'price' => 250000,
                'featured' => false,
            ],
            [
                'name' => 'Triển Lãm Tranh Đương Đại: Sắc Màu Di Sản Á Đông',
                'category' => 'nghe-thuat',
                'region' => 'hcm',
                'days_offset' => 11,
                'price' => 120000,
                'featured' => false,
            ],
            [
                'name' => 'Vietnam AI & Tech Summit 2026: Kỷ Nguyên Trí Tuệ Nhân Tạo',
                'category' => 'hoi-thao-workshop',
                'region' => 'hanoi',
                'days_offset' => 12,
                'price' => 500000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Cổ Điển Tchaikovsky & Chopin Masterpieces',
                'category' => 'nghe-thuat',
                'region' => 'hanoi',
                'days_offset' => 13,
                'price' => 350000,
                'featured' => false,
            ],
            [
                'name' => 'Giải Pickleball Vô Địch Quốc Gia Mở Rộng 2026',
                'category' => 'the-thao',
                'region' => 'danang',
                'days_offset' => 12,
                'price' => 180000,
                'featured' => false,
            ],
            [
                'name' => 'Festival Văn Hóa Ẩm Thực Đường Phố Đông Nam Á 2026',
                'category' => 'giai-tri',
                'region' => 'hcm',
                'days_offset' => 13,
                'price' => 80000,
                'featured' => false,
            ],

            // --- NHÓM 3: THÁNG 11 VÀ THÁNG 12/2026 TRỞ ĐI ---
            [
                'name' => 'Anh Trai Say Hi Live Concert 2026 - Đêm Nhạc Bùng Nổ',
                'category' => 'am-nhac',
                'region' => 'hcm',
                'days_offset' => 25,
                'price' => 1200000,
                'featured' => true,
            ],
            [
                'name' => 'Lễ Hội Pháo Hoa Quốc Tế Đà Nẵng (DIFF 2026)',
                'category' => 'giai-tri',
                'region' => 'danang',
                'days_offset' => 35,
                'price' => 800000,
                'featured' => true,
            ],
            [
                'name' => 'Ravolution Music Festival 2026: Đỉnh Cao Nhạc Điện Tử EDM',
                'category' => 'am-nhac',
                'region' => 'hcm',
                'days_offset' => 40,
                'price' => 950000,
                'featured' => true,
            ],
            [
                'name' => 'Giải Chạy Đêm VnExpress Marathon Ho Chi Minh City Midnight',
                'category' => 'the-thao',
                'region' => 'hcm',
                'days_offset' => 30,
                'price' => 650000,
                'featured' => true,
            ],
            [
                'name' => 'Hanoi Heritage Marathon: Sải Bước Qua Miền Di Sản Ba Đình',
                'category' => 'the-thao',
                'region' => 'hanoi',
                'days_offset' => 28,
                'price' => 700000,
                'featured' => false,
            ],
            [
                'name' => 'Show Diễn Ký Ức Hội An: Tinh Hoa Đất Quảng Triệu Người Say Mê',
                'category' => 'nghe-thuat',
                'region' => 'danang',
                'days_offset' => 20,
                'price' => 600000,
                'featured' => false,
            ],
            [
                'name' => 'Workshop Pha Chế Cà Phê Thủ Công Specialty Coffee V60',
                'category' => 'hoi-thao-workshop',
                'region' => 'hanoi',
                'days_offset' => 18,
                'price' => 250000,
                'featured' => false,
            ],
            [
                'name' => 'Chương Trình Ảo Thuật Quốc Tế: Vén Màn Bí Mật Kỳ Thú',
                'category' => 'giai-tri',
                'region' => 'hcm',
                'days_offset' => 22,
                'price' => 300000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Trịnh Công Sơn: Biển Nhớ & Lời Ru Cố Đô',
                'category' => 'am-nhac',
                'region' => 'other',
                'days_offset' => 24,
                'price' => 400000,
                'featured' => false,
            ],
            [
                'name' => 'Giải Quần Vợt Tranh Cúp Vô Địch Sài Gòn Open 2026',
                'category' => 'the-thao',
                'region' => 'hcm',
                'days_offset' => 26,
                'price' => 220000,
                'featured' => false,
            ],
            [
                'name' => 'Workshop Làm Bánh Tiramisu Ý & Nghệ Thuật Thưởng Trà Chiều',
                'category' => 'hoi-thao-workshop',
                'region' => 'hcm',
                'days_offset' => 19,
                'price' => 320000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Rock Việt Bùng Cháy: Bức Tường, Ngũ Cung & Microwave',
                'category' => 'am-nhac',
                'region' => 'hanoi',
                'days_offset' => 32,
                'price' => 500000,
                'featured' => false,
            ],
            [
                'name' => 'Lễ Hội Khinh Khí Cầu Quốc Tế Bên Bờ Sông Hàn Rực Rỡ',
                'category' => 'giai-tri',
                'region' => 'danang',
                'days_offset' => 45,
                'price' => 150000,
                'featured' => false,
            ],
            [
                'name' => 'Tour Du Thuyền 5 Sao Ngắm Hoàng Hôn Sông Sài Gòn Lãng Mạn',
                'category' => 'tham-quan-trai-nghiem',
                'region' => 'hcm',
                'days_offset' => 16,
                'price' => 890000,
                'featured' => false,
            ],
            [
                'name' => 'Triển Lãm Đa Giác Quan Van Gogh & Monet Immersive Art',
                'category' => 'nghe-thuat',
                'region' => 'hcm',
                'days_offset' => 34,
                'price' => 280000,
                'featured' => false,
            ],
            [
                'name' => 'Vở Múa Đương Đại Sương Sớm & Hương Lúa Đồng Quê',
                'category' => 'nghe-thuat',
                'region' => 'hanoi',
                'days_offset' => 38,
                'price' => 300000,
                'featured' => false,
            ],
            [
                'name' => 'Hội Thảo Chiến Lược Marketing & Thương Hiệu Thời Đại Kỹ Thuật Số',
                'category' => 'hoi-thao-workshop',
                'region' => 'hanoi',
                'days_offset' => 29,
                'price' => 450000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Giao Hưởng Mùa Đông Dưới Mái Vòm Nhà Hát Lớn',
                'category' => 'nghe-thuat',
                'region' => 'hanoi',
                'days_offset' => 42,
                'price' => 600000,
                'featured' => false,
            ],
            [
                'name' => 'Giải Bơi Biển Quốc Tế Oceanman Danang Vietnam 2026',
                'category' => 'the-thao',
                'region' => 'danang',
                'days_offset' => 48,
                'price' => 850000,
                'featured' => false,
            ],
            [
                'name' => 'Xiếc Nghệ Thuật Quốc Tế: Huyền Thoại Làng Việt',
                'category' => 'giai-tri',
                'region' => 'hcm',
                'days_offset' => 36,
                'price' => 350000,
                'featured' => false,
            ],
            [
                'name' => 'Workshop Thêu Tay Nghệ Thuật & Ứng Dụng Họa Tiết Truyền Thống',
                'category' => 'hoi-thao-workshop',
                'region' => 'hanoi',
                'days_offset' => 27,
                'price' => 260000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Trữ Tình Bolero: Giai Điệu Khúc Ca Vàng',
                'category' => 'am-nhac',
                'region' => 'other',
                'days_offset' => 44,
                'price' => 350000,
                'featured' => false,
            ],
            [
                'name' => 'Lễ Hội Rượu Vang & Văn Hóa Ẩm Thực Châu Âu Tại Sài Gòn',
                'category' => 'giai-tri',
                'region' => 'hcm',
                'days_offset' => 46,
                'price' => 400000,
                'featured' => false,
            ],
            [
                'name' => 'Hội Thảo Khởi Nghiệp Công Nghệ Mở Rộng Quy Mô Toàn Cầu',
                'category' => 'hoi-thao-workshop',
                'region' => 'hcm',
                'days_offset' => 50,
                'price' => 350000,
                'featured' => false,
            ],
            [
                'name' => 'Giải Đua Thuyền Buồm Quốc Tế Mở Rộng Vịnh Nha Trang',
                'category' => 'the-thao',
                'region' => 'other',
                'days_offset' => 52,
                'price' => 500000,
                'featured' => false,
            ],
            [
                'name' => 'Đại Nhạc Hội Countdown Festival 2027 Chào Đón Năm Mới',
                'category' => 'am-nhac',
                'region' => 'hanoi',
                'days_offset' => 75,
                'price' => 1500000,
                'featured' => true,
            ],
            [
                'name' => 'Concert Mùa Đông Yêu Thương - Những Giọng Ca Vàng Tình Khúc',
                'category' => 'am-nhac',
                'region' => 'other',
                'days_offset' => 55,
                'price' => 450000,
                'featured' => false,
            ],
            [
                'name' => 'Giải Bóng Rổ Chuyên Nghiệp Mở Rộng Vietnam Pro Basketball',
                'category' => 'the-thao',
                'region' => 'hcm',
                'days_offset' => 23,
                'price' => 180000,
                'featured' => false,
            ],
            [
                'name' => 'Đêm Nhạc Acoustic Hoàng Hôn Trên Đỉnh Đồi Thông Đà Lạt',
                'category' => 'am-nhac',
                'region' => 'other',
                'days_offset' => 21,
                'price' => 290000,
                'featured' => false,
            ],
        ];

        // 5. Tiến hành tạo 50 sự kiện vào CSDL
        $now = Carbon::now();

        foreach ($eventsData as $index => $item) {
            // Chọn địa điểm phù hợp khu vực
            $locationPool = match($item['region']) {
                'hanoi' => $hanoiLocations->isNotEmpty() ? $hanoiLocations : $locations,
                'hcm' => $hcmLocations->isNotEmpty() ? $hcmLocations : $locations,
                'danang' => $danangLocations->isNotEmpty() ? $danangLocations : $locations,
                default => $otherLocations->isNotEmpty() ? $otherLocations : $locations,
            };
            $selectedLocation = $locationPool->random();

            // Chọn ảnh xoay vòng
            $imagePath = $availableImages[$index % count($availableImages)];

            // Thời gian bắt đầu và kết thúc
            $startsAt = $now->copy()->addDays($item['days_offset'])->setHour(rand(18, 20))->setMinute(collect([0, 30])->random())->setSecond(0);
            $endsAt = $startsAt->copy()->addHours(rand(3, 5));

            // Tạo slug duy nhất
            $baseSlug = Str::slug($item['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Tạo Event
            $event = Event::create([
                'location_id' => $selectedLocation->id,
                'name' => $item['name'],
                'slug' => $slug,
                'description' => "Trải nghiệm sự kiện đặc sắc: {$item['name']} được tổ chức hoành tráng tại {$selectedLocation->name}. Hệ thống âm thanh, ánh sáng hiện đại cùng nhiều phần quà hấp dẫn.",
                'image' => $imagePath,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'sale_mode' => collect(['general_admission', 'assigned_seat'])->random(),
                'is_featured' => $item['featured'],
                'is_published' => true,
            ]);

            // Gắn danh mục
            $catSlug = $item['category'];
            if (isset($categories[$catSlug])) {
                $event->categories()->syncWithoutDetaching([$categories[$catSlug]->id]);
            } else {
                $firstCat = $categories->first();
                if ($firstCat) {
                    $event->categories()->syncWithoutDetaching([$firstCat->id]);
                }
            }

            // Tạo các hạng vé cho sự kiện
            $basePrice = $item['price'];
            $ticketQuantity = rand(150, 600);
            $soldQuantity = rand(20, 120);

            // Vé thường
            $ticketStandard = TicketType::create([
                'event_id' => $event->id,
                'name' => 'Vé Tiêu Chuẩn',
                'description' => 'Vé vào cổng tham gia trọn vẹn chương trình',
                'price' => $basePrice,
                'quantity' => $ticketQuantity,
                'sold_quantity' => $soldQuantity,
                'is_active' => true,
            ]);

            // Thêm vé VIP cho sự kiện nổi bật hoặc giá cao
            if ($basePrice >= 400000 || $item['featured']) {
                TicketType::create([
                    'event_id' => $event->id,
                    'name' => 'Vé VIP - Trải Nghiệm Đẳng Cấp',
                    'description' => 'Khu vực gần sân khấu, tặng nước ngọt và quà lưu niệm',
                    'price' => $basePrice * 1.5,
                    'quantity' => rand(50, 150),
                    'sold_quantity' => rand(10, 40),
                    'is_active' => true,
                ]);
            }

            // Tạo các đơn hàng mẫu (orders) để đưa sự kiện lên top xu hướng
            $orderCount = rand(1, 8);
            if ($item['featured']) {
                $orderCount = rand(8, 20); // Sự kiện nổi bật có nhiều đơn hàng để vào top xu hướng
            }

            for ($k = 0; $k < $orderCount; $k++) {
                $orderCode = 'ORD' . strtoupper(Str::random(8));
                while (Order::where('order_code', $orderCode)->exists()) {
                    $orderCode = 'ORD' . strtoupper(Str::random(8));
                }

                $order = Order::create([
                    'order_code' => $orderCode,
                    'user_id' => $defaultUser->id,
                    'event_id' => $event->id,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'payment_method' => 'vnpay',
                    'total_amount' => $basePrice * 2,
                    'customer_name' => 'Khách hàng ' . ($k + 1),
                    'customer_email' => 'customer' . rand(100, 999) . '@example.com',
                    'customer_phone' => '0987' . rand(100000, 999999),
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $ticketStandard->id,
                    'quantity' => 2,
                    'price' => $basePrice,
                    'total' => $basePrice * 2,
                    'status' => 'completed',
                ]);
            }
        }
    }
}
