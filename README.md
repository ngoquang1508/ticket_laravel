# 🎟️ Hệ Thống Đặt Vé & Quản Lý Sự Kiện Trực Tuyến (Ticket Booking System)

Hệ thống đặt vé sự kiện trực tuyến toàn diện được xây dựng trên nền tảng **Laravel 11**, hỗ trợ đa dạng mô hình bán vé (chọn ghế ngồi theo sơ đồ trực quan với **Konva.js**, bán vé theo khu vực, hoặc vé tự do số lượng), tích hợp thanh toán qua cổng **VNPay Sandbox**, và quản lý vé điện tử với **Mã QR Check-in** tại sự kiện.

---

## 📑 Mục Lục
1. [Tính Năng Nổi Bật](#-tính-năng-nổi-bật)
2. [Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
3. [Yêu Cầu Môi Trường](#-yêu-cầu-môi-trường)
4. [Hướng Dẫn Cài Đặt Chi Tiết](#-hướng-dẫn-cài-đặt-chi-tiết)
5. [Hướng Dẫn Cấu Hình `.env`](#-hướng-dẫn-cấu-hình-env)
6. [Tài Khoản & Phân Quyền](#-tài-khoản--phân-quyền)
7. [Thông Tin Thẻ Thử Nghiệm VNPay Sandbox](#-thông-tin-thẻ-thử-nghiệm-vnpay-sandbox)
8. [Tác Vụ Nền & Tự Động Hóa (Queue & Schedule)](#-tác-vụ-nền--tự-động-hóa-queue--schedule)
9. [Cấu Trúc Thư Mục Dự Án](#-cấu-trúc-thư-mục-dự-án)

---

## 🌟 Tính Năng Nổi Bật

### 1. Phía Khách Hàng (Customer)
- **Khám phá sự kiện:**
  - Gợi ý tìm kiếm tức thì (*Live Search Suggestions*) theo tên sự kiện và từ khóa.
  - Lọc sự kiện đa tiêu chí: theo danh mục, thành phố/địa điểm, khoảng thời gian (*Cuối tuần này, Tuần tới, Tháng tới...*).
  - Trang chi tiết sự kiện: Thông tin ban tổ chức, lịch trình, mô tả chi tiết, hình ảnh và định vị địa điểm.
- **Trải nghiệm Đặt Vé Linh Hoạt (3 Hình thức):**
  - **Ghế ngồi cụ thể (`assigned_seat`)**: Sơ đồ ghế tương tác trực quan (sử dụng thư viện **Konva.js** HTML5 Canvas), hiển thị vị trí sân khấu, hàng ghế, số ghế và màu sắc trạng thái (*Trống, Đang chọn, Đang giữ, Đã bán*).
  - **Khu vực vé (`general_admission`)**: Chọn khu vực (Khu A, Khu B...) kèm số lượng vé.
  - **Vé tự do (`free_sale`)**: Hỗ trợ nhiều loại vé cùng lúc (*Vé VIP, Vé Thường, Vé Early Bird...*) với số lượng linh hoạt.
- **Cơ chế Giữ Ghế Tạm Thời (Hold Tickets):**
  - Hệ thống tự động khóa ghế/vé trong **10 phút** để khách hàng hoàn tất thanh toán, ngăn ngừa tình trạng tranh chấp cùng một vị trí.
- **Phương thức Thanh Toán Đa Dạng:**
  - **Cổng thanh toán trực tuyến VNPay**: Hỗ trợ thẻ ATM nội địa, tài khoản ngân hàng hoặc quét VNPAY-QR.
  - **Thanh toán trực tiếp**: Dành cho khách đặt vé trước thời điểm diễn ra sự kiện ít nhất 48 giờ.
- **Quản lý Vé Điện Tử Cá Nhân (`/my-tickets`):**
  - Lưu trữ danh sách vé đã mua, trạng thái thanh toán và thông tin từng vị trí ngồi.
  - Mỗi vé sở hữu một **Mã QR Check-in** bảo mật độc nhất.
- **Tài khoản & Bảo Mật:**
  - Đăng ký, đăng nhập và cập nhật hồ sơ cá nhân, đổi mật khẩu.
  - Khôi phục mật khẩu thông qua **mã OTP gửi về Email cá nhân**.

### 2. Phía Quản Trị Viên (Admin Portal - `/admin`)
- **Dashboard Thống Kê & Phân Tích:**
  - Tổng quan doanh thu, số lượng đơn hàng, số vé đã bán và lượng người dùng mới.
  - Biểu đồ tăng trưởng doanh thu theo ngày/tháng và danh sách sự kiện bán chạy nhất.
- **Quản Lý Sự Kiện Toàn Diện:**
  - Thêm, sửa, xóa sự kiện; hỗ trợ chọn hình thức bán vé, tải ảnh đại diện sự kiện.
  - Cấu hình danh mục và địa điểm tổ chức.
- **Trình Thiết Kế Sơ Đồ Ghế (Seat Map Builder):**
  - Giao diện kéo - thả trực quan trên HTML5 Canvas (Konva.js) để tạo khu vực, vẽ hàng ghế, đánh số thứ tự và định giá từng khu vực.
- **Quản Lý Loại Vé (Ticket Types):**
  - Thiết lập giá tiền, số lượng vé mở bán, kiểm soát số lượng vé đã xuất.
- **Quản Lý Đơn Hàng (Orders):**
  - Theo dõi danh sách đơn hàng theo trạng thái (*Đang giữ chỗ, Đang xử lý, Đã thanh toán, Đã hủy*).
  - Phê duyệt đơn hàng thanh toán trực tiếp hoặc hủy đơn kèm lý do cụ thể.
- **Check-in Vé Sự Kiện Bằng Mã QR (`/admin/check-in`):**
  - Tích hợp camera quét trực tiếp mã QR trên vé của khách hàng hoặc nhập mã vé thủ công.
  - Tự động kiểm tra tính hợp lệ của vé, ngăn chặn vé giả mạo hoặc vé đã check-in trước đó.
- **Hệ Thống Thông Báo (Notifications):**
  - Cập nhật chuông thông báo đơn hàng mới qua cơ chế AJAX Polling dành cho Ban quản trị.

---

## 🛠 Công Nghệ Sử Dụng

| Thành Phần | Công Nghệ / Thư Viện |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x, PHP 8.2+ |
| **Cơ sở dữ liệu** | MySQL 8.0+ |
| **Frontend UI** | Blade Templates, TailwindCSS v4, Vanilla JS |
| **Interactive Canvas** | Konva.js (Sơ đồ ghế ngồi & Trình vẽ sơ đồ) |
| **Cổng Thanh Toán** | VNPay Payment Gateway (Sandbox API v2.1.0) |
| **Mã QR Code** | chillerlan/php-qrcode |
| **Thông báo Toast** | Toastify JS |
| **Quản lý Thư viện** | Composer (PHP) & NPM (Node.js) |

---

## 💻 Yêu Cầu Môi Trường

Trước khi cài đặt, hãy đảm bảo máy tính/máy chủ của bạn đã cài đặt các phần mềm sau:
- **PHP** >= 8.2 (yêu cầu các extension: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `gd`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM** >= 9.x
- **MySQL** >= 8.0 hoặc **MariaDB** >= 10.4
- **Git**

---

## 🚀 Hướng Dẫn Cài Đặt Chi Tiết

### Bước 1: Clone Repository
```bash
git clone https://github.com/ngoquang1508/ticket_laravel.git
cd ticket_laravel
```

### Bước 2: Cài Đặt Các Phụ Thuộc (Dependencies)
Cài đặt thư viện PHP (Composer):
```bash
composer install
```

Cài đặt thư viện Javascript & CSS (NPM):
```bash
npm install
```

### Bước 3: Thiết Lập File Môi Trường `.env`
Sao chép cấu hình từ file mẫu `.env.example`:
```bash
cp .env.example .env
```
*(Trên hệ điều hành Windows PowerShell, sử dụng lệnh: `copy .env.example .env`)*

Tạo mã khóa ứng dụng (`APP_KEY`):
```bash
php artisan key:generate
```

### Bước 4: Cấu Hình Cơ Sở Dữ Liệu & Chạy Migrations
1. Mở file `.env` và cập nhật thông tin kết nối MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ticket_db
   DB_USERNAME=root
   DB_PASSWORD=your_mysql_password
   ```
2. Tạo cơ sở dữ liệu `ticket_db` trong MySQL.
3. Chạy lệnh di chuyển cấu trúc bảng và nạp dữ liệu mẫu:
   ```bash
   php artisan migrate --seed
   ```
   *(Tùy chọn: Nếu muốn nạp 50 sự kiện mẫu thực tế, chạy thêm: `php artisan db:seed --class=FiftyEventsSeeder`)*

### Bước 5: Tạo Liên Kết Lưu Trữ Tập Tin (Storage Link)
Để hiển thị được ảnh đại diện sự kiện đã tải lên:
```bash
php artisan storage:link
```

### Bước 6: Khởi Động Ứng Dụng

Mở các cửa sổ dòng lệnh riêng biệt để chạy ứng dụng:

**Terminal 1: Web Server Laravel**
```bash
php artisan serve
```
Ứng dụng sẽ hoạt động tại: [http://127.0.0.1:8000](http://127.0.0.1:8000)

**Terminal 2: Trình Biên Dịch Giao Diện (Vite)**
```bash
npm run dev
```

**Terminal 3: Tự Động Quét Hủy Đơn Hết Hạn Giữ Chỗ (Schedule)**
```bash
php artisan schedule:work
```

---

## ⚙️ Hướng Dẫn Cấu Hình `.env`

File `.env` đóng vai trò điều khiển toàn bộ cấu hình hệ thống:

### 1. Cấu Hình Gửi Mail OTP (Quên Mật Khẩu)
Hệ thống sử dụng SMTP Gmail để gửi mã OTP khi người dùng quên mật khẩu:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```
> **Lưu ý:** `MAIL_PASSWORD` là **Mật khẩu ứng dụng (App Password)** gồm 16 ký tự tạo trong phần *Tài khoản Google > Bảo mật > Xác minh 2 bước > Mật khẩu ứng dụng*, không phải mật khẩu đăng nhập Gmail thông thường.

### 2. Cấu Hình Cổng Thanh Toán VNPay Sandbox
Đăng ký hoặc sử dụng thông tin tài khoản thử nghiệm của VNPay:
```env
VNP_TMN_CODE=your_tmn_code
VNP_HASH_SECRET=your_hash_secret
VNP_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
```

---

## 👥 Tài Khoản & Phân Quyền

Hệ thống phân tách 2 nhóm quyền hạn (`role`):
1. **`admin`**: Toàn quyền truy cập Dashboard `/admin`, quản lý sự kiện, sơ đồ ghế, đơn hàng, người dùng và quét mã QR check-in vé.
2. **`customer`**: Người dùng mua vé thông thường.

### Cách Tạo Tài Khoản Quản Trị (Admin)
Mở cửa sổ dòng lệnh và chạy lệnh Artisan Tinker:
```bash
php artisan tinker
```
Sau đó dán đoạn mã tạo tài khoản quản trị:
```php
\App\Models\User::create([
    'name' => 'Quản Trị Viên',
    'email' => 'admin@gmail.com',
    'phone' => '0987654321',
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    'role' => 'admin',
    'is_active' => true,
]);
```
- **Email đăng nhập Admin:** `admin@gmail.com`
- **Mật khẩu:** `password123`
- **Đường dẫn trang quản trị:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)

---

## 💳 Thông Tin Thẻ Thử Nghiệm VNPay Sandbox

Khi thực hiện thanh toán qua cổng VNPay Sandbox, sử dụng thông tin thẻ thử nghiệm của ngân hàng NCB như sau:

| Thông Tin Thẻ | Giá Trị Mẫu |
| :--- | :--- |
| **Ngân hàng** | **NCB** |
| **Số thẻ** | `9704198526191432198` |
| **Tên chủ thẻ** | `NGUYEN VAN A` |
| **Ngày phát hành** | `07/15` |
| **Mật khẩu OTP** | `123456` |

---

## ⏳ Tác Vụ Nền & Tự Động Hóa (Queue & Schedule)

Hệ thống có cơ chế tự động giải phóng ghế sau 10 phút nếu khách hàng không hoàn tất thanh toán:

- Lệnh thực thi thủ công quét hủy đơn hết hạn:
  ```bash
  php artisan orders:cancel-expired
  ```
- Chạy tự động trong môi trường phát triển (Local Development):
  ```bash
  php artisan schedule:work
  ```
- Cấu hình Cron Job trên máy chủ Production (Linux Crontab):
  ```bash
  * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
  ```

---

## 📂 Cấu Trúc Thư Mục Dự Án

```
ticket/
├── app/
│   ├── Console/Commands/        # Lệnh Artisan tùy chỉnh (CancelExpiredOrders)
│   ├── Http/
│   │   ├── Controllers/         # Controller phía Khách hàng (Home, Booking, Auth...)
│   │   │   └── Admin/           # Controller Quản trị (Event, Order, SeatMap, CheckIn...)
│   │   └── Middleware/          # Middleware xác thực quyền Admin
│   ├── Models/                  # Eloquent Models (Event, Seat, SeatMap, Order, Ticket...)
│   └── Services/                # Lớp nghiệp vụ (BookingService, VNPayService...)
├── config/                      # Cấu hình hệ thống Laravel
├── database/
│   ├── migrations/              # Cấu trúc các bảng CSDL
│   └── seeders/                 # Dữ liệu khởi tạo ban đầu (FiftyEventsSeeder...)
├── public/                      # Tài nguyên công khai, file build Vite, index.php
├── resources/
│   ├── css/                     # CSS tùy chỉnh và Tailwind directives
│   ├── js/                      # Script frontend
│   └── views/                   # Giao diện Blade Views
│       ├── admin/               # Màn hình quản trị, sơ đồ ghế, check-in QR
│       ├── booking/             # Màn hình chọn vé, chọn ghế, giỏ hàng
│       └── layouts/             # Giao diện khung dùng chung
├── routes/
│   ├── web.php                  # Định tuyến ứng dụng Web và Admin Portal
│   └── console.php              # Lịch trình tự động hóa (Schedule commands)
├── .env.example                 # Mẫu cấu hình môi trường
└── README.md                    # Tài liệu hướng dẫn sử dụng dự án
```

---

## 📄 Bản Quyền & Giấy Phép
Dự án được phát triển phục vụ mục đích học tập và xây dựng giải pháp bán vé sự kiện chuyên nghiệp với mã nguồn mở theo giấy phép [MIT License](LICENSE).
