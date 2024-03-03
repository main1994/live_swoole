<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\SendSmsEvent;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    //发送验证码
    public function sendCode(Request $request)
    {
        $phone = $request->post('phone');
        $code = Redis::get('login:' . $phone);
        if (!$code) {
            $code =  rand(1000, 9999);
            Redis::set('login:' . $phone, $code);
        }
        return return_json(200, $code);
    }

    //发送短信验证码
    public function sendSms(Request $request)
    {
        $phone = $request->post('phone') ?? 0;
        $code = Redis::get('login:' . $phone);
        if (!$code) {
            $code =  rand(1000, 9999);
            Redis::set('login:' . $phone, $code, 300);
        }
        event(new SendSmsEvent($phone, $code)); //事件触发写法一
        // SendSmsEvent::dispatch($phone, $code); //事件触发写法二
        return return_json(200, $code);
    }

    //登录
    public function login(Request $request)
    {
        list($phone, $code) = [$request->post('phone'), $request->post('code')];
        if ($code !=  Redis::get('login:' . $phone)) {
            return return_json(301, '验证码错误');
        }
        Redis::sadd('userStat:' . date('Y-m-d'), $phone);
        $phone_arr = str_split($phone);
        foreach ($phone_arr as $key => $value) {
            if (in_array($key, [3, 4, 5])) {
                $phone_arr[$key] = '*';
            }
        }
        $phone = implode('', $phone_arr);
        return return_json(200, $phone);
    }
}
