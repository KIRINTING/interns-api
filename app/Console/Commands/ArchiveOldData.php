<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Intern;
use App\Models\DailyLog;
use Illuminate\Support\Facades\DB;

class ArchiveOldData extends Command
{
    protected $signature = 'data:archive {--years=2 : The number of years to keep data} {--force : Force deletion without confirmation}';

    protected $description = 'Archive and delete data older than specified years (default 2)';

    public function handle()
    {
        $years = $this->option('years');
        $force = $this->option('force');
        $cutoffDate = Carbon::now()->subYears($years);

        $this->info("Archiving data older than {$years} years (before {$cutoffDate->toDateString()})...");

        // Fetch data
        $students = Student::where('created_at', '<', $cutoffDate)->get();
        $interns = Intern::where('created_at', '<', $cutoffDate)->get();
        // Assuming logs use 'date' column, assessments use 'created_at' or 'evaluation_date'
        $dailyLogs = DailyLog::where('date', '<', $cutoffDate)->get();
        $assessments = \App\Models\Assessment::where('created_at', '<', $cutoffDate)->get();

        if ($students->count() === 0 && $interns->count() === 0 && $dailyLogs->count() === 0 && $assessments->count() === 0) {
            $this->info('No old data found to archive.');
            return;
        }

        $archiveData = [
            'meta' => [
                'archived_at' => Carbon::now()->toDateTimeString(),
                'cutoff_date' => $cutoffDate->toDateString(),
                'counts' => [
                    'students' => $students->count(),
                    'interns' => $interns->count(),
                    'daily_logs' => $dailyLogs->count(),
                    'assessments' => $assessments->count(),
                ]
            ],
            'data' => [
                'students' => $students->toArray(),
                'interns' => $interns->toArray(),
                'daily_logs' => $dailyLogs->toArray(),
                'assessments' => $assessments->toArray(),
            ]
        ];

        // Save to JSON
        $filename = 'archive_' . Carbon::now()->format('Y_m_d_H_i_s') . '.json';
        Storage::disk('local')->put('archives/' . $filename, json_encode($archiveData, JSON_PRETTY_PRINT));

        $this->info("Data archived to storage/app/archives/{$filename}");

        // Delete data
        if ($force || $this->confirm('Do you want to delete these records from the database?', true)) {
            DB::transaction(function () use ($students, $interns, $dailyLogs, $assessments) {
                // Delete related data first
                \App\Models\Assessment::whereIn('id', $assessments->pluck('id'))->delete();
                DailyLog::whereIn('id', $dailyLogs->pluck('id'))->delete();
                Intern::whereIn('id', $interns->pluck('id'))->delete();
                Student::whereIn('id', $students->pluck('id'))->delete();
            });
            $this->info('Old data deleted successfully.');
        } else {
            $this->info('Data archived but NOT deleted.');
        }
    }
}
