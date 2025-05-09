<?php

namespace App\Jobs;

use App\Models\Call;
use App\Services\CallService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCall implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */



    public function __construct(public Call $call) {}

    /**
     * Execute the job.
     */
    public function handle(CallService $callservice): void
    {
        $channel = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/Call/call.log'),
        ]);
        Log::stack(['single', $channel])->info('Start Cron Job running');

        $callservice->call($this->call);

        Log::stack(['single', $channel])->info('End Cron Job running');
    }
}