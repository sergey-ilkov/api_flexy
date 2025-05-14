<?php



use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;




// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Artisan::command('logs:clear', function () {
    exec('rm -f ' . storage_path('logs/*.log'));
    exec('rm -f ' . storage_path('logs/Call/*.log'));
    $this->comment('Logs have been cleared!');
})->describe('Clear log files');
