<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Outs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LiveController extends Controller
{
    public function push(Request $request)
    {
        //后台提交的赛况数据入库
        $base_path = config('app.base_path');
        $faker = \Faker\Factory::create();
        //伪造数据
        $content = $faker->text(20);
        $img = explode('/', $faker->image(null, 360, 360, 'animals', true))[2];
        rename('/tmp/' . $img, $base_path . 'public/fakerimg/' . $img);
        $team_id = rand(1, 2);
        $out = [
            'game_id' => 1,
            'team_id' => $team_id,
            'content' => $content,
            'type' => 1,
            'image' => $img,
            'status' => 1
        ];
        Outs::create($out);
        $out = Outs::orderBy('id', 'desc')->first();
        $teams = Team::orderBy('id')->limit(2)->get()->keyBy('id');
        foreach ($_POST['http_server']->ports[1]->connections as $fd) {
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            if ($_POST['http_server']->isEstablished($fd)) {
                $data = [
                    'id' => $out->id,
                    'type' => 1,
                    'title' => $teams[$out->team_id]['name'] ?? '直播员',
                    'logo' => '*******:8088/logo/' . ($teams[$out->team_id]['image'] ?? ''),
                    'content' => $out->content,
                    'image' => '*******:8088/fakerimg/' . $out->image,
                    'time' => $out->created_at->format('H:i')
                ];
                $_POST['http_server']->push($fd, json_encode($data));
            }
        }
        return return_json(200);
    }

    //广播数据数据查询
    // public function outs(Request $request)
    // {
    //     // $page = $request->get('page') ?? 1;
    //     // $pagesiz = 15;
    //     // $limit = $pagesiz * ($page - 1);
    //     // $outs = Outs::orderBy('id')->skip($limit)->take($pagesiz)->get();
    //     $outs = Outs::orderBy('id', 'desc')->limit(15)->get();
    //     $teams = Team::orderBy('id')->limit(2)->get()->keyBy('id');
    //     $data = [];
    //     foreach ($outs as $out) {
    //         $data[] = [
    //             'id' => $out->id,
    //             'type' => 1,
    //             'title' => $teams[$out->team_id]['name'] ?? '直播员',
    //             'logo' => 'http://laravel.com:8088/logo/' . ($teams[$out->team_id]['image'] ?? ''),
    //             'content' => $out->content,
    //             'image' => 'http://laravel.com:8088/fakerimg/' . $out->image,
    //             'time' => $out->created_at->format('H:i')
    //         ];
    //     }
    //     array_multisort(array_column($data, 'id'), SORT_ASC, $data); //重新排序
    //     return return_json(200, $data);
    // }
}
