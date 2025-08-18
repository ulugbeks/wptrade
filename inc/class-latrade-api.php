<?php
/**
 * Класс для работы с API LA-Trade
 */
class LaTradeAPI {
    
    private $username = 'admin';
    private $password = 'M5YQkSoB6phia0BruaLEjuAq73vSAdP9';
    private $base_url = 'https://la-trade.ru/api/isakov/';
    
    /**
     * Получить общую информацию
     */
    public function getInfo() {
        $response = $this->makeRequest('info');
        return $response;
    }
    
    /**
     * Получить информацию о пользователе
     */
    public function getUserInfo($user_id) {
        $response = $this->makeRequest('userget/' . $user_id);
        return $response;
    }
    
    /**
     * Обновить подписку пользователя
     */
    public function updateUserSubscription($user_id, $indicator_id, $account_number, $date) {
        $data = array(
            'indicator' => $indicator_id,
            'account_number' => $account_number,
            'date' => $date
        );
        
        $response = $this->makeRequest('userupd/' . $user_id, 'POST', $data);
        return $response;
    }
    
    /**
     * Преобразовать название индикатора в ID
     */
    public function getIndicatorId($name) {
        $indicators = array(
            'volatility_levels' => 1,
            'fibo_musang' => 2,
            'future_volume' => 3,
            'options_fx' => 4
        );
        
        return isset($indicators[$name]) ? $indicators[$name] : 0;
    }
    
    /**
     * Получить название индикатора по ID
     */
    public function getIndicatorName($id) {
        $indicators = array(
            1 => 'Volatility Levels',
            2 => 'Fibo Musang',
            3 => 'Future Volume',
            4 => 'Options FX'
        );
        
        return isset($indicators[$id]) ? $indicators[$id] : 'Unknown Indicator';
    }
    
    /**
     * Выполнить запрос к API
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->base_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Для решения проблем с SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Таймаут 30 секунд
        
        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            error_log('LaTradeAPI CURL Error: ' . curl_error($ch));
            curl_close($ch);
            return array(
                'code' => 0,
                'data' => array('error' => curl_error($ch))
            );
        }
        
        curl_close($ch);
        
        // Пытаемся декодировать JSON
        $result = json_decode($response, true);
        
        // Если не удалось декодировать JSON, возвращаем сырой ответ
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('LaTradeAPI JSON Error: ' . json_last_error_msg());
            error_log('LaTradeAPI Raw Response: ' . $response);
            return array(
                'code' => $httpCode,
                'data' => array('raw' => $response, 'error' => 'JSON decode error')
            );
        }
        
        return array(
            'code' => $httpCode,
            'data' => $result
        );
    }
    
    /**
     * Проверить доступность API
     */
    public function testConnection() {
        $result = $this->getInfo();
        return $result && isset($result['code']) && $result['code'] == 200;
    }
}