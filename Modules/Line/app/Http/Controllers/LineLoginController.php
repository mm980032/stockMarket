<?php

namespace Modules\Line\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LineLoginController extends Controller
{
    public $friendStatus = true;
    public function callBackLineToken(Request $request)
    {
        $code = $request->input('code', '');
        $header = [
            "Content-Type" => "application/x-www-form-urlencoded"
        ];
        $parameters = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => "http://stockmarket.com/api/line/callback/token",
            "client_id" => "2004719903",
            "client_secret" => "d0a167499a849ecb55f8df8f4fbf424a"
        ];
        $response = Http::asForm()->withHeaders($header)->post(env("LINE_TOKEN_URL"), $parameters);
        // 確認是否為好友
        // $statusResponse = $this->checkFriendshipStatus($response->json());
        // 取得用戶資料
        $data = $this->getLineUserInfo($response->json());
        var_dump($data);exit;
        // var_dump($statusResponse);exit;
        // if($statusResponse['friendFlag'] === false) {
        //     $this->friendStatus = false;
        //     return redirect()->away('https://line.me/R/ti/p/@081dfqvr');
        //     // return view('/login', ['friendStatus' => $this->friendStatus, 'data' => $data]);
        //     // Redirect('https://line.me/R/ti/p/@081dfqvr');
        // }
    }

    public function getLineUserInfo(array $data)
    {
        var_dump($data);
        var_dump('=============='. '</br>');
        // "error": "invalid_token",
        // "error_description": "The access token revoked"
        $headers =  [
            'Authorization' => 'Bearer ' . $data['access_token'],
            // 'Authorization' => 'Bearer ' . 'eyJhbGciOiJIUzI1NiJ9.0XeLPnO8OAQ8x6N9OaW-tLCiTCISp74JRtXziW__Ni-Km1Ai1FrgxGkj9AsN40bmOARGVmtJXzHbenm7y9yrfxvQwEEK9ZMJDBLb8XSVblwWw5ftbA_tDykWeVNbY1WI85Fh-akRDUbhJ_tYHJaiFuiSFDLOpRqecfnxoXt-HYA.3Uoycp0dIEHAfyeLXNX9CBaoI1tN3lZQfG62or5c1-w',
            'Accept'        => 'application/json',
        ];
        $response = Http::withHeaders($headers)->get(env("LINE_USERINFO_URL"));
        return $response->json();
    }

    /**
     * 推播訊息
     *
     * @return void
     * @author Lawson
     */
    public function pushMessage()
    {
        $channelAccessToken = "4WCaZHy5zww3V9lWGDiCgOxCty2MhBPBNGn3RLPNM7IlHTsMzv2XKYcdPepXV10LDNZ517IiSaZLNtvIugxc7F+jRjq4kiPSD7J8ai0/Kn2E/6ILbUvs/qclBjVPLjNdMDt9l3Vm5uJAc8sydn+dKQdB04t89/1O/w1cDnyilFU=";
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $channelAccessToken
        ];
        $parameters = [
            "to" => "Uc43d99ef1a396067e2cad6c5255916df",
            "messages" => [
                [
                    "type" => "text",
                    "text" => "在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

                    这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。
                    
                    好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。
                    
                    在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。
                    
                    几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。
                    
                    这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
                    在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。

好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。

在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。

几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。

好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。

在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。

几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。

好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。

在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。

几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。

好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。

在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。

几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。

好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。

在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。

几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
在一个遥远的古老村庄里，有一个被大山环绕的美丽小镇，小镇的居民生活平静而又幸福。每天，太阳刚刚升起，村中的人们就开始了一天的劳作。农民们挥舞着锄头，辛勤地在田里劳作，妇女们则在溪边洗衣服，孩子们在村庄的小路上追逐嬉戏。

这一天，村里突然来了一个陌生人，他穿着破旧的衣服，头发凌乱，看起来十分疲惫。他走进村子，四处张望，似乎在寻找什么。村民们对这个陌生人感到好奇，但也有些戒备。陌生人注意到村民们的警惕，便微笑着向他们打招呼，并解释说自己是一个远道而来的旅行者，只是想在这里稍作休息。

好心的村民们听了旅行者的解释，决定帮助他。他们给了他食物和水，还为他指了一条去往村中心的路。陌生人对村民们的热情和善良表示感谢，他感到非常温暖和感动。

在村中心的时候，旅行者被村中的和谐与美丽所吸引，他决定多留几天，更深入地了解这个村庄。村民们热情地向他介绍村里的风俗习惯，还带他参观了村里的学校和图书馆。旅行者与村里的孩子们一起玩耍，与村民们一起分享他的旅行故事，大家都被他那些奇异的冒险经历深深吸引。

几天后，旅行者准备离开这个村庄，继续他的旅行。村民们为他送行，他们给旅行者带上了食物和水，还有一些小礼物，以表达他们的谢意和祝福。旅行者在离开时对每个人说了再见，他的眼中充满了不舍。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。

这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访这个充满爱和温暖的地方。他踏上了归途，心中充满了对未来的希望和憧憬。
这个村庄给旅行者留下了深刻的印象，他在心里暗暗发誓，将来一定还要回来，再次探访嗨嗨妳
"
                ]
            ]
        ];
var_dump(mb_strlen($parameters['messages'][0]["text"], "UTF-8"));
        $response = Http::withHeaders($headers)->post(env("LINE_MESSAGING_URL"), $parameters);
        // 沒有 sentMessages 代表發送錯誤
        var_dump($response->json());
            
    }

    public function accessTokenVerify(Request $request)
    {
        $accessToken = $request->input('access_token');
        $headers =  [
            'Accept'        => 'application/json',
        ];
        $response = Http::withHeaders($headers)->get("https://api.line.me/oauth2/v2.1/verify", ['access_token' => $accessToken]);
        return $response->json();
    }

    public function verify(Request $request) 
    {
        $idToken = $request->input('id_Token');
        $header = [
            "Content-Type" => "application/x-www-form-urlencoded"
        ];
        $parameters = [
            "client_id" => "2004719903",
            "id_token" => $idToken
        ];
        $response = Http::asForm()->withHeaders($header)->post("https://api.line.me/oauth2/v2.1/verify", $parameters);
        var_dump($response->json());exit;
    }
    public function revoke() 
    {
        $header = [
            "Content-Type" => "application/x-www-form-urlencoded"
        ];
        $parameters = [
            "client_id" => "2004719903",
            "access_token" => "eyJhbGciOiJIUzI1NiJ9.I_A4ODg5jk8xo25GyyQXjEbXSCn3VBty47UwBCgOtWuZiqraQd6ojyYTSJYGPVJYyIzBpeUiJknNu-nfMjpJbcHlf7HH5aWA04rl-AmS8D9Tqshtgws5e3MF7cZwr7Om3BQvqkscSYqnVTGeKhckTcwv3HZ-bWAYhOvqKg-zyjk.2eDXU0K5owyc_RJdKt3FWg-GexK0BiJswhJjl6WoJxA"
        ];
        $response = Http::asForm()->withHeaders($header)->post("https://api.line.me/oauth2/v2.1/revoke", $parameters);
        var_dump($response->json());exit;
    }

    public function checkFriendshipStatus(array $data)
    {

        $headers =  [
            'Authorization' => 'Bearer ' . $data['access_token'],
        ];
        $response = Http::withHeaders($headers)->get("https://api.line.me/friendship/v1/status");
        return $response->json();
    }

    public function removeUserAuthorize()
    {
        $channelAccessToken = "4WCaZHy5zww3V9lWGDiCgOxCty2MhBPBNGn3RLPNM7IlHTsMzv2XKYcdPepXV10LDNZ517IiSaZLNtvIugxc7F+jRjq4kiPSD7J8ai0/Kn2E/6ILbUvs/qclBjVPLjNdMDt9l3Vm5uJAc8sydn+dKQdB04t89/1O/w1cDnyilFU=";
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $channelAccessToken
        ];
        $params = [
            'userAccessToken' => 'eyJhbGciOiJIUzI1NiJ9.hnSVXNyHBIBpC0YYSnE3Z8zkC2PXhZxYWilyNH_MVogcffZ2OcDw8Bw5zXBB6J6b1-sAS_tJFSzWK6ch9waiIgxJ3owjCGTWg_GZiHo1xIsO6ycdIw2SkXkCBDMlfRjDs4ExAxNYsTMwwenErfopq6YAfAW7r2LIcOp9oeuZX4A.u35D3w0ok5wDcmMyc6M5tW4oOVxsg3UMD_QmRErH3VM'
        ];
        $response = Http::withHeaders($headers)->post("https://api.line.me/user/v1/deauthorize", $params);
        var_dump($response->status());
        // 處理回應
        if ($response->successful()) {
            // 請求成功的處理
            $responseData = $response->json();
            echo "Success: " . json_encode($responseData);
        } else {
            // 請求失敗的處理
            echo "Error: " . $response->body();
        }
    }
}
