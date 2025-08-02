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
     * Выполнить запрос к API
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $this->base_url . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            error_log('LaTradeAPI Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        return array(
            'code' => $httpCode,
            'data' => $result
        );
    }
}