# 🐳 Docker Deployment Guide

Руководство по развертыванию Telegram Weather Bot с использованием Docker (PHP 5.6).

## 📋 Требования

- Docker 20.10+
- Docker Compose 1.27+

## 🚀 Быстрый старт

### 1. Подготовка конфигурации

```bash
# Скопируйте пример конфигурации
cp .env.example .env

# Отредактируйте настройки
nano .env
```

### 2. Настройка переменных окружения

Отредактируйте файл `.env`:

```env
# Обязательные настройки
BOT_TOKEN=your_telegram_bot_token_here
WEBHOOK_URL=https://your-domain.com/bot.php
SERVER_NAME=your-domain.com
```

### 3. Запуск контейнера

```bash
# Простой способ
./docker-run.sh

# Или через make
make up

# Или напрямую через docker compose
docker compose up -d
```

## 🔧 Детальная настройка

### SSL Сертификаты

#### Для разработки (самоподписанный сертификат)
```bash
# Автоматическая генерация
./docker/generate-ssl.sh your-domain.com

# Или через make
make ssl
```

#### Для продакшена (Let's Encrypt)
```bash
# Создайте директорию ssl
mkdir -p ssl

# Поместите ваши сертификаты
cp /path/to/your/fullchain.pem ssl/cert.pem
cp /path/to/your/privkey.pem ssl/key.pem
```

### Конфигурация Apache

Файл `docker/apache-ssl.conf` содержит конфигурацию для Apache с SSL.
При необходимости можете его отредактировать.

## 📊 Управление контейнером

### Использование Makefile

```bash
make help      # Показать все доступные команды
make build     # Собрать образ
make up        # Запустить контейнер
make down      # Остановить контейнер
make restart   # Перезапустить контейнер
make logs      # Показать логи
make shell     # Войти в контейнер
make test      # Запустить тесты
make clean     # Очистить Docker ресурсы
```

### Использование Docker Compose

```bash
# Сборка образа
docker compose build

# Запуск в фоне
docker compose up -d

# Просмотр логов
docker compose logs -f telegram-bot

# Остановка
docker compose down

# Перезапуск
docker compose restart
```

### Прямые команды Docker

```bash
# Просмотр запущенных контейнеров
docker ps

# Вход в контейнер
docker exec -it telegram-weather-bot bash

# Просмотр логов
docker logs telegram-weather-bot

# Остановка контейнера
docker stop telegram-weather-bot
```

## 🧪 Тестирование

```bash
# Тест API погоды
docker compose exec telegram-bot php tests/test_weather.php

# Полная проверка системы
docker compose exec telegram-bot php tests/check_system.php

# Или через make
make test
```

## 📁 Структура Docker файлов

```
telegram-weather-bot/
├── Dockerfile              # Основной образ
├── docker-compose.yml      # Композиция сервисов
├── .dockerignore           # Исключения для сборки
├── .env.example            # Пример конфигурации
├── docker-run.sh           # Скрипт быстрого запуска
├── Makefile               # Команды управления
└── docker/
    ├── apache-ssl.conf     # Конфигурация Apache SSL
    └── generate-ssl.sh     # Генератор SSL сертификатов
```

## 🔍 Отладка

### Просмотр логов

```bash
# Логи контейнера
docker compose logs -f telegram-bot

# Логи Apache
docker compose exec telegram-bot tail -f /var/log/apache2/error.log
docker compose exec telegram-bot tail -f /var/log/apache2/access.log

# Логи бота
docker compose exec telegram-bot tail -f logs/bot.log
```

### Проверка состояния

```bash
# Проверка healthcheck
docker compose ps

# Информация о контейнере
docker inspect telegram-weather-bot

# Использование ресурсов
docker stats telegram-weather-bot
```

## 🔒 Безопасность

### Файрвол
```bash
# Разрешить только необходимые порты
ufw allow 80/tcp
ufw allow 443/tcp
```

### Обновления
```bash
# Регулярно обновляйте образ
docker compose pull
docker compose up -d
```

### Резервное копирование
```bash
# Бэкап конфигурации
tar -czf telegram-bot-backup.tar.gz .env ssl/ logs/
```

## 🚨 Troubleshooting

### Частые проблемы

1. **Порт уже занят**
   ```bash
   # Найти процесс, использующий порт
   lsof -i :80
   lsof -i :443
   ```

2. **SSL сертификат не работает**
   ```bash
   # Проверить сертификат
   openssl x509 -in ssl/cert.pem -text -noout
   ```

3. **Webhook не работает**
   ```bash
   # Проверить доступность
   curl -I https://your-domain.com/bot.php
   ```

4. **Логи недоступны**
   ```bash
   # Проверить права доступа
   docker compose exec telegram-bot ls -la logs/
   ```

## 📈 Мониторинг

### Базовый мониторинг
```bash
# Создать скрипт мониторинга
cat > monitor.sh << 'EOF'
#!/bin/bash
while true; do
    if ! docker compose ps | grep -q "Up"; then
        echo "Container is down, restarting..."
        docker compose up -d
    fi
    sleep 60
done
EOF

chmod +x monitor.sh
```

### Настройка alerting
Рекомендуется использовать внешние сервисы мониторинга для продакшена.
