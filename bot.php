<?php

require_once 'config.php';
require_once 'classes/TelegramBot.php';
require_once 'classes/WeatherService.php';

// Создаем экземпляры классов
$bot = new TelegramBot(BOT_TOKEN);
$weatherService = new WeatherService();

// Получаем данные от Telegram
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Логирование для отладки (можно убрать в продакшене)
file_put_contents('bot.log', date('Y-m-d H:i:s') . " - " . $input . "\n", FILE_APPEND);

if (!$update) {
    exit;
}

// Обработка текстовых сообщений
if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'];
    $text = isset($message['text']) ? $message['text'] : '';
    
    if ($text == '/start' || $text == '/cities' || strtolower($text) == 'города') {
        // Отправляем приветствие и клавиатуру с городами
        $welcomeText = "🏙️ <b>Добро пожаловать в бот прогноза погоды!</b>\n\n";
        $welcomeText .= "Выберите город из списка ниже, чтобы узнать актуальную погоду:";
        
        $keyboard = $bot->createCitiesKeyboard($CITIES);
        $bot->sendMessage($chatId, $welcomeText, $keyboard);
        
    } elseif ($text == '/help') {
        $helpText = "🤖 <b>Помощь по боту</b>\n\n";
        $helpText .= "Доступные команды:\n";
        $helpText .= "• /start - начать работу с ботом\n";
        $helpText .= "• /cities - показать список городов\n";
        $helpText .= "• /help - показать эту справку\n\n";
        $helpText .= "Просто выберите город из списка, и я покажу вам актуальную погоду!";
        
        $bot->sendMessage($chatId, $helpText);
        
    } else {
        // Неизвестная команда
        $unknownText = "❓ Извините, я не понимаю эту команду.\n\n";
        $unknownText .= "Используйте /start для начала работы или /help для справки.";
        
        $bot->sendMessage($chatId, $unknownText);
    }
}

// Обработка callback query (нажатия на инлайн кнопки)
if (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $messageId = $callbackQuery['message']['message_id'];
    $data = $callbackQuery['data'];
    $callbackQueryId = $callbackQuery['id'];
    
    // Отвечаем на callback query
    $bot->answerCallbackQuery($callbackQueryId);
    
    // Проверяем, что это выбор города
    if (strpos($data, 'city_') === 0) {
        $cityId = substr($data, 5);
        $cityName = $bot->getCityNameById($cityId, $CITIES);
        
        if ($cityName && isset($CITIES[$cityName])) {
            $cityData = $CITIES[$cityName];
            
            // Отправляем сообщение о загрузке
            $loadingText = "⏳ Получаю данные о погоде в городе <b>" . $cityName . "</b>...";
            $bot->sendMessage($chatId, $loadingText);
            
            // Получаем погоду
            $weather = $weatherService->getWeather(
                $cityData['lat'], 
                $cityData['lon'], 
                $cityData['timezone']
            );
            
            if ($weather) {
                // Формируем сообщение с погодой
                $weatherText = "🌡️ <b>Погода в городе " . $cityName . "</b>\n\n";
                $weatherText .= "📅 <b>Время:</b> " . $weather['current_time'] . "\n";
                $weatherText .= "🌡️ <b>Температура:</b> " . $weather['temperature'] . "°C\n";
                $weatherText .= "💧 <b>Влажность:</b> " . $weather['humidity'] . "%\n";
                $weatherText .= "🏔️ <b>Давление:</b> " . $weather['pressure'] . " гПа\n";
                $weatherText .= "💨 <b>Скорость ветра:</b> " . $weather['wind_speed'] . " км/ч\n";
                
                if ($weather['precipitation'] > 0) {
                    $weatherText .= "🌧️ <b>Осадки:</b> " . $weather['precipitation'] . " мм\n";
                } else {
                    $weatherText .= "☀️ <b>Осадки:</b> отсутствуют\n";
                }
                
                $weatherText .= "\n" . $weather['weather_description'];
                
                // Добавляем кнопку для выбора другого города
                $backKeyboard = array(
                    'inline_keyboard' => array(
                        array(
                            array(
                                'text' => '🔙 Выбрать другой город',
                                'callback_data' => 'back_to_cities'
                            )
                        )
                    )
                );
                
                $bot->sendMessage($chatId, $weatherText, $backKeyboard);
                
            } else {
                $errorText = "❌ <b>Ошибка получения данных</b>\n\n";
                $errorText .= "Не удалось получить данные о погоде для города " . $cityName . ".\n";
                $errorText .= "Попробуйте позже или выберите другой город.";
                
                $keyboard = $bot->createCitiesKeyboard($CITIES);
                $bot->sendMessage($chatId, $errorText, $keyboard);
            }
            
        } else {
            $errorText = "❌ <b>Город не найден</b>\n\n";
            $errorText .= "Выберите город из списка ниже:";
            $keyboard = $bot->createCitiesKeyboard($CITIES);
            $bot->sendMessage($chatId, $errorText, $keyboard);
        }
    }
    
    // Обработка возврата к списку городов
    if ($data == 'back_to_cities') {
        $backText = "🏙️ <b>Выберите город:</b>";
        $keyboard = $bot->createCitiesKeyboard($CITIES);
        $bot->sendMessage($chatId, $backText, $keyboard);
    }
}

?> 