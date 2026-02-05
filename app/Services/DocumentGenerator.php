<?php

namespace App\Services;

use App\Models\Intern;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;

class DocumentGenerator
{
    /**
     * Generate internship approval document PDF
     *
     * @param Intern $intern
     * @return string PDF file path
     */
    public function generateInternshipApprovalDocument(Intern $intern): string
    {
        // Generate PDF from blade template
        $pdf = PDF::loadView('pdf.internship-approval', [
            'intern' => $intern
        ]);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Generate filename
        $filename = 'internship_approval_' . $intern->intern_id . '_' . time() . '.pdf';
        $path = 'internship_documents/' . $filename;

        // Save PDF to storage
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate PDF with dean signature
     *
     * @param Intern $intern
     * @return string PDF file path
     */
    public function generateSignedDocument(Intern $intern): string
    {
        // Regenerate PDF with signature
        return $this->generateInternshipApprovalDocument($intern);
    }
}
