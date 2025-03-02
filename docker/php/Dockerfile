# Sử dụng PHP 8.0.25 CLI làm nền tảng
FROM php:8.0.25-cli

# Thiết lập biến môi trường
ENV COMPOSER_VERSION=2.6.6 \
    REDIS_VERSION=5.0.14 \
    APP_ENV=local \
    APP_DEBUG=true \
    APP_KEY=base64:3U9Z7MrsxckmFv224b7m8oFGgpfx0qbrrqJ8ohi0TT0=

# Cập nhật và cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libmemcached-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip pdo pdo_mysql mbstring bcmath opcache

# Cài đặt Redis extension cho PHP
RUN pecl install redis && docker-php-ext-enable redis

# Cài đặt Composer phiên bản chính xác
RUN curl -sS https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar -o /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Tạo thư mục ứng dụng Laravel
WORKDIR /var/www/html

# Sao chép mã nguồn Laravel
COPY . /var/www/html

# Cài đặt Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Tạo file .env và sinh khóa ứng dụng
RUN cp .env.example .env && php artisan key:generate

# Biên dịch Redis từ nguồn
RUN curl -L https://github.com/redis/redis/archive/${REDIS_VERSION}.tar.gz -o redis.tar.gz && \
    tar xzvf redis.tar.gz && \
    cd redis-${REDIS_VERSION} && \
    make && make install && \
    cd .. && rm -rf redis-${REDIS_VERSION} redis.tar.gz

# Cài đặt Supervisor
RUN apt-get install -y supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Mở cổng 9000 cho ứng dụng Laravel
EXPOSE 9000

# Chạy Supervisor để quản lý Laravel và Redis
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
