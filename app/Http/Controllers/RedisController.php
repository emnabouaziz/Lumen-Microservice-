<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class RedisController extends BaseController
{
    public function setValue(Request $request)
    {
        $key = $request->input('key');
        $value = $request->input('value');

        Redis::set($key, $value);

        return response()->json(['message' => 'Value set successfully.']);
    }

    public function getValue(Request $request)
    {
        $key = $request->input('key');
        $value = Redis::get($key);

        return response()->json(['key' => $key, 'value' => $value]);
    }
}
