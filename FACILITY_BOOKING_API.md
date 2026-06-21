# API - Đặt Lịch Sử Dụng Tiện Ích

## 📋 Danh Sách Endpoints

### 👤 CƯ DÂN (Middleware: `auth:sanctum`)

#### 1. Xem khung giờ còn trống
```
POST /api/facility-bookings/available-slots
Body: {
  "facility_id": 1,
  "booking_date": "2026-06-25"
}
Response: {
  "success": true,
  "facility": { ... },
  "available_slots": [
    {"start": "08:00", "end": "09:00", "label": "08:00 – 09:00"},
    ...
  ]
}
```

#### 2. Đặt lịch
```
POST /api/facility-bookings
Body: {
  "facility_id": 1,
  "booking_date": "2026-06-25",
  "start_time": "08:00",
  "end_time": "09:00",
  "number_of_people": 2
}
Response: {
  "success": true,
  "message": "Đặt lịch thành công",
  "booking": { ... }
}
```

#### 3. Xem lịch sử đặt chỗ
```
GET /api/facility-bookings?status=pending&page=1
Response: {
  "success": true,
  "data": [...],
  "pagination": { ... }
}
```

#### 4. Hủy lịch
```
PATCH /api/facility-bookings/{id}/cancel
Response: {
  "success": true,
  "message": "Hủy lịch thành công"
}
```

#### 5. Thanh toán phí sử dụng
```
POST /api/facility-bookings/{id}/pay
Body: {
  "payment_method": "vnpay|cash|bank_transfer|momo"
}
Response: {
  "success": true,
  "payment": {
    "amount": 50000,
    "formatted_amount": "50.000đ"
  }
}
```

#### 6. Check-in bằng QR
```
POST /api/facility-bookings/check-in
Body: {
  "qr_code": "QR_1_abc123_1234567890"
}
Response: {
  "success": true,
  "message": "Check-in thành công"
}
```

---

### 👨‍💼 BAN QUẢN LÝ (Middleware: `auth:sanctum`)

#### 1. Xem danh sách đặt lịch
```
GET /api/admin/facility-bookings?status=pending&facility_id=1&page=1
Response: {
  "success": true,
  "data": [...],
  "pagination": { ... }
}
```

#### 2. Duyệt lịch
```
PATCH /api/admin/facility-bookings/{id}/approve
Response: {
  "success": true,
  "message": "Duyệt lịch thành công",
  "booking": { ... }
}
```

#### 3. Hủy/Từ chối lịch
```
PATCH /api/admin/facility-bookings/{id}/cancel
Response: {
  "success": true,
  "message": "Hủy lịch thành công"
}
```

#### 4. Cập nhật trạng thái
```
PATCH /api/admin/facility-bookings/{id}/status
Body: {
  "status": "pending|approved|used|cancelled"
}
Response: {
  "success": true,
  "booking": { ... }
}
```

---

## 📊 Database Schema

```sql
facility_bookings:
- id
- facility_id (FK)
- user_id (FK)
- booking_date (date)
- start_time (time)
- end_time (time)
- number_of_people (int)
- status (enum: pending, approved, used, cancelled)
- qr_code (string, unique)
- checked_in_at (datetime, nullable)
- created_at, updated_at
```

---

## 🔄 Trạng Thái

| Status | Tiếng Việt | Mô Tả |
|--------|-----------|-------|
| pending | Chờ duyệt | Vừa tạo, chờ admin duyệt |
| approved | Đã duyệt | Admin duyệt, có thể thanh toán |
| used | Đã sử dụng | Check-in thành công |
| cancelled | Đã hủy | Đã hủy |

---

## 🛡️ Xác Thực

Tất cả API đều cần header:
```
Authorization: Bearer {token}
```
