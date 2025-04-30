<div class="m-auto">
    <h1 class="text-center mt-10 text-4xl">Project Monitoring and Evaluation</h1>

    <div class="flex justify-center items-center bg-gray-100 mt-10">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-chart-card completed="{{  $constructionsChartData['completed'] ?? 0 }}"
                ongoing="{{  $constructionsChartData['ongoing'] ?? 0 }}"
                pending="{{  $constructionsChartData['pending'] ?? 0 }}" chart-name="Construction Projects" />
            <x-chart-card completed="{{  $repairsChartData['completed'] ?? 0 }}"
                ongoing="{{  $repairsChartData['ongoing'] ?? 0 }}" pending="{{  $repairsChartData['pending'] ?? 0 }}"
                chart-name="Repair Projects" />
            <x-chart-card completed="{{  $fabricationsChartData['completed'] ?? 0 }}"
                ongoing="{{  $fabricationsChartData['ongoing'] ?? 0 }}"
                pending="{{  $fabricationsChartData['pending'] ?? 0 }}" chart-name="Fabrication Projects" />

        </div>


    </div>
    <x-mary-card class="mt-10 mx-10">

        <p>Latest Projects</p>
        <!-- Table Section -->
        <div class="overflow-x-auto mt-10">
            <table class="table w-full">
                <!-- Table Head -->
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Date Start</th>
                        <th>Duration</th>
                        <th>Date End</th>
                        <th>Status</th>
                        {{-- <th>Balance</th> --}}
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    @foreach ($latestProjects as $project)
                        <tr>
                            <td>{{ $project['project_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($project['start_date'])?->format('F j, Y') ?? 'No Date' }}</td>
                            <td>{{ $project['duration'] }} {{ $project['duration'] > 1 ? 'days' : 'day' }} </td>
                            <td>{{ \Carbon\Carbon::parse($project->calculatedEndDate())?->format('F j, Y')  }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{$project->checkProjectStatus() === 'Completed' ? 'bg-green-100' :  'bg-yellow-100'}} text-green-800">
                                    {{ $project->checkProjectStatus() }}
                                </span>
                            </td>


                        </tr>
                    @endforeach
                    {{-- <tr>
                        <td>Project 1</td>
                        <td>For Approval</td>
                    </tr>
                    <tr>
                        <td>Project 2</td>
                        <td>Approved</td>
                    </tr>

                    <tr>
                        <td>Project 3</td>
                        <td>For Approval</td>
                    </tr> --}}

                </tbody>
            </table>
        </div>
    </x-mary-card>
</div>