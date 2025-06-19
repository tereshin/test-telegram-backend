<?php

/**
 * Конфигурация Telegram Weather Bot
 * 
 * При использовании Docker переменные берутся из .env файла
 * При ручной установке - из значений по умолчанию (нужно заменить на ваши)
 */

// Настройки Telegram бота
define('BOT_TOKEN', getenv('BOT_TOKEN') ?: 'YOUR_BOT_TOKEN_HERE');
define('TELEGRAM_API', 'https://api.telegram.org/bot' . BOT_TOKEN);

// Список городов с координатами для Open Meteo API
$CITIES = array(
    'Москва' => array('lat' => 55.7558, 'lon' => 37.6176, 'timezone' => 'Europe/Moscow'),
    'Санкт-Петербург' => array('lat' => 59.9311, 'lon' => 30.3609, 'timezone' => 'Europe/Moscow'),
    'Новосибирск' => array('lat' => 55.0084, 'lon' => 82.9357, 'timezone' => 'Asia/Novosibirsk'),
    'Екатеринбург' => array('lat' => 56.8431, 'lon' => 60.6454, 'timezone' => 'Asia/Yekaterinburg'),
    'Казань' => array('lat' => 55.8304, 'lon' => 49.0661, 'timezone' => 'Europe/Moscow'),
    'Нижний Новгород' => array('lat' => 56.2965, 'lon' => 43.9361, 'timezone' => 'Europe/Moscow'),
    'Челябинск' => array('lat' => 55.1644, 'lon' => 61.4368, 'timezone' => 'Asia/Yekaterinburg'),
    'Самара' => array('lat' => 53.2001, 'lon' => 50.15, 'timezone' => 'Europe/Samara'),
    'Омск' => array('lat' => 54.9885, 'lon' => 73.3242, 'timezone' => 'Asia/Omsk'),
    'Ростов-на-Дону' => array('lat' => 47.2357, 'lon' => 39.7015, 'timezone' => 'Europe/Moscow')
);

// URL для Open Meteo API
define('OPEN_METEO_API', 'https://api.open-meteo.com/v1/forecast');

// URL вебхука  
define('WEBHOOK_URL', getenv('WEBHOOK_URL') ?: 'https://your-domain.com/bot.php');

?>