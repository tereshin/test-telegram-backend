#!/bin/bash

# Скрипт для генерации самоподписанного SSL сертификата
# Используется только для разработки и тестирования

DOMAIN=${1:-localhost}
SSL_DIR="ssl"

echo "Создание SSL сертификата для домена: $DOMAIN"

# Создаем директорию для SSL
mkdir -p $SSL_DIR

# Генерируем приватный ключ
openssl genrsa -out $SSL_DIR/key.pem 2048

# Генерируем самоподписанный сертификат
openssl req -new -x509 -key $SSL_DIR/key.pem -out $SSL_DIR/cert.pem -days 365 -subj "/C=RU/ST=Moscow/L=Moscow/O=TelegramBot/OU=Development/CN=$DOMAIN"

# Устанавливаем права доступа
chmod 600 $SSL_DIR/key.pem
chmod 644 $SSL_DIR/cert.pem

echo "SSL сертификат создан в директории $SSL_DIR/"
echo "ВНИМАНИЕ: Это самоподписанный сертификат только для разработки!"
echo "Для продакшена используйте сертификат от доверенного CA (например, Let's Encrypt)" 