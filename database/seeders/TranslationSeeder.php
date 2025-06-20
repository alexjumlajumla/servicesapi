<?php

namespace Database\Seeders;

use App\Traits\Loggable;
use Illuminate\Database\Seeder;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
use Throwable;

class TranslationSeeder extends Seeder
{
    use Loggable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $host = env('DB_HOST');
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');
        $fpath = resource_path('lang/translations_en.sql');

        $command = "mysql --force -h$host -u$user -p$password $database < $fpath";

        info("running: $command");

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(null); 

        try {
            $process->run();

            if ($process->isSuccessful()) {
                Log::info('SQL import completed successfully.');
                Log::info($process->getOutput());
            } else {
                Log::warning('SQL import completed with warnings.');
                Log::warning($process->getErrorOutput());
            }
        } catch (Throwable $e) {
            Log::error('Error during SQL import: ' . $e->getMessage());
        }
    }
}
