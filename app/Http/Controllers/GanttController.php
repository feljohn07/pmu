<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\IndividualProgramOfWork;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GanttController extends Controller
{
    /**
     * Display the Gantt chart view with data for a SPECIFIC project.
     * Accepts the projectId from the updated route.
     *
     * @param  int  $projectId The ID of the project to display
     * @return \Illuminate\Contracts\View\View
     */
    public function showGantt($projectId) // Accept projectId from route
    {
        try {
            // Fetch ONLY the specified project and its related tasks
            // findOrFail will throw an exception if the ID is not found
            $project = Project::with('individualProgramOfWorks')->findOrFail($projectId);

            $ganttData = [];
            $ganttLinks = []; // Keep links array for consistency
            $ganttDateFormat = 'd-m-Y'; // Date format for Gantt display

            // Add the specific project as the root item
            // $ganttData[] = [
            //     'id'         => 'project_' . $project->id,
            //     'text'       => $project->project_name,
            //     'start_date' => Carbon::parse($project->start_date)->format($ganttDateFormat),
            //     'duration'   => $project->duration,
            //     'progress'   => $project->physical_accomplishment / 100, // Convert 0-100 to 0-1
            //     'open'       => true, // Keep the project expanded
            //     'type'       => 'project',
            //     'parent'     => 0
            // ];

            // Add individual program of works (tasks) for THIS project
            foreach ($project->individualProgramOfWorks as $task) {
                $startDateFormatted = $task->start_date ? Carbon::parse($task->start_date)->format($ganttDateFormat) : null;

                $ganttData[] = [
                    'id'         => $task->id, // Use the actual task ID
                    'text'       => $task->work_description ?: $task->item_description ?: 'Task ' . $task->id,
                    'start_date' => $startDateFormatted,
                    'duration'   => $task->duration,
                    'progress'   => $task->progress / 100, // Convert 0-100 to 0-1
                    // 'parent'     => 'project_' . $project->id // Link to the parent project
                ];
            }

            // Prepare the final structure for the view
            $tasks = [
                'data' => $ganttData,
                'links' => $ganttLinks,
            ];

            // Pass the project model and task data to the view
            return view('gantt-chart-example', ['ganttTasks' => $tasks, 'project' => $project]);

        } catch (ModelNotFoundException $e) {
            // Handle case where the project ID doesn't exist
            Log::warning("Gantt chart requested for non-existent project ID: $projectId");
            return view('gantt-chart-example', [
                'ganttTasks' => ['data' => [], 'links' => []],
                'errorMessage' => 'The requested project was not found.'
                ]);

        } catch (Throwable $e) {
            // Handle other potential errors during data fetching
            Log::error("Error fetching data for Gantt chart (Project ID: $projectId): " . $e->getMessage());
            return view('gantt-chart-example', [
                'ganttTasks' => ['data' => [], 'links' => []],
                'errorMessage' => 'Could not load project data due to a server error.'
                ]);
        }
    }

    /**
     * Show the details page for an individual program of work.
     * (No changes needed here from previous version)
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showIndividualPow($id)
    {
        try {
            $task = IndividualProgramOfWork::with('project')->findOrFail($id); // Eager load project
            return view('individual-pow-detail', compact('task'));
        } catch (Throwable $e) {
            Log::error("Error fetching individual POW $id: " . $e->getMessage());
            // Redirect appropriately if task not found
            return redirect()->route('dashboard')->with('error', 'Task not found.'); // Example redirect
        }
    }


    /**
     * Update the progress of an individual program of work.
     * (No changes needed here from previous version)
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  The ID of the IndividualProgramOfWork
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProgress(Request $request, $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'progress' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find and update task
        try {
            $task = IndividualProgramOfWork::findOrFail($id);
            $task->progress = (int) $request->input('progress');
            $task->save();

            // Redirect back to the task detail page
            return redirect()->route('individual-pow.show', ['id' => $task->id])
                       ->with('success', 'Task progress updated successfully!');
        } catch (Throwable $e) {
            Log::error("Error updating progress for task $id: " . $e->getMessage());
            return redirect()->back()
                       ->with('error', 'Failed to update task progress.')
                       ->withInput();
        }
    }
}
