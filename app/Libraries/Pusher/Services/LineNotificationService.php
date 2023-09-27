<?php
namespace App\Libraries\Pusher\Services;

use Illuminate\Support\Facades\Http;

class LineNotificationService{

    private $lineNotifyToken = 'gdAkw54Tcd2Kl6urviW1tZLsjOGtX59yF9vcWfwmTEy';

    /**
     * 發送訊息
     *
     * @param string $msg
     * @return void
     * @author ZhiYong
     */
    public function sendNotification(string $msg) : void
    {
        $headers = array(
            'Content-Type: multipart/form-data',
            'Authorization: Bearer ' . $this->lineNotifyToken
        );
        $message = array(
            'message' => $msg
        );
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , "https://notify-api.line.me/api/notify");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
