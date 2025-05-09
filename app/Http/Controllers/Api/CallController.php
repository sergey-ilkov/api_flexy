<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessCall;
use App\Models\Call;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{
    //
    public function task(Request $request)
    {

        $channel = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/Call/call.log'),
        ]);

        $request->validate([
            'phone' => 'required|numeric',
            'callback_url' => 'required|url',
        ]);


        $dataDB = [
            'result' => true,
            'task_id' => str()->uuid(),
            'attributes' => [
                'state' => 'waiting',
                'phone' => $request->phone,
                'call_from_number' => '380951111111',
                'callback_url' => $request->callback_url,
                'ivr_answer' => null,
                'is_called' => false,

                'is_callback_sent' => false,
                'is_error_happened' => false,
                'error_desc' => null,

                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            'error' => null,
        ];


        $response = Http::get($request->callback_url);

        if ($response->status() != 200) {
            $dataDB['result'] = false;
            $dataDB['error'] = 'Callback url not available.';

            Log::stack(['single', $channel])->error('Error get callback_url CallController');
        }

        // ? create Task DB
        $task = Call::create($dataDB);



        if ($task) {
            // ? add task Job                  
            ProcessCall::dispatch($task);
            // ProcessCall::dispatchSync($task);
            // ProcessCall::dispatch($task)->delay(now()->addSecond(30));


            Log::stack(['single', $channel])->info('Add Job task');

            $dataSend = [
                'result' => $task->result,
                'message' => [
                    'id' => $task->task_id,
                    'phone' => $task->attributes['phone'],
                    'callback_url' => $task->attributes['callback_url'],
                    'created_at' => Carbon::parse($task->created_at)->format('Y-m-d H:i:s'),
                ],
                'error' => $task->error,
            ];



            return response()->json($dataSend);
        }

        Log::stack(['single', $channel])->error('Error task save DB CallController');

        return response()->json(['error' => 'Error task save DB']);
    }

    public function getTask($id) {}
}