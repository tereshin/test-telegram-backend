# Telegram Weather Bot на PHP 5.6

Telegram бот для получения информации о погоде в 10 российских городах с использованием API Open Meteo.

![Обложка](https://raw.githubusercontent.com/tereshin/test-telegram-backend/refs/heads/main/cover.png)

## 🌟 Возможности

- 📋 Список из 10 заранее установленных городов
- 🌡️ Актуальная информация о погоде (температура, влажность, давление, осадки)
- ⏰ Текущее время в выбранном городе
- 🎨 Удобный интерфейс с инлайн кнопками
- 📱 Поддержка emoji для лучшего UX

## 🏙️ Поддерживаемые города

1. Москва
2. Санкт-Петербург  
3. Новосибирск
4. Екатеринбург
5. Казань
6. Нижний Новгород
7. Челябинск
8. Самара
9. Омск
10. Ростов-на-Дону

## 📋 Требования

### Для Docker развертывания:
- Docker 20.10+
- Docker Compose 1.27+
- 2GB свободного места на диске

### Для ручной установки:
- PHP 5.6 или выше
- Доступ к интернету
- SSL сертификат (для webhook)
- Telegram Bot Token

## 🚀 Установка и настройка

### 🐳 Быстрая установка с Docker (рекомендуется)

1. **Настройте конфигурацию:**
```bash
cp .env.example .env
nano .env  # Укажите ваш BOT_TOKEN и WEBHOOK_URL
```
> **Примечание:** Файл `config.php` автоматически подхватывает настройки из `.env` файла при работе в Docker

2. **Запустите контейнер:**
```bash
# Простой способ
./docker-run.sh

# Или через make
make up
```

3. **Настройте webhook в Telegram:**
```bash
# Войдите в контейнер и выполните
docker compose exec telegram-bot php set_webhook.php
```

📖 **Подробная документация по Docker:** [DOCKER.md](DOCKER.md)

### 🔧 Ручная установка

#### 1. Создание Telegram бота

1. Найдите в Telegram бота [@BotFather](https://t.me/botfather)
2. Отправьте команду `/newbot`
3. Следуйте инструкциям для создания бота
4. Сохраните полученный токен

#### 2. Настройка файлов

1. В .env файле укажите токен бота и ссылку
```

#### 3. Установка webhook

1. Откройте файл `set_webhook.php`
2. Замените `https://yourdomain.com/bot.php` на URL вашего сервера
3. Выполните файл через браузер или командную строку:
```bash
php set_webhook.php
```

#### 4. Загрузка на сервер

Загрузите все файлы на ваш веб-сервер с поддержкой PHP и SSL.

## 📁 Структура проекта

```
telegram-weather-bot/
├── classes/                 # Классы для работы с API
│   ├── TelegramBot.php      # Класс для работы с Telegram API
│   └── WeatherService.php   # Класс для работы с Open Meteo API
├── tests/                   # Тестирование и проверка системы
│   ├── test_weather.php     # Тестирование API погоды
│   └── check_system.php     # Полная проверка системы
├── docker/                  # Docker конфигурация
│   ├── apache-ssl.conf      # Конфигурация Apache с SSL
│   └── generate-ssl.sh      # Скрипт генерации SSL сертификатов
├── config.php              # Конфигурация бота и список городов
├── bot.php                 # Основной файл бота (webhook endpoint)
├── set_webhook.php         # Скрипт установки webhook
├── .htaccess               # Настройки веб-сервера
├── Dockerfile              # Docker образ
├── docker-compose.yml      # Docker Compose конфигурация
├── docker-run.sh           # Скрипт быстрого запуска
├── Makefile               # Команды управления Docker
├── .env.example           # Пример переменных окружения
├── DOCKER.md              # Документация по Docker
└── README.md              # Основная документация
```

## ⚙️ Конфигурация

Проект использует **единую систему конфигурации**:

### Docker развертывание:
- Настройки задаются в `.env` файле
- `config.php` автоматически читает переменные окружения

### Ручная установка:
- Отредактируйте `config.php` напрямую
- Замените значения по умолчанию на ваши

### Основные параметры:
- `BOT_TOKEN` - токен Telegram бота
- `WEBHOOK_URL` - URL для webhook

## 🧪 Тестирование

Для тестирования работы с API погоды выполните:
```bash
php tests/test_weather.php
```

Для полной проверки системы выполните:
```bash
php tests/check_system.php
```

## 🔧 API Reference

### Telegram Bot API
Бот использует следующие методы Telegram Bot API:
- `sendMessage` - отправка сообщений
- `answerCallbackQuery` - ответ на нажатия кнопок

### Open Meteo API  
Используется бесплатный API [Open Meteo](https://open-meteo.com/) для получения данных о погоде:
- Текущая погода
- Температура
- Влажность  
- Атмосферное давление
- Скорость ветра
- Осадки
