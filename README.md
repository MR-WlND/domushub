# BÁO CÁO ĐỒ ÁN TỐT NGHIỆP

## PHÁT TRIỂN HỆ THỐNG QUẢN LÝ CHUNG CƯ — DOMUSHUB

---

| | |
|---|---|
| **Dự án** | DomusHub — Hệ thống quản lý chung cư toàn diện |
| **Repository** | https://github.com/MR-WlND/domushub |
| **Công nghệ nền** | Laravel 13 / PHP 8.3 / MySQL |
| **Thời gian thực hiện** | 2026 |

---

## MỤC LỤC

1. [Giới thiệu tổng quan](#1-giới-thiệu-tổng-quan)
2. [Mục tiêu và phạm vi hệ thống](#2-mục-tiêu-và-phạm-vi-hệ-thống)
3. [Công nghệ sử dụng](#3-công-nghệ-sử-dụng)
4. [Kiến trúc hệ thống](#4-kiến-trúc-hệ-thống)
5. [Phân tích chức năng](#5-phân-tích-chức-năng)
6. [Cơ sở dữ liệu](#6-cơ-sở-dữ-liệu)
7. [Phân quyền và vai trò người dùng](#7-phân-quyền-và-vai-trò-người-dùng)
8. [Tài khoản mẫu (Seed Data)](#8-tài-khoản-mẫu-seed-data)
9. [Hướng dẫn cài đặt và triển khai](#9-hướng-dẫn-cài-đặt-và-triển-khai)
10. [Cấu trúc thư mục dự án](#10-cấu-trúc-thư-mục-dự-án)

---

## 1. Giới thiệu tổng quan

**DomusHub** là một hệ thống quản lý chung cư (Apartment Management System) được xây dựng nhằm giải quyết các bài toán quản lý vận hành tòa nhà trong thực tế. Hệ thống hướng đến việc số hóa toàn bộ quy trình quản lý — từ hành chính cư dân, hóa đơn dịch vụ, quản lý phương tiện, đến giám sát khách thăm và xử lý sự cố kỹ thuật.

Ứng dụng được phát triển theo mô hình **Web Application** sử dụng kiến trúc **MVC (Model–View–Controller)** của framework Laravel, hỗ trợ đa vai trò người dùng và tích hợp các công nghệ hiện đại như **WebSocket thời gian thực**, **AI Chatbot (Google Gemini)** và **Docker containerization**.

---

## 2. Mục tiêu và phạm vi hệ thống

### 2.1. Mục tiêu

- Xây dựng nền tảng số hóa toàn diện cho ban quản lý chung cư, thay thế các quy trình thủ công dễ gây sai sót.
- Cung cấp giao diện trực quan cho từng nhóm người dùng (cư dân, lễ tân, bảo vệ, kỹ thuật viên...).
- Tự động hóa các nghiệp vụ lặp đi lặp lại như: tạo hóa đơn hàng tháng, gửi thông báo, tổng hợp báo cáo.
- Tích hợp công nghệ AI và realtime để nâng cao trải nghiệm người dùng.

### 2.2. Phạm vi

Hệ thống bao gồm các phân hệ chính:

| STT | Phân hệ | Mô tả |
|:---:|---|---|
| 1 | Quản lý hạ tầng | Tòa nhà, tầng, căn hộ, loại căn hộ |
| 2 | Quản lý cư dân | Hồ sơ cư dân, thành viên, hợp đồng, trạng thái cư trú |
| 3 | Đăng ký tạm trú / tạm vắng | Quy trình xét duyệt, cấp thẻ tạm trú |
| 4 | Hóa đơn & thanh toán | Tạo, phân phối, theo dõi và thanh toán hóa đơn |
| 5 | Phương tiện & bãi đỗ | Đăng ký xe, quản lý vị trí, nhật ký ra/vào |
| 6 | Khách thăm & bảo vệ | Check-in/out bằng QR code, khách vãng lai |
| 7 | Phiếu yêu cầu (Ticket) | Sửa chữa, khiếu nại, phân công kỹ thuật |
| 8 | Đặt chỗ tiện ích | Hồ bơi, gym, BBQ, phòng sinh hoạt... |
| 9 | Bảng tin cộng đồng | Đăng bài, bình luận, thông báo hệ thống |
| 10 | Quản lý vệ sinh | Lên lịch, phân công và báo cáo vệ sinh |
| 11 | Chỉ số điện / nước | Ghi nhận, duyệt và khiếu nại chỉ số |
| 12 | Bưu kiện | Nhận hộ và thông báo cho cư dân |
| 13 | Nhân sự & ca làm việc | Phòng ban, nhân viên, lịch ca, yêu cầu nhân sự |
| 14 | Chatbot AI | Hỗ trợ tự động qua Google Gemini |
| 15 | Nhật ký & báo cáo | Activity log, dashboard thống kê, xuất Excel |

---

## 3. Công nghệ sử dụng

### 3.1. Backend

| Thành phần | Phiên bản | Mục đích |
|---|---|---|
| **PHP** | ^8.3 | Ngôn ngữ lập trình chính |
| **Laravel** | ^13.8 | Framework MVC |
| **Laravel Reverb** | ^1.0 | WebSocket Server (thông báo thời gian thực) |
| **Laravel Sanctum** | ^4.3 | Xác thực API / phiên đăng nhập |
| **Google Gemini PHP** | ^2.0 | Tích hợp AI Chatbot |
| **Spatie ActivityLog** | ^5.0 | Ghi nhật ký hoạt động hệ thống |
| **Simple QR Code** | ^4.2 | Tạo mã QR cho phương tiện và khách thăm |
| **Laravel Tinker** | ^3.0 | Công cụ debug/REPL |

### 3.2. Frontend

| Thành phần | Phiên bản | Mục đích |
|---|---|---|
| **Blade Template** | Laravel built-in | Render giao diện phía server |
| **Tailwind CSS** | ^4.0 | Framework CSS utility-first |
| **Vite** | ^8.0 | Build tool & hot-reload |
| **Laravel Echo** | ^2.3.7 | Lắng nghe sự kiện WebSocket |
| **Pusher JS** | ^8.5.0 | Client cho WebSocket (kết nối Reverb) |
| **Lucide Static** | ^1.18.0 | Thư viện icon SVG |
| **Instrument Sans** | Bunny Fonts | Font chữ giao diện |

### 3.3. DevOps & Công cụ

| Thành phần | Mục đích |
|---|---|
| **Docker** (multi-stage) | Containerization, triển khai sản xuất |
| **shinsenter/laravel:php8.4** | Base image Docker tối ưu cho Laravel |
| **Nginx + PHP-FPM** | Web server trong môi trường Docker |
| **PHPUnit** | Kiểm thử tự động |
| **Laravel Pint** | Code style formatter |

---

## 4. Kiến trúc hệ thống

### 4.1. Mô hình tổng thể

Hệ thống áp dụng kiến trúc **Monolithic MVC** với phân tầng rõ ràng:

```
[Trình duyệt / Client]
        │
        ▼
[Nginx Web Server]
        │
        ▼
[Laravel Application — PHP 8.3]
   ├── Routes (web.php / api.php)
   ├── Middleware (Phân quyền theo role)
   ├── Controllers (Admin / Resident / Receptionist / Security / Cleaning)
   ├── Models (Eloquent ORM — 42 models)
   ├── Blade Views (Giao diện người dùng)
   └── Events / Notifications (Realtime qua Reverb)
        │
        ▼
[MySQL Database]        [Laravel Reverb — WebSocket]
```

### 4.2. Phân tầng Controller

Hệ thống tổ chức Controller theo từng vai trò người dùng, đảm bảo tính tách biệt rõ ràng:

```
app/Http/Controllers/
├── Admin/           (32 controllers — quản lý toàn hệ thống)
├── Resident/        (12 controllers — nghiệp vụ cư dân)
├── Receptionist/    (7 controllers  — nghiệp vụ lễ tân)
├── Security/        (5 controllers  — nghiệp vụ bảo vệ)
├── Cleaning/        (quản lý vệ sinh)
└── Api/             (REST API endpoints)
```

### 4.3. Middleware phân quyền

Mỗi nhóm tính năng được bảo vệ bởi một middleware riêng:

| Middleware | Role áp dụng |
|---|---|
| `AdminMiddleware` | `admin` |
| `ManagerMiddleware` | `manager` |
| `ResidentMiddleware` | `resident` |
| `ReceptionistMiddleware` | `receptionist` |
| `SecurityMiddleware` | `security` |
| `CleaningMiddleware` | `cleaning` |
| `TechnicianMiddleware` | `technician` |
| `StaffMiddleware` | `staff` |
| `CheckBannedUser` | Kiểm tra tài khoản bị cấm đăng bài/bình luận |

---

## 5. Phân tích chức năng

### 5.1. Phân hệ Admin

Nhóm Admin (bao gồm các role: `admin`, `manager`, `staff`, `technician`) có quyền truy cập **Admin Portal** với các chức năng:

- **Dashboard**: Thống kê tổng quan — số căn hộ, cư dân, hóa đơn chưa thanh toán, phiếu yêu cầu đang xử lý...
- **Quản lý hạ tầng**: CRUD tòa nhà (Block), tầng (Floor), căn hộ (Apartment) và loại căn hộ (ApartmentType).
- **Quản lý cư dân**: Xem danh sách, mời cư dân qua link, xem lịch sử cư trú, quản lý hợp đồng.
- **Hóa đơn**: Tạo hóa đơn thủ công hoặc hàng loạt, duyệt thanh toán, theo dõi công nợ, xuất PDF/Excel.
- **Phương tiện**: Duyệt đăng ký xe mới, quản lý bãi đỗ theo khu vực, xem nhật ký xe ra/vào.
- **Phiếu yêu cầu**: Duyệt, phân công kỹ thuật viên, theo dõi tiến độ, ghi nhận chi phí.
- **Tiện ích**: Quản lý danh mục tiện ích, duyệt đặt chỗ, cấu hình lịch hoạt động.
- **Nhân sự**: Quản lý phòng ban, nhân viên, ca làm việc và lịch phân công.
- **Thông báo**: Đăng thông báo hệ thống (popup hoặc thường), quản lý bài đăng vi phạm của cư dân.
- **Nhật ký**: Xem Activity Log toàn hệ thống — ai làm gì, khi nào.

### 5.2. Phân hệ Cư Dân (Resident)

- Xem hóa đơn và lịch sử thanh toán.
- Đặt lịch sử dụng tiện ích (hồ bơi, gym...) và theo dõi trạng thái đặt chỗ.
- Tạo phiếu yêu cầu sửa chữa hoặc khiếu nại, theo dõi tiến độ xử lý.
- Đăng ký phương tiện và quản lý danh sách xe cá nhân.
- Đăng ký khách thăm trước, xem trạng thái khách đã check-in/out.
- Đăng ký tạm trú / tạm vắng và theo dõi trạng thái duyệt.
- Đăng bài, bình luận, like trên bảng tin cộng đồng.
- Hỏi đáp với Chatbot AI 24/7.
- Nhận thông báo realtime (hóa đơn mới, yêu cầu được duyệt, khách đến...).

### 5.3. Phân hệ Lễ Tân (Receptionist)

- Ghi nhận và quản lý bưu kiện nhận hộ cư dân.
- Tiếp nhận và xử lý khách thăm tại quầy lễ tân.
- Xét duyệt đăng ký tạm trú / tạm vắng từ cư dân.
- Hỗ trợ cư dân về các vấn đề tiện ích.

### 5.4. Phân hệ Bảo Vệ (Security)

- Check-in / check-out phương tiện bằng quét mã QR.
- Check-in / check-out khách thăm bằng mã QR đặt trước.
- Ghi nhận khách vãng lai không có đăng ký trước.
- Xem nhật ký ra/vào trong ca trực.

### 5.5. Phân hệ Vệ Sinh (Cleaning)

- Xem lịch nhiệm vụ vệ sinh được phân công.
- Cập nhật trạng thái và báo cáo kết quả công việc.

---

## 6. Cơ sở dữ liệu

Hệ thống sử dụng **MySQL** với **146+ migration files**, được tổ chức theo nhóm chức năng:

### 6.1. Sơ đồ các nhóm bảng chính

| Nhóm | Các bảng chính |
|---|---|
| **Người dùng & Nhân sự** | `users`, `residents`, `apartment_members`, `staffs`, `departments`, `shifts`, `staff_schedules`, `shift_requirements` |
| **Hạ tầng** | `blocks`, `floors`, `apartments`, `apartment_types`, `apartment_invites`, `invitations` |
| **Tài chính** | `invoices`, `invoice_details`, `payments`, `service_prices` |
| **Phương tiện** | `vehicles`, `vehicle_logs`, `parking_lots` |
| **Khách thăm** | `visitors` |
| **Phiếu yêu cầu** | `tickets`, `ticket_progress`, `ticket_costs`, `ticket_assignments` |
| **Tiện ích** | `facilities`, `facility_bookings` |
| **Cộng đồng & Truyền thông** | `posts`, `post_images`, `post_reports`, `post_hides`, `comments`, `comment_reports`, `likes`, `announcements` |
| **Vệ sinh** | `cleaning_tasks`, `cleaning_reports` |
| **Chỉ số dịch vụ** | `utility_meters` |
| **Tạm trú / Tạm vắng** | `temporary_registrations` |
| **Bưu kiện** | `parcels` |
| **AI & Nhật ký** | `chatbot_messages`, `activity_log` |
| **Hệ thống** | `system_settings`, `notifications`, `personal_access_tokens`, `cache`, `jobs` |

### 6.2. Một số mối quan hệ quan trọng

- Một `User` có thể là `Resident` của nhiều `Apartment` (lưu lịch sử cư trú).
- Một `Apartment` thuộc một `Floor`, một `Floor` thuộc một `Block`.
- Một `Invoice` có nhiều `InvoiceDetail` và nhiều `Payment`.
- Một `Ticket` có nhiều `TicketProgress`, nhiều `TicketCost` và được phân công cho nhiều `User` qua `ticket_assignments`.
- Một `Vehicle` có nhiều `VehicleLog` ghi lại thời điểm ra/vào.

> 📐 Xem sơ đồ cơ sở dữ liệu đầy đủ: [`database.dbml`](./database.dbml)

---

## 7. Phân quyền và vai trò người dùng

Hệ thống áp dụng cơ chế **Role-Based Access Control (RBAC)** với 8 vai trò:

| Vai trò (`role`) | Tên hiển thị | Phạm vi truy cập |
|---|---|---|
| `admin` | Quản Trị Viên | Toàn quyền hệ thống, xem mọi báo cáo và cấu hình |
| `manager` | Trưởng Ban Quản Lý | Tương tự Admin, giới hạn một số chức năng cấu hình |
| `staff` | Nhân Viên Quản Lý | Nghiệp vụ hành chính hàng ngày (hóa đơn, cư dân...) |
| `technician` | Kỹ Thuật Viên | Xử lý phiếu yêu cầu được phân công |
| `cleaning` | Nhân Viên Vệ Sinh | Xem và cập nhật nhiệm vụ vệ sinh |
| `receptionist` | Lễ Tân | Khách thăm, bưu kiện, tạm trú/vắng |
| `security` | Bảo Vệ | Check-in/out xe và khách bằng QR |
| `resident` | Cư Dân | Nghiệp vụ cá nhân: hóa đơn, đặt chỗ, phiếu yêu cầu... |

**Cơ chế xác thực:**
- Session-based authentication cho Web Portal (mỗi portal có route `/login` riêng).
- Token-based authentication qua **Laravel Sanctum** cho API.
- Tài khoản bị khóa (`CheckBannedUser`) sẽ không thể đăng bài hoặc bình luận trong thời gian bị cấm.

---

## 8. Tài khoản mẫu (Seed Data)

Sau khi chạy `php artisan migrate --seed`, hệ thống tạo sẵn các tài khoản sau để kiểm thử:

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@example.com` | `password123` |
| Bảo Vệ (Cổng Chính) | `security.main@example.com` | `password123` |
| Bảo Vệ (Tầng Hầm) | `security.basement@example.com` | `password123` |
| Lễ Tân 1 | `letan01@domushub.vn` | `Chungcu@2026` |
| Lễ Tân 2 | `letan02@domushub.vn` | `Chungcu@2026` |
| Cư Dân (mẫu) | *(xem ResidentSeeder)* | `password123` |

**Đường dẫn đăng nhập theo vai trò:**

| Vai trò | URL đăng nhập |
|---|---|
| Admin / Manager / Staff / Technician | `/admin/login` |
| Cư Dân | `/login` |
| Lễ Tân | `/receptionist/login` |
| Bảo Vệ | `/security/login` |
| Nhân Viên Vệ Sinh | `/cleaning/login` |

---

## 9. Hướng dẫn cài đặt và triển khai

### 9.1. Yêu cầu môi trường

| Thành phần | Yêu cầu tối thiểu |
|---|---|
| PHP | >= 8.3 (với extension: `pdo`, `mbstring`, `gd`, `curl`, `zip`) |
| Composer | >= 2.x |
| Node.js | >= 20.x |
| NPM | >= 10.x |
| MySQL | >= 8.0 |

### 9.2. Cài đặt môi trường phát triển

**Bước 1 — Clone mã nguồn:**
```bash
git clone https://github.com/MR-WlND/domushub.git
cd domushub
```

**Bước 2 — Cài đặt nhanh (All-in-one):**
```bash
composer run setup
```
> Script này tự động thực hiện: `composer install` → tạo `.env` → `key:generate` → `migrate` → `npm install` → `npm run build`

Hoặc **cài đặt thủ công từng bước:**

```bash
# Cài đặt PHP dependencies
composer install

# Tạo file cấu hình môi trường
cp .env.example .env

# Sinh Application Key
php artisan key:generate

# Cài đặt Frontend dependencies và build
npm install
npm run build

# Chạy migration và seed dữ liệu mẫu
php artisan migrate --seed
```

**Bước 3 — Cấu hình file `.env`:**

```env
APP_NAME="DomusHub"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=domushub
DB_USERNAME=root
DB_PASSWORD=your_password

# WebSocket (Laravel Reverb)
REVERB_APP_ID=domushub
REVERB_APP_KEY=domushub-key
REVERB_APP_SECRET=domushub-secret
REVERB_HOST=localhost
REVERB_PORT=8080

# AI Chatbot (Google Gemini)
GEMINI_API_KEY=your_gemini_api_key
```

**Bước 4 — Khởi động môi trường phát triển:**

```bash
# Khởi động tất cả services cùng lúc (khuyến nghị)
composer run dev

# Hoặc chạy riêng từng service:
php artisan serve           # Web Server    → http://localhost:8000
npm run dev                 # Vite Dev      → hot-reload frontend
php artisan reverb:start    # WebSocket     → ws://localhost:8080
php artisan queue:work      # Queue Worker  → xử lý mail, notification
```

### 9.3. Triển khai bằng Docker

```bash
# Build image
docker build -t domushub:latest .

# Chạy container
docker run -d \
  -p 8080:80 \
  --env-file .env \
  --name domushub \
  domushub:latest
```

Truy cập ứng dụng tại: **http://localhost:8080**

> **Ghi chú cấu hình Docker:**
> Docker image được tối ưu hóa bộ nhớ RAM bằng cách giới hạn Nginx 1 worker process, PHP-FPM tối đa 4 worker processes và PHP memory limit 128MB, phù hợp với môi trường hosting chia sẻ.

### 9.4. Kiểm thử

```bash
# Chạy toàn bộ test suite
composer run test

# Hoặc
php artisan test

# Chạy một test cụ thể
php artisan test --filter=TenTestCase
```

---

## 10. Cấu trúc thư mục dự án

```
domushub/
│
├── app/                            # Mã nguồn chính của ứng dụng
│   ├── Console/                    # Scheduled commands
│   ├── Events/                     # Laravel Events (WebSocket triggers)
│   ├── Exports/                    # Xuất file Excel
│   ├── Helpers/                    # Hàm tiện ích toàn cục (PortalHelper.php)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # 32 controllers — Admin Portal
│   │   │   ├── Resident/           # 12 controllers — Resident Portal
│   │   │   ├── Receptionist/       # 7  controllers — Receptionist Portal
│   │   │   ├── Security/           # 5  controllers — Security Portal
│   │   │   ├── Cleaning/           # controllers — Cleaning Portal
│   │   │   └── Api/                # REST API controllers
│   │   └── Middleware/             # 10 middleware phân quyền
│   ├── Mail/                       # Mailable classes (gửi email)
│   ├── Models/                     # 42 Eloquent Models
│   ├── Notifications/              # Notification classes (realtime + email)
│   ├── Providers/                  # Service Providers
│   └── Rules/                      # Custom validation rules
│
├── database/
│   ├── factories/                  # Model factories (test data)
│   ├── migrations/                 # 146+ migration files
│   ├── seeders/                    # 24 seeder files
│   └── database.dbml               # Sơ đồ CSDL (DBML format)
│
├── resources/
│   ├── css/                        # CSS modules theo từng trang/layout
│   ├── js/                         # JavaScript modules theo từng trang
│   └── views/
│       ├── admin/                  # Giao diện Admin Portal
│       ├── resident/               # Giao diện Resident Portal
│       ├── receptionist/           # Giao diện Receptionist Portal
│       ├── security/               # Giao diện Security Portal
│       ├── cleaning/               # Giao diện Cleaning Portal
│       └── layouts/                # Master layouts theo từng portal
│
├── routes/
│   ├── web.php                     # ~61KB — Toàn bộ web routes
│   ├── api.php                     # API routes
│   └── channels.php                # WebSocket channels
│
├── scripts/
│   └── start-dev.ps1               # PowerShell script khởi động dev
│
├── tests/                          # PHPUnit test cases
├── Dockerfile                      # Multi-stage Docker build
├── vite.config.js                  # Cấu hình Vite build + Tailwind CSS v4
├── composer.json                   # PHP dependencies
├── package.json                    # Node.js dependencies
└── database.dbml                   # Sơ đồ cơ sở dữ liệu
```