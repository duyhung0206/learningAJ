<?php
namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take a backup of the entire DB and upload to S3.';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('backup - db');
        $date = Carbon::now()->format('Y-m-d_h-i');
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        $command = "mysqldump --user={$user} -p{$password} {$database} > {$date}.sql";
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $local = Storage::disk('local');
        $local->put('gallery-app-db/' . $date . ".sql", file_get_contents("{$date}.sql"), 'public');
        unlink("{$date}.sql");
        // while ($process->isRunning()) {
        //     $local = Storage::disk('local');
        //     $local->put('gallery-app-db/' . $date . ".sql", file_get_contents("{$date}.sql"), 'public');
        //     unlink("{$date}.sql");
        // }
        echo $process->getOutput();
    }
}