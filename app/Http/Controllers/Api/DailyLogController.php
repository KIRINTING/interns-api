<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyWorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DailyLogController extends Controller
{
    /**
     * Get all logs for a student
     */
    public function index(Request $request)
    {
        $studentCode = $request->query('student_code');

        if (!$studentCode) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาระบุรหัสนักศึกษา'
            ], 400);
        }

        $logs = DailyWorkLog::where('student_code', $studentCode)
            ->orderBy('log_date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'log_date' => $log->log_date->format('Y-m-d'),
                    'work_description' => $log->work_description,
                    'hours_worked' => $log->hours_worked,
                    'is_weekend' => $log->is_weekend,
                    'photo_url' => $log->photo_url,
                    'created_at' => $log->created_at
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Create a new daily log
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_code' => 'required|string',
            'log_date' => 'required|date',
            'work_description' => 'required|string|min:10',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if log already exists for this date
        $existingLog = DailyWorkLog::where('student_code', $request->student_code)
            ->where('log_date', $request->log_date)
            ->first();

        if ($existingLog) {
            return response()->json([
                'success' => false,
                'message' => 'มีการบันทึกสำหรับวันนี้แล้ว กรุณาแก้ไขรายการเดิม'
            ], 409);
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('daily-logs', 'public');
        }

        // Determine if weekend
        $date = Carbon::parse($request->log_date);
        $isWeekend = $date->isWeekend();

        $log = DailyWorkLog::create([
            'student_code' => $request->student_code,
            'intern_id' => $request->intern_id,
            'log_date' => $request->log_date,
            'work_description' => $request->work_description,
            'hours_worked' => $request->hours_worked,
            'is_weekend' => $isWeekend,
            'photo_path' => $photoPath
        ]);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกรายการทำงานสำเร็จ',
            'data' => [
                'id' => $log->id,
                'log_date' => $log->log_date->format('Y-m-d'),
                'work_description' => $log->work_description,
                'hours_worked' => $log->hours_worked,
                'is_weekend' => $log->is_weekend,
                'photo_url' => $log->photo_url
            ]
        ], 201);
    }

    /**
     * Get hours summary
     */
    public function summary(Request $request)
    {
        $studentCode = $request->query('student_code');

        if (!$studentCode) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาระบุรหัสนักศึกษา'
            ], 400);
        }

        $logs = DailyWorkLog::where('student_code', $studentCode)->get();

        $weekdayHours = $logs->where('is_weekend', false)->sum('hours_worked');
        $weekendHours = $logs->where('is_weekend', true)->sum('hours_worked');
        $totalHours = $weekdayHours + $weekendHours;

        // Required hours (example: 320 weekday, 80 weekend)
        $requiredWeekdayHours = 320;
        $requiredWeekendHours = 80;
        $requiredTotalHours = 400;

        return response()->json([
            'success' => true,
            'data' => [
                'weekday_hours' => round($weekdayHours, 2),
                'weekend_hours' => round($weekendHours, 2),
                'total_hours' => round($totalHours, 2),
                'required_weekday_hours' => $requiredWeekdayHours,
                'required_weekend_hours' => $requiredWeekendHours,
                'required_total_hours' => $requiredTotalHours,
                'weekday_percentage' => round(($weekdayHours / $requiredWeekdayHours) * 100, 2),
                'weekend_percentage' => round(($weekendHours / $requiredWeekendHours) * 100, 2),
                'total_percentage' => round(($totalHours / $requiredTotalHours) * 100, 2),
                'total_days_logged' => $logs->count()
            ]
        ]);
    }

    /**
     * Update a log
     */
    public function update(Request $request, $id)
    {
        $log = DailyWorkLog::find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบรายการที่ต้องการแก้ไข'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'work_description' => 'sometimes|string|min:10',
            'hours_worked' => 'sometimes|numeric|min:0.5|max:24',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('work_description')) {
            $log->work_description = $request->work_description;
        }

        if ($request->has('hours_worked')) {
            $log->hours_worked = $request->hours_worked;
        }

        // Handle new photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($log->photo_path) {
                Storage::delete($log->photo_path);
            }
            $log->photo_path = $request->file('photo')->store('daily-logs', 'public');
        }

        $log->save();

        return response()->json([
            'success' => true,
            'message' => 'แก้ไขรายการสำเร็จ',
            'data' => [
                'id' => $log->id,
                'log_date' => $log->log_date->format('Y-m-d'),
                'work_description' => $log->work_description,
                'hours_worked' => $log->hours_worked,
                'photo_url' => $log->photo_url
            ]
        ]);
    }

    /**
     * Delete a log
     */
    public function destroy($id)
    {
        $log = DailyWorkLog::find($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบรายการที่ต้องการลบ'
            ], 404);
        }

        $log->delete(); // Photo will be auto-deleted via model boot method

        return response()->json([
            'success' => true,
            'message' => 'ลบรายการสำเร็จ'
        ]);
    }
}
