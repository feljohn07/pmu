{{-- <x-app-layout>
    <div id="gantt_here" style="width:100%; height:500px;"></div>

    <script>

        // gantt.config.readonly = true;

        gantt.config.columns = [
            { name: "text", label: "Your Custom Title", tree: true, width: "*" },
            { name: "start_date", label: "Start Date", align: "center" },
            { name: "duration", label: "Duration", align: "center" },
            { name: "add", label: "", width: 44 }
        ];

        var tasks = {
            data: [
                { id: -1, text: "Project #1", start_date: "01-04-2025", duration: 5, progress: 0.6, open: true, type: 'project' },
                { id: 2, text: "Task #1 (40%)", start_date: null, duration: 0, progress: 0.4, },
                { id: 3, text: "Task #2", start_date: "05-04-2025", duration: 2, progress: 0.8, parent: -1 }
            ],
            links: [
                { id: 1, source: -1, target: 2, type: "1" },
                { id: 2, source: 2, target: 3, type: "0" }
            ]
        };

        gantt.init("gantt_here");
        gantt.parse(tasks);

        gantt.attachEvent("onTaskClick", function (id, e) {
            var task = gantt.getTask(id);
            if (task.type === gantt.config.types.project) {
                // Redirect to the project page
                window.location.href = "/project-form/" + id;
            } else {
                // Redirect to the task page
                window.location.href = "/individual-pow/" + id;
            }
            return false;
        });
    </script>
</x-app-layout> --}}

<x-app-layout>
    {{-- Set the header title using the project name --}}
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @isset($project)
            {{ __('Gantt Chart: ') }} {{ $project->project_name }}
            @else
            {{ __('Gantt Chart') }}
            @endisset
        </h2>
    </x-slot> --}}
    <a href="{{ route('project-view', ['id' => $project->id]) }}"
        class="ms-5 mt-3 inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        ← Back to Project
    </a>
    {{-- Include dhtmlxGantt CSS and JS --}}
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css" type="text/css">
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div>
        {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> --}}
            {{-- Display Error Message if passed from controller --}}
            {{-- @isset($errorMessage)
            <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-400 rounded">
                <strong>Error:</strong> {{ $errorMessage }}
            </div>
            @endisset --}}

            {{-- Link back to the main project details page --}}
            @isset($project)
                <div class="mb-4">
                    {{-- <a href="{{ route('project.show', ['id' => $project->id]) }}"
                        class="text-blue-600 hover:underline">
                        &larr; Back to Project Details
                    </a> --}}
                </div>
            @endisset
            {{-- <div style="text-align: center; margin-bottom: 10px;">
                <button onclick="zoomToFit()">Zoom to Fit</button>
            </div> --}}
            {{-- Gantt chart container --}}
            <div id="gantt_here" style="width:100%; height:600px;"></div>
            {{--
        </div> --}}
    </div>


    <script>
        // Configure Gantt (read-only)
        // gantt.config.readonly = true;
        gantt.config.date_format = "%d-%m-%Y"; // Display format
        gantt.config.details_on_dblclick = false;
        

        // Define columns
        gantt.config.columns = [
            { name: "text", label: "Task Description", tree: true, width: '*' }, // More specific label
            { name: "start_date", label: "Start Date", align: "center", width: 100 },
            { name: "duration", label: "Duration", align: "center", width: 80 },
            {
                name: "progress", label: "Progress", align: "center", width: 90, template: function (task) {
                    // Display progress as percentage
                    return task.progress ? Math.round(task.progress * 100) + "%" : "0%";
                }
            },
        ];

        // Configure timescale
        gantt.config.scales = [
            { unit: "month", step: 1, format: "%F, %Y" },
            { unit: "day", step: 1, format: "%j, %D" } // Day of year, Day of week
        ];
        gantt.config.scale_height = 50;
        gantt.config.fit_tasks = true; // Adjust timescale to fit all tasks
        // Load data passed from the controller
        var tasks = @json($ganttTasks);

        gantt.plugins({
            fullscreen: true
        });
        // Initialize Gantt
        gantt.init("gantt_here");


        // Check if data exists before parsing
        if (tasks && tasks.data && tasks.data.length > 0) {
            gantt.parse(tasks);
        }

        gantt.attachEvent("onTaskDrag", function (id, mode, task, original, e) {
            if (mode === "progress") {
                console.log("Progress updated for task:", id, "New progress:", task.progress);
                // Implement additional logic here, such as saving changes to a server
            }
        });

        let clickTimeout = null;

        gantt.attachEvent("onTaskClick", function (id, e) {
            // if (clickTimeout) {
            //     clearTimeout(clickTimeout);
            //     clickTimeout = null;
            //     return;
            // }

            // clickTimeout = setTimeout(function () {
            //     clickTimeout = null;
            //     console.log("Single click on task:", id);
            //     // Implement single-click behavior here
            // }, 300); // Adjust the delay as needed
            const task = gantt.getTask(id);
            const currentProgress = task.progress ? Math.round(task.progress * 100) : 0;
            const input = prompt(`Update progress for "${task.text}" (0–100):`, currentProgress);

            if (input === null) {
                // User canceled the prompt
                return false;
            }

            const newProgress = parseInt(input, 10);
            if (isNaN(newProgress) || newProgress < 0 || newProgress > 100) {
                alert("Please enter a valid number between 0 and 100.");
                return false;
            }

            task.progress = newProgress / 100;
            gantt.updateTask(id); // Refresh the task to reflect changes
            return false; // Prevent default lightbox from opening
        });

        gantt.attachEvent("onTaskDblClick", function (id, e) {
            if (clickTimeout) {
                clearTimeout(clickTimeout);
                clickTimeout = null;
            }
            console.log("Double click on task:", id);
            // Implement double-click behavior here
            return true;
        });

        function zoomToFit() {
            var project = gantt.getSubtaskDates();
            if (!project.start_date || !project.end_date) return;

            gantt.config.start_date = gantt.date.add(project.start_date, -1, "day");
            gantt.config.end_date = gantt.date.add(project.end_date, 1, "day");

            gantt.render();
        }

    </script>
</x-app-layout>