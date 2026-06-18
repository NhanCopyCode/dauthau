# Hệ Thống Crawler Đấu Thầu

Hệ thống quản lý và thu thập dữ liệu đấu thầu được xây dựng trên nền tảng Laravel.

---

# Yêu Cầu Hệ Thống

Trước khi chạy dự án, hãy đảm bảo máy của bạn đã cài đặt:

* PHP 8.2 trở lên
* Composer
* MySQL 8 trở lên
* Git
* Node.js 

---

# Hướng Dẫn Cài Đặt

## 1. Clone Source Code

```bash
git clone <repository-url>
cd <project-folder>
```

---

## 2. Cài Đặt Thư Viện

Cài đặt các package PHP:

```bash
composer install
```

Nếu dự án có sử dụng frontend build:

```bash
npm install
```

---

## 3. Cấu Hình File Môi Trường

Tạo file `.env`:

```bash
cp .env.example .env
```

Sau đó cập nhật thông tin kết nối cơ sở dữ liệu trong file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ten_database
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4. Tạo Application Key

```bash
php artisan key:generate
```

---

## 5. Tối Ưu Hóa Hệ Thống Laravel

Xóa cache cũ (nếu có):

```bash
php artisan optimize:clear
```

Tạo lại cache:

```bash
php artisan optimize
```

---

## 6. Chạy Migration

Tạo các bảng dữ liệu:

```bash
php artisan migrate
```

---

## 7. Khởi Tạo Dữ Liệu Mặc Định

```bash
php artisan db:seed
```

---

## 8. Khởi Động Queue Worker

Hệ thống crawler sử dụng Laravel Queue để xử lý các tác vụ nền.

Chạy file:

```bash
start-queue.bat
```


## 9. Khởi Động Website

```bash
php artisan serve
```

Sau khi chạy thành công, truy cập:

```text
http://127.0.0.1:8000
```

---

# Đăng Nhập Hệ Thống

Truy cập:

```text
http://127.0.0.1:8000/login
```

Tài khoản quản trị mặc định:

**Email**

```text
admin@dauthau.gov.vn
```

**Mật khẩu**

```text
admin@123
```

---

# Hướng Dẫn Chạy Nhanh

Nếu bạn chỉ muốn chạy hệ thống nhanh sau khi clone source:

```bash
composer install

cp .env.example .env

php artisan key:generate

php artisan optimize:clear

php artisan optimize

php artisan migrate

php artisan db:seed
```

Tiếp theo:

```bash
start-queue.bat
```

Sau đó:

```bash
php artisan serve
```

Cuối cùng truy cập:

```text
http://127.0.0.1:8000/login
```

Thông tin đăng nhập:

```text
Email: admin@dauthau.gov.vn
Mật khẩu: admin@123
```

---

# Một Số Lệnh Hữu Ích

## Xóa Toàn Bộ Cache

```bash
php artisan optimize:clear
```

## Tạo Lại Cache

```bash
php artisan optimize
```

## Chạy Queue Worker

```bash
php artisan queue:work
```

## Xem Danh Sách Job Bị Lỗi

```bash
php artisan queue:failed
```

## Retry Toàn Bộ Job Bị Lỗi

```bash
php artisan queue:retry all
```

## Khởi Tạo Lại Database

```bash
php artisan migrate:fresh --seed
```

---

# Xử Lý Sự Cố Thường Gặp

## Queue Không Hoạt Động

Kiểm tra:

* File `start-queue.bat` đã được chạy hay chưa.
* Queue Worker có đang hoạt động không.
* Cấu hình Queue trong file `.env` đã chính xác chưa.

---

## Không Thể Chạy Migration

Kiểm tra:

* Database đã được tạo chưa.
* Thông tin kết nối trong file `.env` có chính xác không.
* Dịch vụ MySQL có đang chạy không.

---

## Không Đăng Nhập Được

Hãy chạy lại:

```bash
php artisan migrate:fresh --seed
```

Sau đó sử dụng tài khoản mặc định được cung cấp ở trên.

---

# Chức Năng Chính Của Hệ Thống

* Quản lý tác vụ crawl dữ liệu đấu thầu
* Theo dõi trạng thái các tiến trình crawl
* Xử lý tác vụ nền bằng Queue
* Ghi nhận lịch sử và log crawl
* Retry các job crawl bị lỗi
* Đồng bộ dữ liệu chi tiết gói thầu
* Dashboard theo dõi hoạt động hệ thống
