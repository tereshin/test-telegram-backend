# Используем официальный образ PHP 5.6 с Apache
FROM php:5.6-apache

# Устанавливаем переменные окружения
ENV APACHE_DOCUMENT_ROOT /var/www/html
ENV TZ=Europe/Moscow

# Обновляем источники пакетов для архивных репозиториев Debian Stretch
RUN echo "deb http://archive.debian.org/debian stretch main" > /etc/apt/sources.list \
    && echo "deb http://archive.debian.org/debian-security stretch/updates main" >> /etc/apt/sources.list \
    && echo "Acquire::Check-Valid-Until false;" > /etc/apt/apt.conf.d/10-nocheckvalid \
    && echo "Acquire::Check-Date false;" >> /etc/apt/apt.conf.d/10-nocheckvalid

# Устанавливаем необходимые пакеты и расширения PHP
RUN apt-get update && apt-get install -y --allow-unauthenticated \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    curl \
    nano \
    ca-certificates \
    && docker-php-ext-install curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Включаем необходимые модули Apache
RUN a2enmod rewrite ssl headers

# Устанавливаем часовой пояс
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Копируем конфигурацию Apache
COPY docker/apache-ssl.conf /etc/apache2/sites-available/000-default.conf

# Настраиваем PHP 5.6
RUN echo "date.timezone = ${TZ}" >> /usr/local/etc/php/conf.d/timezone.ini \
    && echo "memory_limit = 128M" >> /usr/local/etc/php/conf.d/memory.ini \
    && echo "max_execution_time = 30" >> /usr/local/etc/php/conf.d/execution.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "allow_url_fopen = On" >> /usr/local/etc/php/conf.d/url_fopen.ini \
    && echo "user_agent = 'TelegramBot/1.0'" >> /usr/local/etc/php/conf.d/user_agent.ini

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы приложения
COPY --chown=www-data:www-data . .

# Создаем необходимые директории
RUN mkdir -p /var/www/html/logs /var/www/html/ssl \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 644 /var/www/html/config.php

# Создаем healthcheck скрипт
RUN echo '<?php http_response_code(200); echo "OK"; ?>' > /var/www/html/health.php

# Открываем порты
EXPOSE 80 443

# Добавляем healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1

# Команда запуска Apache
CMD ["apache2-foreground"] 