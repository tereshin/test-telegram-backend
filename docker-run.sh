#!/bin/bash

# Скрипт для запуска Telegram Weather Bot в Docker контейнере

set -e

echo "🚀 Запуск Telegram Weather Bot в Docker контейнере (PHP 5.6)"
echo "⚠️  Внимание: Используется PHP 5.6 (EOL версия). Рекомендуется обновление для продакшена."

# Проверяем наличие .env файла
if [ ! -f .env ]; then
    echo "⚠️  Файл .env не найден. Создаю из примера..."
    cp .env.example .env
    echo "📝 Отредактируйте файл .env с вашими настройками перед запуском"
    exit 1
fi

# Создаем необходимые директории
mkdir -p logs ssl

# Проверяем наличие SSL сертификатов
if [ ! -f ssl/cert.pem ] || [ ! -f ssl/key.pem ]; then
    echo "🔐 SSL сертификаты не найдены. Генерирую самоподписанный сертификат для разработки..."
    ./docker/generate-ssl.sh
fi

# Проверяем, что Docker установлен
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен. Установите Docker для продолжения."
    exit 1
fi

# Проверяем, что docker compose доступен
if ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose не доступен. Установите Docker Compose для продолжения."
    exit 1
fi

echo "🔧 Сборка Docker образа..."
docker compose build

echo "🏁 Запуск контейнера..."
docker compose up -d

echo "✅ Контейнер запущен!"
echo ""
echo "📋 Полезные команды:"
echo "  docker compose logs -f telegram-bot  # Просмотр логов"
echo "  docker compose down                  # Остановка контейнера"
echo "  docker compose restart              # Перезапуск контейнера"
echo ""
echo "🌐 Бот доступен по адресу: https://$(grep SERVER_NAME .env | cut -d'=' -f2)/bot.php"
echo "📝 Не забудьте настроить webhook в Telegram!" 