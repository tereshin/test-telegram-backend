<?php

class TelegramBot {
    
    private $token;
    private $apiUrl;
    
    public function __construct($token) {
        $this->token = $token;
        $this->apiUrl = 'https://api.telegram.org/bot' . $token;
    }
    
    /**
     * Отправка сообщения
     */
    public function sendMessage($chatId, $text, $replyMarkup = null) {
        $data = array(
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        );
        
        if ($replyMarkup !== null) {
            $data['reply_markup'] = json_encode($replyMarkup);
        }
        
        return $this->makeRequest('sendMessage', http_build_query($data));
    }
    
    /**
     * Создание инлайн клавиатуры с городами
     */
    public function createCitiesKeyboard($cities) {
        $keyboard = array();
        $buttons = array();
        
        // Создаем короткие ID для городов
        $cityIds = array(
            'Москва' => 'msk',
            'Санкт-Петербург' => 'spb',
            'Новосибирск' => 'nsk',
            'Екатеринбург' => 'ekb',
            'Казань' => 'kzn',
            'Нижний Новгород' => 'nnv',
            'Челябинск' => 'chl',
            'Самара' => 'sam',
            'Омск' => 'omsk',
            'Ростов-на-Дону' => 'rnd'
        );
        
        $count = 0;
        foreach ($cities as $cityName => $cityData) {
            $cityId = isset($cityIds[$cityName]) ? $cityIds[$cityName] : substr(md5($cityName), 0, 8);
            
            $buttons[] = array(
                'text' => $cityName,
                'callback_data' => 'city_' . $cityId
            );
            
            $count++;
            // По 2 кнопки в ряд
            if ($count % 2 == 0) {
                $keyboard[] = $buttons;
                $buttons = array();
            }
        }
        
        // Добавляем последний ряд если есть нечетное количество кнопок
        if (!empty($buttons)) {
            $keyboard[] = $buttons;
        }
        
        return array('inline_keyboard' => $keyboard);
    }
    
    /**
     * Получение названия города по короткому ID
     */
    public function getCityNameById($cityId, $cities) {
        $cityIds = array(
            'msk' => 'Москва',
            'spb' => 'Санкт-Петербург', 
            'nsk' => 'Новосибирск',
            'ekb' => 'Екатеринбург',
            'kzn' => 'Казань',
            'nnv' => 'Нижний Новгород',
            'chl' => 'Челябинск',
            'sam' => 'Самара',
            'omsk' => 'Омск',
            'rnd' => 'Ростов-на-Дону'
        );
        
        if (isset($cityIds[$cityId])) {
            return $cityIds[$cityId];
        }
        
        // Fallback: поиск по хешу для совместимости
        foreach ($cities as $cityName => $cityData) {
            if (substr(md5($cityName), 0, 8) === $cityId) {
                return $cityName;
            }
        }
        
        return null;
    }
    
    /**
     * Ответ на callback query
     */
    public function answerCallbackQuery($callbackQueryId, $text = '') {
        $data = array(
            'callback_query_id' => $callbackQueryId,
            'text' => $text
        );
        
        return $this->makeRequest('answerCallbackQuery', http_build_query($data));
    }
    
    /**
     * Выполнение HTTP запроса к Telegram API
     */
    private function makeRequest($method, $data) {
        $url = $this->apiUrl . '/' . $method;
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        // Логирование ошибок для отладки
        if ($error) {
            error_log("cURL Error: " . $error);
            return false;
        }
        
        if ($httpCode !== 200) {
            error_log("HTTP Error: " . $httpCode . " - Response: " . $response);
            return false;
        }
        
        return json_decode($response, true);
    }
}

?> 