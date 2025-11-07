FROM php:8.1-cli

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    libonig-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширение mbstring
RUN docker-php-ext-install mbstring

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /app

# Копируем файлы проекта
COPY composer.json composer.lock ./
COPY phpunit.xml ./
COPY src/ ./src/
COPY tests/ ./tests/

# Устанавливаем зависимости (включая dev для тестов)
RUN composer install --optimize-autoloader

# Запускаем тесты по умолчанию
CMD ["vendor/bin/phpunit"]

