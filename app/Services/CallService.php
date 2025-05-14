<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallService
{
    /**
     * Create a new class instance.
     */

    // protected $channel = Log::build([
    //     'driver' => 'single',
    //     'path' => storage_path('logs/Call/call.log'),
    // ]);

    protected $channel;

    public function __construct()
    {
        //
        $this->channel = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/Call/call.log'),
        ]);
    }
    public function call($task)
    {
        // 


        // $channel = Log::build([
        //     'driver' => 'single',
        //     'path' => storage_path('logs/Call/call.log'),
        // ]);

        // Log::stack(['single', $channel])->info('Cron Job running CallService call() ', ['task' => $task]);
        Log::stack([$this->channel])->info('Cron Job running CallService call()');

        // sleep(10); // 10 seconds
        sleep(5);

        // ? формируется ответ после звонка
        $data = [
            'result' => true,
            'message' => [
                'type' => 'task',
                'id' => $task->task_id,
                'attributes' => [
                    'state' => 'done',
                    'phone' => $task->attributes['phone'],
                    'call_from_number' => $task->attributes['call_from_number'],
                    'callback_url' => $task->attributes['callback_url'],

                    'ivr_answer' => 1, // null 
                    'is_called' => true,
                    'is_callback_sent' => false,
                    'is_error_happened' => false,
                    'error_desc' => null,

                    'created_at' => Carbon::parse($task->created_at)->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ],
            ],
            'error' => null,
        ];

        // ? 'ivr_answer' => 1, // null 
        // ? 'is_error_happened' => false, - true если во время звонка произошла ошибка
        // ? error_desc' => null, - string если во время звонка произошла ошибка



        $this->send($task, $data);
    }

    public function send($task, $data)
    {


        // ? post 
        $dataSend = [
            'result' => true,
            'message' => $data->attributes,
            'error' => null,
        ];

        try {
            $response = Http::post($task->attributes['callback_url'], $dataSend);
        } catch (\Exception $e) {
            Log::stack([$this->channel])->error('HTTP request failed: ', ['error' => $e->getMessage()]);
            return false;
        }



        Log::stack([$this->channel])->info('Cron Job running CallService send() ', ['$response' => $response]);

        if ($response) {
            $data['attributes']['is_callback_sent'] = true;
        }



        $this->updateTask($task, $data);
    }

    public function updateTask($task, $data)
    {
        $task->attributes = $data['message']['attributes'];
        $task->update();

        Log::stack([$this->channel])->info('Cron Job running CallService update DB updateTask() ', ['id' => $task->id, 'task_id' => $task->task_id]);
    }
}
