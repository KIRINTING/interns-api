<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Services\DocumentGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InternController extends Controller
{
    protected $documentGenerator;

    public function __construct(DocumentGenerator $documentGenerator)
    {
        $this->documentGenerator = $documentGenerator;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Intern::all();
    }

    /**
     * Get internship request by student code
     */
    public function getByStudentCode(Request $request, $student_code)
    {
        $studentCode = $student_code;

        if (!$studentCode) {
            return response()->json([
                'success' => false,
                'message' => 'Student code is required'
            ], 400);
        }

        // Search by both student_code and student_id to handle different data formats
        $intern = Intern::where('student_code', $studentCode)
            ->orWhere('student_id', $studentCode)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$intern) {
            return response()->json([
                'success' => false,
                'message' => 'No internship request found for this student'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $intern
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'intern_id' => 'required|unique:interns',
            // Student Information
            'student_code' => 'required|string',
            'title' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'class_group' => 'required|string',
            'registration_status' => 'required|string',
            // Company Information
            'company_name' => 'required|string',
            'position' => 'required|string',
            'job_description' => 'required|string',
            'company_address' => 'required|string',
            'company_phone' => 'required|string',
            // Coordinator Information
            'coordinator_name' => 'required|string',
            'coordinator_position' => 'required|string',
            'coordinator_phone' => 'required|string',
            // Approver Information
            'approver_name' => 'required|string',
            'approver_position' => 'required|string',
            // Location & Photo
            'google_map_coordinates' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // 5MB max
            // Additional
            'notes' => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('internship_photos', 'public');
            $validated['photo_path'] = $path;
        }

        // Remove 'photo' from validated data as it's not in the database
        unset($validated['photo']);

        // Set initial status
        $validated['status'] = 'pending';

        $intern = Intern::create($validated);

        // Generate PDF document
        try {
            $pdfPath = $this->documentGenerator->generateInternshipApprovalDocument($intern);
            $intern->pdf_path = $pdfPath;
            $intern->save();
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'คำขอฝึกงานถูกบันทึกเรียบร้อยแล้ว',
            'data' => $intern,
            'pdf_url' => $intern->pdf_path ? url('storage/' . $intern->pdf_path) : null
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Intern::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $intern = Intern::findOrFail($id);

        $validated = $request->validate([
            'intern_id' => 'required|unique:interns,intern_id,' . $intern->id,
            // Student Information
            'student_code' => 'sometimes|required|string',
            'title' => 'sometimes|required|string',
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string',
            'class_group' => 'sometimes|required|string',
            'registration_status' => 'sometimes|required|string',
            // Company Information
            'company_name' => 'sometimes|required|string',
            'position' => 'sometimes|required|string',
            'job_description' => 'sometimes|required|string',
            'company_address' => 'sometimes|required|string',
            'company_phone' => 'sometimes|required|string',
            // Coordinator Information
            'coordinator_name' => 'sometimes|required|string',
            'coordinator_position' => 'sometimes|required|string',
            'coordinator_phone' => 'sometimes|required|string',
            // Approver Information
            'approver_name' => 'sometimes|required|string',
            'approver_position' => 'sometimes|required|string',
            // Location & Photo
            'google_map_coordinates' => 'nullable|string',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            // Additional
            'notes' => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($intern->photo_path && Storage::disk('public')->exists($intern->photo_path)) {
                Storage::disk('public')->delete($intern->photo_path);
            }
            $path = $request->file('photo')->store('internship_photos', 'public');
            $validated['photo_path'] = $path;
        }

        // Remove 'photo' from validated data
        unset($validated['photo']);

        $intern->update($validated);

        return $intern;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $intern = Intern::findOrFail($id);

        // Delete photo if exists
        if ($intern->photo_path && Storage::disk('public')->exists($intern->photo_path)) {
            Storage::disk('public')->delete($intern->photo_path);
        }

        $intern->delete();

        return response()->noContent();
    }

    /**
     * Officer approves internship request
     */
    public function approveByOfficer(Request $request, string $id)
    {
        $intern = Intern::findOrFail($id);

        if ($intern->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'คำขอนี้ไม่สามารถอนุมัติได้ในสถานะปัจจุบัน'
            ], 400);
        }

        $intern->status = 'officer_approved';
        $intern->officer_approved_at = now();
        $intern->officer_approved_by = $request->input('officer_id');
        $intern->save();

        return response()->json([
            'success' => true,
            'message' => 'อนุมัติคำขอโดยเจ้าหน้าที่เรียบร้อยแล้ว',
            'data' => $intern
        ]);
    }

    /**
     * Dean approves and signs internship request
     */
    public function approveByDean(Request $request, string $id)
    {
        $intern = Intern::findOrFail($id);

        if ($intern->status !== 'officer_approved') {
            return response()->json([
                'success' => false,
                'message' => 'คำขอนี้ต้องได้รับการอนุมัติจากเจ้าหน้าที่ก่อน'
            ], 400);
        }

        // Handle signature upload if provided
        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('dean_signatures', 'public');
            $intern->dean_signature_path = $path;
        }

        $intern->status = 'dean_approved';
        $intern->dean_approved_at = now();
        $intern->save();

        // Regenerate PDF with signature
        try {
            $pdfPath = $this->documentGenerator->generateSignedDocument($intern);
            $intern->pdf_path = $pdfPath;
            $intern->save();
        } catch (\Exception $e) {
            Log::error('PDF regeneration failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'อนุมัติและเซ็นเอกสารเรียบร้อยแล้ว',
            'data' => $intern,
            'pdf_url' => $intern->pdf_path ? url('storage/' . $intern->pdf_path) : null
        ]);
    }

    /**
     * Reject internship request
     */
    public function reject(Request $request, string $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string'
        ]);

        $intern = Intern::findOrFail($id);

        $intern->status = 'rejected';
        $intern->rejection_reason = $validated['reason'];
        $intern->save();

        return response()->json([
            'success' => true,
            'message' => 'ปฏิเสธคำขอเรียบร้อยแล้ว',
            'data' => $intern
        ]);
    }

    /**
     * Download PDF document
     */
    public function downloadPdf(string $id)
    {
        $intern = Intern::findOrFail($id);

        // Always regenerate to ensure latest data and fonts
        try {
            $pdfPath = $this->documentGenerator->generateInternshipApprovalDocument($intern);
            $intern->pdf_path = $pdfPath;
            $intern->save();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถสร้างเอกสาร PDF ได้: ' . $e->getMessage()
            ], 500);
        }

        return Storage::disk('public')->download($intern->pdf_path, 'internship_approval_' . $intern->intern_id . '.pdf');
    }

    /**
     * Submit training evidence with hours calculation
     */
    public function submitTrainingEvidence(Request $request, string $id)
    {
        $validated = $request->validate([
            'total_training_hours' => 'required|numeric|min:0|max:1000',
            'absence_days' => 'required|integer|min:0|max:365',
            'leave_days' => 'required|integer|min:0|max:365',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $intern = Intern::findOrFail($id);

        // Calculate total hours: total_training_hours - (absence_days * 8) - (leave_days * 8)
        // Assuming 8 hours per day for absence and leave
        $hoursPerDay = 8;
        $calculatedHours = $validated['total_training_hours']
            - ($validated['absence_days'] * $hoursPerDay)
            - ($validated['leave_days'] * $hoursPerDay);

        // Determine pass/fail status
        // Required: 80% of 560 hours = 448 hours minimum
        $requiredHours = 448;
        $trainingStatus = $calculatedHours >= $requiredHours ? 'passed' : 'failed';

        // Update intern record
        $intern->total_training_hours = $validated['total_training_hours'];
        $intern->absence_days = $validated['absence_days'];
        $intern->leave_days = $validated['leave_days'];
        $intern->calculated_hours = $calculatedHours;
        $intern->training_status = $trainingStatus;
        $intern->evidence_submitted_at = now();

        if (isset($validated['start_date'])) {
            $intern->start_date = $validated['start_date'];
        }
        if (isset($validated['end_date'])) {
            $intern->end_date = $validated['end_date'];
        }

        $intern->save();

        return response()->json([
            'success' => true,
            'message' => 'บันทึกหลักฐานการฝึกงานเรียบร้อยแล้ว',
            'data' => [
                'intern' => $intern,
                'calculation' => [
                    'total_training_hours' => $validated['total_training_hours'],
                    'absence_hours' => $validated['absence_days'] * $hoursPerDay,
                    'leave_hours' => $validated['leave_days'] * $hoursPerDay,
                    'calculated_hours' => $calculatedHours,
                    'required_hours' => $requiredHours,
                    'training_status' => $trainingStatus,
                    'percentage' => round(($calculatedHours / 560) * 100, 2)
                ]
            ]
        ]);
    }

    /**
     * Get training evidence data
     */
    public function getTrainingEvidence(string $id)
    {
        $intern = Intern::findOrFail($id);

        $hoursPerDay = 8;
        $requiredHours = 448;
        $totalRequiredHours = 560;

        return response()->json([
            'success' => true,
            'data' => [
                'total_training_hours' => $intern->total_training_hours,
                'absence_days' => $intern->absence_days,
                'leave_days' => $intern->leave_days,
                'calculated_hours' => $intern->calculated_hours,
                'training_status' => $intern->training_status,
                'start_date' => $intern->start_date,
                'end_date' => $intern->end_date,
                'evidence_submitted_at' => $intern->evidence_submitted_at,
                'calculation' => [
                    'absence_hours' => $intern->absence_days * $hoursPerDay,
                    'leave_hours' => $intern->leave_days * $hoursPerDay,
                    'required_hours' => $requiredHours,
                    'total_required_hours' => $totalRequiredHours,
                    'percentage' => $intern->calculated_hours ? round(($intern->calculated_hours / $totalRequiredHours) * 100, 2) : 0
                ]
            ]
        ]);
    }

    /**
     * Submit final report and presentation
     */
    public function submitReport(Request $request, string $id)
    {
        $request->validate([
            'final_report' => 'nullable|file|mimes:pdf,doc,docx|max:20480', // 20MB
            'presentation' => 'nullable|file|mimes:pdf,ppt,pptx|max:20480', // 20MB
        ]);

        $intern = Intern::findOrFail($id);
        $updated = false;

        if ($request->hasFile('final_report')) {
            // Delete old file if exists
            if ($intern->final_report_path && Storage::disk('public')->exists($intern->final_report_path)) {
                Storage::disk('public')->delete($intern->final_report_path);
            }
            $path = $request->file('final_report')->store('final_reports', 'public');
            $intern->final_report_path = $path;
            $updated = true;
        }

        if ($request->hasFile('presentation')) {
            // Delete old file if exists
            if ($intern->presentation_path && Storage::disk('public')->exists($intern->presentation_path)) {
                Storage::disk('public')->delete($intern->presentation_path);
            }
            $path = $request->file('presentation')->store('presentations', 'public');
            $intern->presentation_path = $path;
            $updated = true;
        }

        if ($updated) {
            $intern->report_submitted_at = now();
            $intern->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกรายงานเรียบร้อยแล้ว',
            'data' => $intern
        ]);
    }
}
