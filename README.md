# Quiz Test System - PHP Assignment

Hệ thống Quiz Test được xây dựng bằng PHP OOP theo mô hình MVC với SQLite database.

## Tính năng chính

### 🎓 Dành cho Học viên
- Đăng ký/Đăng nhập tài khoản
- Làm bài quiz trắc nghiệm với timer
- Xem kết quả chi tiết sau khi hoàn thành
- Theo dõi lịch sử bài làm và tiến độ học tập
- Thống kê cá nhân với biểu đồ

### 👨‍💼 Dành cho Quản trị viên
- Quản lý quiz (Tạo, sửa, xóa)
- Quản lý câu hỏi cho từng quiz
- Xem kết quả và thống kê của học viên
- Dashboard tổng quan hệ thống

## Công nghệ sử dụng

- **Backend**: PHP 8+ với OOP và MVC pattern
- **Database**: SQLite (không cần cài đặt MySQL)
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: jQuery, Chart.js
- **Icons**: Font Awesome

## Cài đặt nhanh

### 1. Download dự án
```bash
# Option 1: Download từ Replit
# Nhấn menu 3 chấm > Download as zip

# Option 2: Clone từ Git (nếu có)
git clone [repository-url]
```

### 2. Setup với XAMPP
```bash
# Copy thư mục vào htdocs
C:\xampp\htdocs\quiz-system\

# Khởi động Apache trong XAMPP Control Panel
# Truy cập: http://localhost/quiz-system
```

### 3. Setup với PHP built-in server
```bash
cd quiz-system
php -S localhost:8000
# Truy cập: http://localhost:8000
```

## Tài khoản mặc định

**Admin Panel:**
- Username: `admin`
- Password: `admin123`

**Demo User:**
- Username: `demo`
- Password: `demo123`

## Cấu trúc dự án

```
quiz-system/
├── assets/              # CSS, JS, images
├── backend/             # Admin panel pages
├── config/              # Database configuration
├── controllers/         # MVC Controllers
├── frontend/            # User interface pages
├── models/             # MVC Models  
├── views/              # Layout templates
├── index.php           # Entry point
├── quiz_system.db      # SQLite database (auto-created)
├── SETUP.md            # Detailed setup guide
└── README.md           # This file
```

## Workflow sử dụng

### Cho học viên:
1. Truy cập trang chủ → Đăng ký/Đăng nhập
2. Chọn quiz muốn làm từ danh sách
3. Hoàn thành bài quiz trong thời gian quy định
4. Xem kết quả chi tiết
5. Theo dõi tiến độ trong "Lịch sử bài làm"

### Cho admin:
1. Truy cập Admin Panel
2. Tạo quiz mới và thêm câu hỏi
3. Theo dõi kết quả học viên
4. Quản lý nội dung hệ thống

## Đặc điểm kỹ thuật

- **MVC Architecture**: Tách biệt logic, data và presentation
- **OOP Design**: Sử dụng classes và inheritance
- **Security**: Password hashing, SQL injection prevention
- **Responsive**: Tương thích mobile và desktop
- **Auto-save**: Lưu tiến độ làm bài tự động
- **Charts**: Biểu đồ tiến độ học tập

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## Yêu cầu hệ thống

- PHP 7.4+
- SQLite extension (built-in)
- Web server (Apache/Nginx)
- Modern web browser

## License

Dự án này được phát triển cho mục đích học tập.

---

📧 **Liên hệ hỗ trợ**: Xem file SETUP.md để biết chi tiết cách khắc phục sự cố.