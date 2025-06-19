# Makefile для управления Telegram Weather Bot

.PHONY: help build up down restart logs ssl shell test clean

# Показать справку
help:
	@echo "🤖 Telegram Weather Bot - Docker Management"
	@echo ""
	@echo "Доступные команды:"
	@echo "  make build   - Собрать Docker образ"
	@echo "  make up      - Запустить контейнер"
	@echo "  make down    - Остановить контейнер"
	@echo "  make restart - Перезапустить контейнер"
	@echo "  make logs    - Показать логи"
	@echo "  make ssl     - Сгенерировать SSL сертификат"
	@echo "  make shell   - Войти в контейнер"
	@echo "  make test    - Запустить тесты"
	@echo "  make clean   - Очистить Docker ресурсы"

# Собрать образ
build:
	@echo "🔧 Сборка Docker образа..."
	docker compose build

# Запустить контейнер
up:
	@echo "🚀 Запуск контейнера..."
	@make ssl
	docker compose up -d
	@echo "✅ Контейнер запущен!"

# Остановить контейнер
down:
	@echo "🛑 Остановка контейнера..."
	docker compose down

# Перезапустить контейнер
restart:
	@echo "🔄 Перезапуск контейнера..."
	docker compose restart

# Показать логи
logs:
	@echo "📋 Логи контейнера:"
	docker compose logs -f telegram-bot

# Сгенерировать SSL сертификат
ssl:
	@if [ ! -f ssl/cert.pem ] || [ ! -f ssl/key.pem ]; then \
		echo "🔐 Генерация SSL сертификата..."; \
		./docker/generate-ssl.sh; \
	fi

# Войти в контейнер
shell:
	@echo "🐚 Вход в контейнер..."
	docker compose exec telegram-bot bash

# Запустить тесты
test:
	@echo "🧪 Запуск тестов..."
	docker compose exec telegram-bot php tests/check_system.php

# Очистить Docker ресурсы
clean:
	@echo "🧹 Очистка Docker ресурсов..."
	docker compose down -v
	docker system prune -f 