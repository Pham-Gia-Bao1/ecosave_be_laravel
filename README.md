## run server
--- php artisan serve

## install jwt
--- composer require tymon/jwt-auth

##  Publish the JWT Configuration
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

## Generate the JWT Secret Key
--- php artisan jwt:secret

## run migrations
--- php artisan migrate

## run seeders
--- php artisan db:seed

Dưới đây là tài liệu hướng dẫn cài đặt **Redis** và **Laravel Realtime** đầy đủ và chi tiết.

---

# **📌 Hướng Dẫn Cài Đặt Realtime với Redis & Laravel Echo Server**

## **1️⃣ Cài đặt Redis cho Laravel**
### **1.1 Cài đặt thư viện Redis cho Laravel**
```sh
composer require predis/predis
```

---

### **1.2 Cấu hình `.env` để sử dụng Redis**
Mở file `.env` và thiết lập cấu hình Redis:

```env
BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

---

### **1.3 Cấu hình Redis trong `config/database.php`**
Mở file `config/database.php`, kiểm tra Redis có được cấu hình đúng không:

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', 0),
    ],

    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', 1),
    ],
],
```

---

### **1.4 Cấu hình Queue trong `config/queue.php`**
Mở file `config/queue.php`, đảm bảo Redis được đặt làm driver hàng đợi:

```php
'default' => env('QUEUE_CONNECTION', 'redis'),
```

---

### **1.5 Cấu hình Broadcasting trong `config/broadcasting.php`**
Mở file `config/broadcasting.php` và đảm bảo Redis là driver mặc định:

```php
'default' => env('BROADCAST_DRIVER', 'redis'),
```

---

## **2️⃣ Cài đặt Redis trên Windows**
### **2.1 Tải và cài đặt Redis cho Windows**
📌 **Tải Redis tại:**
👉 [https://github.com/tporadowski/redis/releases](https://github.com/tporadowski/redis/releases)

📥 **Cài đặt Redis:**
1. Tải **Redis-x64-5.0.14.1.msi** hoặc phiên bản mới hơn.
2. Cài đặt và thêm Redis vào hệ thống.
3. Thêm `redis-server.exe` vào **biến môi trường hệ thống** để có thể chạy Redis từ bất kỳ thư mục nào.

📌 **Kiểm tra Redis đã cài chưa:**
```sh
redis-server
```
Nếu Redis chạy thành công, nó sẽ hiển thị **Redis server started**.

---

## **3️⃣ Cài đặt Redis Extension cho PHP**
🔹 **Cách cài đặt Redis extension cho PHP 8.0.25 (ZTS, x64) trong XAMPP**

### **3.1 Tải Redis extension**
📥 **Tải tại:**
👉 [https://windows.php.net/downloads/pecl/releases/redis/](https://windows.php.net/downloads/pecl/releases/redis/)

🔎 Tìm phiên bản Redis mới nhất cho PHP **8.0, ZTS, x64**
Ví dụ: **php_redis-5.3.7-8.0-ts-vs16-x64.zip**

✅ **Giải thích các ký hiệu:**
- `5.3.7` → Phiên bản Redis
- `8.0` → Tương thích với PHP 8.0
- `ts` → Thread Safe (ZTS)
- `vs16` → Biên dịch bằng **Visual Studio 2019**
- `x64` → Dành cho hệ điều hành **64-bit**

---

### **3.2 Cài đặt Redis extension vào PHP**
1. **Giải nén** file `.zip` đã tải về.
2. **Copy** file `php_redis.dll` vào thư mục XAMPP:
   ```
   C:\xampp\php\ext\
   ```
3. Mở file `php.ini` (trong thư mục `C:\xampp\php\`)
   Thêm dòng sau vào cuối file:
   ```
   extension=redis
   ```

---

### **3.3 Kiểm tra Redis đã được cài chưa**
Chạy lệnh sau để kiểm tra Redis extension:
```sh
php -m | findstr redis
```
Nếu hiển thị **redis**, nghĩa là Redis extension đã được cài đặt thành công. 🎉

---

## **4️⃣ Chạy Redis Server**
Sau khi Redis đã được cài đặt, khởi động Redis bằng lệnh:
```sh
redis-server
```

---

## **5️⃣ Cài đặt Laravel Echo Server**
Laravel Echo Server giúp Laravel gửi sự kiện tới frontend bằng WebSockets.

### **5.1 Cài đặt Laravel Echo Server**
```sh
npm install -g laravel-echo-server
```

---

### **5.2 Khởi tạo Laravel Echo Server**
```sh
laravel-echo-server init
```
Khi chạy lệnh này, Laravel sẽ yêu cầu bạn nhập một số thông tin. Nếu không chắc chắn, có thể giữ **giá trị mặc định** bằng cách nhấn **Enter**.

---

### **5.3 Chạy Laravel Echo Server**
```sh
laravel-echo-server start
```
Nếu chạy thành công, bạn sẽ thấy **Laravel Echo Server running on port 6001**.

---

## **6️⃣ Kiểm tra lại toàn bộ hệ thống**
1. **Mở Redis server:**
   ```sh
   redis-server
   ```
2. **Mở Laravel queue worker:**
   ```sh
   php artisan queue:work
   ```
3. **Mở Laravel Echo Server:**
   ```sh
   laravel-echo-server start
   ```
4. **Mở Tinker và phát sự kiện:**
   ```sh
   php artisan tinker
   >>> event(new \App\Events\ProductCreated(\App\Models\Product::first()));
   ```
5. **Kiểm tra log trên Redis:**
   ```sh
   redis-cli
   SUBSCRIBE products
   ```

Nếu hệ thống hoạt động đúng, Redis sẽ nhận sự kiện và **frontend sẽ cập nhật dữ liệu theo thời gian thực!** 🎉

---

## **🔥 Tổng kết**
✅ **Cài đặt Redis cho Laravel**
✅ **Cấu hình Redis, Queue, và Broadcasting**
✅ **Cài đặt Redis trên Windows**
✅ **Cài đặt Redis Extension cho PHP**
✅ **Chạy Laravel Echo Server để nhận sự kiện realtime**

Bây giờ, bạn đã có hệ thống **Realtime với Redis, Laravel, và WebSockets** hoạt động! 🚀
