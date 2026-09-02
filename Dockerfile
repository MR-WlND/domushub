# Stage 1: Composer
FROM composer:2.7 AS composer_builder
WORKDIR /app
COPY . .
# Bỏ qua check extension gd và script để build siêu mượt
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs --no-scripts

# Stage 2: Final pre-built PHP web server
FROM shinsenter/laravel:php8.4

# TRỊ LỖI OOM RUNTIME (SIGNAL 9):
# Ép Nginx chỉ dùng 1 worker (thay vì auto tự đẻ ra hàng chục tiến trình theo số CPU của server)
RUN find /etc/nginx /etc/opt -type f -name "*.conf" -exec sed -i 's/worker_processes auto;/worker_processes 1;/g' {} + || true
RUN find /etc/nginx /etc/opt -type f -name "*.conf" -exec sed -i 's/worker_processes  auto;/worker_processes 1;/g' {} + || true

# Ép PHP-FPM chỉ dùng tối đa 2-4 tiến trình con (thay vì hàng chục tiến trình)
ENV PHP_MEMORY_LIMIT=128M
RUN find /etc /usr/local/etc -type f -name "*.conf" -exec sed -i 's/pm.max_children = .*/pm.max_children = 4/g' {} + || true
RUN find /etc /usr/local/etc -type f -name "*.conf" -exec sed -i 's/pm.start_servers = .*/pm.start_servers = 1/g' {} + || true

# Copy toàn bộ code từ stage composer
COPY --from=composer_builder /app /var/www/html

# Cấp quyền ghi cho thư mục lưu trữ của Laravel để tránh lỗi Permission denied
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true
