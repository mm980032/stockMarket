<?php
    /**
     * curl
     *
     * @param string $method
     * @param string $curlUrl
     * @param array $headers
     * @param array $data
     * @return array
     * @author ZhiYong
     */
    function curl(string $method = 'GET', string $curlUrl, array $headers = [], array $data = []) : array
    {
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , $curlUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch($method){
            case 'GET':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POST         , true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data) );
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data) );
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }
        $result['content']   = curl_exec($ch);
        $result['httpCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $result;
    }
