<?php
//响应返回
if (!function_exists('return_json')) {
    function return_json($status, $data = null)
    {
        return array_merge(['status' => $status], $status == 200 ? ['msg' => null, 'data' => $data] : ['msg' => $data, 'data' => null]);
    }
}
