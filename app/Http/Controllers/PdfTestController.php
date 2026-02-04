<?php

namespace App\Http\Controllers;

use App\Http\Utils\PdfHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PdfTestController extends Controller
{
    /**
     * Show the HTML to PDF test form.
     */
    public function index(): View
    {
        return view('pdf-test.index');
    }

    /**
     * Generate PDF from uploaded HTML file and display preview.
     */
    public function generatePdf(Request $request)
    {
        $request->validate([
            'html_file' => 'required|file|mimes:html,htm,txt',
            'orientation' => 'required|in:portrait,landscape',
            'page_size' => 'required|in:A3,A4,A5,Letter,Legal',
        ]);

        // Store the uploaded HTML file
        $htmlFilePath = $request->file('html_file')->store('temp');

        // Generate a unique filename for the PDF
        $pdfFilePath = 'temp/pdf_' . time() . '.pdf';

        // Generate PDF from HTML
        $success = PdfHelper::generatePurePdf($htmlFilePath, $pdfFilePath, $request->orientation, $request->page_size);

        if (!$success) {
            return back()->with('error', 'Failed to generate PDF');
        }

        // Return the PDF file as a response
        return Storage::disk('local')->response($pdfFilePath);
    }
}
