<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTestController extends Controller
{
    //
    public function index()
    {
        // $task = Call::find(62);

        // dd($task->attributes['callback_url']);

        return view('api.index');
    }
}
