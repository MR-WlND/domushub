# Hướng dẫn & Cách hoạt động của Chức năng Lịch sử thao tác

Chức năng **Lịch sử thao tác (Activity Logs)** được xây dựng nhằm mục đích ghi vết lại toàn bộ các hành động quan trọng do Admin hoặc Ban quản lý thực hiện trên hệ thống. 

Chức năng này sử dụng thư viện mạnh mẽ **`spatie/laravel-activitylog`** để tự động xử lý và lưu trữ.

---

## 1. Cấu trúc lưu trữ dữ liệu

Mọi thao tác được lưu tập trung tại **1 bảng duy nhất** trong cơ sở dữ liệu:
- **Tên bảng:** `activity_log`
- **Các trường dữ liệu chính:**
  - `log_name`: Phân loại nhóm thao tác (ví dụ: `system` cho Hệ thống, `communication` cho Báo cáo/Cộng đồng, v.v.).
  - `description`: Mô tả chi tiết hành động (VD: "Cập nhật tài khoản Nguyễn Văn A").
  - `causer_type` & `causer_id`: Người thực hiện hành động (Thường là thông tin của Admin đang đăng nhập).
  - `subject_type` & `subject_id`: Đối tượng bị tác động (Ví dụ: ID của Căn hộ, ID của User bị sửa...).
  - `properties`: Chứa các dữ liệu thay đổi hoặc thông tin bổ sung (dạng JSON).
  - `created_at`: Thời gian thực hiện thao tác.

---

## 2. Cách thức hệ thống Ghi Log (Write)

Hệ thống sử dụng một Helper class trung gian có tên là `SystemLogger` (`app/Helpers/SystemLogger.php`) để làm cho việc gọi lệnh ghi log trở nên ngắn gọn và đồng nhất ở mọi nơi trong code.

**Ví dụ một đoạn code ghi log trong Controller:**
```php
use App\Helpers\SystemLogger;

// Sau khi tạo thành công một tài khoản:
SystemLogger::log(
    'system', // Tên nhóm log
    'Tạo tài khoản: ' . $user->name . ' (' . $user->role . ')', // Mô tả
    $user // Đối tượng bị tác động (Tùy chọn)
);
```

**Bên dưới lớp `SystemLogger`, thư viện Spatie sẽ làm việc như sau:**
```php
$activity = activity($logName)
    ->causedBy(Auth::user()) // Tự động lấy admin đang đăng nhập
    ->performedOn($subject)  // Liên kết với đối tượng bị tác động
    ->log($description);     // Lưu mô tả và ghi vào Database
```

Hiện tại, việc ghi log tự động đã được "cài cắm" vào các chức năng cốt lõi:
- **Quản lý Cư dân/Tài khoản:** Thêm, sửa, đổi quyền, khóa tài khoản, reset mật khẩu.
- **Quản lý Căn hộ:** Tạo mới, sửa, xóa, nhập Excel hàng loạt.
- **Quản lý Tiện ích:** Duyệt, hủy đặt lịch.
- **Quản lý Bãi Xe:** Gán lốt, thu hồi lốt, khóa/mở khóa xe, duyệt xe.
- **Quản lý Tài chính:** Tạo hóa đơn, tạo hàng loạt, ghi nhận thanh toán, hủy thanh toán.
- **Cộng đồng:** Ẩn/hiện bài viết, xóa bài, cấm đăng bài, cấm bình luận.

---

## 3. Cách thức hệ thống Hiển thị & Lọc Log (Read)

Giao diện xem lịch sử thao tác được đặt tại trang **Admin > Lịch sử thao tác** và được chia thành nhiều Tab nhỏ để dễ quản lý. Việc điều phối các Tab này do `ActivityLogController` đảm nhận.

- **Mỗi Tab sẽ query dữ liệu dựa theo bảng nghiệp vụ liên quan hoặc theo `log_name`:**
  - **Quản lý Bãi Xe:** Lấy từ bảng `vehicle_logs` (thông tin xe ra vào).
  - **Quản lý Ra Vào:** Lấy từ bảng `visitors` (khách đến thăm).
  - **Tài chính:** Lấy từ bảng `payments` (lịch sử thanh toán).
  - **Hệ thống & Phân quyền:** Lấy từ bảng `activity_log` với điều kiện `where('log_name', 'system')`.

- **Bộ lọc đa năng:** Người dùng có thể tìm kiếm theo từ khóa (mô tả), lọc theo khoảng thời gian (Từ ngày - Đến ngày) và lọc theo người thao tác (Causer).

---

## 4. Chức năng Xuất Excel (Export)

Khi người dùng nhấn nút **Xuất Excel** trên bất kỳ Tab nào, hệ thống thực hiện:
1. Gửi request kèm theo toàn bộ tham số bộ lọc hiện tại tới hàm `export` trong `ActivityLogController`.
2. Truyền dữ liệu sang Class `ActivityLogsExport` (`app/Exports/ActivityLogsExport.php`) thuộc thư viện **`maatwebsite/excel`**.
3. Class `ActivityLogsExport` có nhiệm vụ:
   - Dịch dữ liệu thành các cột tương ứng (`map()`).
   - Gắn tiêu đề cột (`headings()`).
   - Render file `.xlsx` và tự động tải xuống trình duyệt của người dùng.

---

## 5. Dọn dẹp Log định kỳ (Clean up)

Lịch sử thao tác nếu để quá lâu sẽ gây nặng cơ sở dữ liệu. Thư viện `spatie/laravel-activitylog` đã tích hợp sẵn chức năng tự động dọn dẹp các log cũ (mặc định là sau 365 ngày).

**Cấu hình thời gian giữ log:** 
Nằm tại file `config/activitylog.php`:
```php
'delete_records_older_than_days' => 365, // Số ngày lưu trữ log
```

**Cách chạy lệnh dọn dẹp:**
```bash
php artisan activitylog:clean
```
*(Có thể đưa lệnh này vào Cronjob / Schedule của Laravel để chạy tự động định kỳ, ví dụ mỗi ngày 1 lần).*
