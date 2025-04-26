<?php

namespace App\Http\Controllers;

use App\Models\AccomplishmentReport; // Import the model
use App\Models\DocumentationUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View; 
use Storage;// Import View

class AccomplishmentReportController extends Controller
{
    /**
     * Display the documentation for a specific accomplishment report.
     *
     * Uses route model binding to inject the AccomplishmentReport instance.
     */
    public function showDocumentation(AccomplishmentReport $report): View // Type hint the model
    {
        // Eager load the associated documentation uploads
        // This helps prevent the N+1 query problem in the view
        $report->load('documentationUploads');

        // Pass the report data (including loaded uploads) to the view
        return view('accomplishments.documentation', compact('report')); // Pass the report variable
    }

    /**
     * Toggle the approval status of a specific documentation upload.
     *
     * Uses route model binding to inject the DocumentationUpload instance.
     */
    public function toggleApproval(DocumentationUpload $upload): RedirectResponse // Type hint the model
    {
        try {
            // Toggle the boolean 'approved' status
            $upload->approved = !$upload->approved;
            $upload->save();

            // Prepare success message
            $message = $upload->approved ? 'Document approved successfully.' : 'Document approval revoked.';

            // Redirect back to the documentation page it came from
            // We need the report ID to reconstruct the previous URL
            return redirect()->route('accomplishment.documentation.show', ['report' => $upload->accomplishment_report_id])
                ->with('status', $message); // Flash a success message

        } catch (\Exception $e) {
            Log::error("Error toggling approval for upload ID {$upload->id}: " . $e->getMessage()); // Optional logging

            // Redirect back with an error message
            return redirect()->route('accomplishment.documentation.show', ['report' => $upload->accomplishment_report_id])
                ->with('error', 'Failed to update approval status. Please try again.');
        }
    }

    /**
     * Remove the specified documentation upload from storage and database.
     *
     * Uses route model binding to inject the DocumentationUpload instance.
     */
    public function destroyDocumentation(DocumentationUpload $upload): RedirectResponse // Type hint the model
    {
        // Store the report ID before deleting the upload object, needed for redirect
        $reportId = $upload->accomplishment_report_id;
        $filePath = $upload->url; // Get the file path stored in the DB

        try {
            // 1. Delete the physical file from storage
            // Ensure you use the correct disk (e.g., 'public')
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            } else {
                // Optional: Log if the file was already missing
                Log::warning("File not found in storage during delete: " . $filePath);
            }

            // 2. Delete the database record
            $upload->delete();

            // Prepare success message
            $message = 'Document deleted successfully.';

            // Redirect back to the documentation page
            return redirect()->route('accomplishment.documentation.show', ['report' => $reportId])
                ->with('status', $message); // Flash success message

        } catch (\Exception $e) {
            Log::error("Error deleting documentation upload ID {$upload->id}: " . $e->getMessage());

            // Redirect back with an error message
            return redirect()->route('accomplishment.documentation.show', ['report' => $reportId])
                ->with('error', 'Failed to delete document. Please try again.');
        }
    }

}