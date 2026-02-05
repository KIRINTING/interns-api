<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Get list of interns with their progress
     */
    public function index(Request $request)
    {
        // Filter mainly for approved interns or all
        // Filter - showing ALL interns for now to ensure data visibility
        // $interns = Intern::whereIn('status', ['officer_approved', 'dean_approved']) 
        $interns = Intern::query()
            ->with([
                'logs' => function ($query) {
                    // We could just select light fields if performance is issue
                    $query->select('student_code', 'hours_worked', 'is_weekend');
                }
            ])
            ->latest()
            ->get()
            ->map(function ($intern) {
                $totalHours = $intern->logs->sum('hours_worked');

                return [
                    'id' => $intern->id,
                    'student_code' => $intern->student_code,
                    'full_name' => "{$intern->title}{$intern->first_name} {$intern->last_name}",
                    'company_name' => $intern->company_name,
                    'total_hours' => round($totalHours, 2),
                    'required_hours' => 400, // Example config
                    'progress_percent' => min(100, round(($totalHours / 400) * 100, 1)),
                    'status' => $intern->status
                ];
            });

        \Illuminate\Support\Facades\Log::info('MonitorController Index: Found ' . $interns->count() . ' interns.');

        return response()->json($interns);
    }

    /**
     * Get detailed monitoring info for a specific intern
     */
    public function show($id)
    {
        $intern = Intern::with([
            'logs' => function ($query) {
                $query->orderBy('log_date', 'desc');
            }
        ])->findOrFail($id);

        $logs = $intern->logs;
        $weekdayHours = $logs->where('is_weekend', false)->sum('hours_worked');
        $weekendHours = $logs->where('is_weekend', true)->sum('hours_worked');

        // Extract photos from logs
        $photos = $logs->whereNotNull('photo_path')->map(function ($log) {
            return [
                'id' => $log->id,
                'url' => $log->photo_url,
                'date' => $log->log_date->format('Y-m-d'),
                'description' => $log->work_description
            ];
        })->values();

        // Calculate days metrics
        $uniqueWorkDays = $logs->pluck('log_date')->unique()->count();
        // Assuming absence/leave are stored in intern record or calculated
        $daysAbsence = $intern->absence_days ?? 0;
        $daysLeave = $intern->leave_days ?? 0;

        // Format logs for display
        $formattedLogs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'date' => $log->log_date->format('Y-m-d'),
                'hours' => $log->hours_worked,
                'desc' => $log->work_description,
                'is_weekend' => $log->is_weekend,
                'photo' => $log->photo_url
            ];
        })->values();

        return response()->json([
            'intern' => [
                'id' => $intern->id,
                'student_code' => $intern->student_code,
                'full_name' => "{$intern->title}{$intern->first_name} {$intern->last_name}",
                'company_name' => $intern->company_name,
                'position' => $intern->position,
                'start_date' => optional($intern->start_date)->format('Y-m-d'),
                'end_date' => optional($intern->end_date)->format('Y-m-d'),
            ],
            'hours' => [
                'weekday' => round($weekdayHours, 2),
                'weekend' => round($weekendHours, 2),
                'total' => round($weekdayHours + $weekendHours, 2),
                'days_worked' => $uniqueWorkDays,
                'days_absence' => $daysAbsence,
                'days_leave' => $daysLeave
            ],
            'photos' => $photos,
            'logs' => $formattedLogs
        ]);
    }
}
