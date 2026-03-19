# Huesfot

## ⚙️ Yêu cầu hệ thống

- PHP >= 8.0 (khuyến nghị 8.2)
- Composer
- MySQL / MariaDB
- Node.js (nếu build frontend)

---

## 🛠️ Cài đặt dự án

### 1. Clone project

```bash
git clone https://github.com/NhanCopyCode/dauthau.git
cd project-folder
```

---

### 2. Cài đặt dependencies

```bash
composer install
```

---

### 3. Tạo file môi trường

```bash
cp .env.example .env
```

👉 Cập nhật thông tin database trong `.env`

---

### 4. Generate key

```bash
php artisan key:generate
```

---

### 5. Migrate database

```bash
php artisan migrate
```

---

### 6. Seed dữ liệu mẫu

```bash
php artisan db:seed --class=DatabaseSeeder
```

---

### 7. Tạo symbolic link cho storage

```bash
php artisan storage:link
```

---

### 8. Chạy project

```bash
php artisan serve
```

👉 Truy cập: http://127.0.0.1:8000

---

## 🔐 Tài khoản mặc định

| Username                                      | Password |
| --------------------------------------------- | -------- |
| admin                                         | hs@12345 |

_(Có thể thay đổi trong Seeder)_

---

## 🧱 Công nghệ sử dụng

- Laravel (hiện tại đã upgrade từ 8.5 → 9+)
- Bootstrap 3
- AdminLTE Template
- jQuery

---

## 📦 Package sử dụng

- kyslik/column-sortable
- intervention/image
- laravel/passport
- nwidart/laravel-modules
- spatie/\*

---

## 📁 Cấu trúc chính

```
app/
Modules/
resources/views/
routes/
config/
```

---

## ⚠️ Lưu ý khi phát triển

- Dự án đã trải qua quá trình nâng cấp Laravel (8.5 → 9 → ...).
- Một số package có thể cần update khi nâng cấp tiếp.
- Nên làm việc trên branch riêng khi upgrade framework.

---

## 🚀 Roadmap

- [x] Upgrade Laravel 8.5 → 9
- [ ] Upgrade Laravel 10
- [ ] Upgrade Laravel 11
- [ ] Upgrade Laravel 12

---

## 🤝 Đóng góp

Pull request luôn được chào đón.
Hãy đảm bảo code tuân thủ coding standards.

---

## 📄 License

MIT License
