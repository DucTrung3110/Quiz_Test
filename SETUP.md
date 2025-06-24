# HƯỚNG DẪN CÀI ĐẶT QUIZ TEST SYSTEM

## Yêu cầu hệ thống
- PHP 7.4 hoặc cao hơn
- Web server (Apache/Nginx) 
- SQLite support (có sẵn trong PHP)

## Cách 1: Sử dụng XAMPP (Khuyến nghị cho Windows)

### Bước 1: Cài đặt XAMPP
1. Download XAMPP từ: https://www.apachefriends.org/
2. Cài đặt với cấu hình mặc định
3. Khởi động XAMPP Control Panel

### Bước 2: Copy dự án
1. Copy thư mục dự án vào `C:\xampp\htdocs\`
2. Đổi tên thành `quiz-system` (hoặc tên bạn muốn)

### Bước 3: Khởi động
1. Trong XAMPP Control Panel, nhấn "Start" cho Apache
2. Mở trình duyệt và truy cập: `http://localhost/quiz-system`

## Cách 2: Sử dụng PHP Built-in Server

### Bước 1: Cài đặt PHP
- Windows: Download từ https://windows.php.net/download/
- macOS: `brew install php`
- Ubuntu: `sudo apt install php`

### Bước 2: Chạy server
```bash
cd /path/to/quiz-system
php -S localhost:8000
```

Truy cập: `http://localhost:8000`

## Cấu hình Database

Dự án sử dụng SQLite, database sẽ tự động được tạo khi chạy lần đầu.

File database: `quiz_system.db` sẽ được tạo trong thư mục gốc.

## Tài khoản mặc định

### Tài khoản Admin:
- Username: `admin`
- Password: `admin123`

### Tài khoản Demo User:
- Username: `demo`  
- Password: `demo123`

## Cấu trúc thư mục

```
quiz-system/
├── assets/
│   ├── css/
│   └── js/
├── backend/           # Admin panel
├── config/           # Cấu hình database
├── controllers/      # MVC Controllers
├── frontend/         # Giao diện người dùng
├── models/          # MVC Models
├── views/           # Templates
├── index.php        # Entry point
└── quiz_system.db   # SQLite database (tự tạo)
```

## Tính năng chính

### Cho người dùng:
- Đăng ký/Đăng nhập
- Làm bài quiz với timer
- Xem kết quả chi tiết
- Lịch sử bài làm với thống kê

### Cho admin:
- Quản lý quiz (CRUD)
- Quản lý câu hỏi
- Xem kết quả của học viên
- Dashboard thống kê

## Xử lý sự cố

### Lỗi không tạo được database:
```bash
# Đảm bảo thư mục có quyền ghi
chmod 755 /path/to/quiz-system
```

### Lỗi 404 Not Found:
- Kiểm tra đường dẫn trong URL
- Đảm bảo Apache đã được khởi động

### Lỗi PHP:
- Kiểm tra PHP version >= 7.4
- Đảm bảo SQLite extension được bật

## Tùy chỉnh

### Thay đổi thông tin site:
Chỉnh sửa file `config/config.php`:
```php
define('SITE_NAME', 'Tên site của bạn');
define('SITE_URL', 'http://domain-cua-ban.com');
```

### Thêm quiz mới:
1. Đăng nhập admin panel
2. Vào "Manage Quizzes"
3. Nhấn "Create New Quiz"

## Liên hệ hỗ trợ

Nếu gặp vấn đề trong quá trình cài đặt, vui lòng kiểm tra:
1. PHP version
2. Web server status
3. File permissions
4. Error logs trong web server