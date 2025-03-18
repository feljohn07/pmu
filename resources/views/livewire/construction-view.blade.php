<div class="mx-20 mt-10">

    <p>University Gymnasium and Cultural Center</p>

    <br>
    <div class="flex flex-wrap gap-6 justify-center lg:justify-start">
        <x-mary-card class="w-full">
            <p>Monthly Report</p>
            <canvas id="weeklyAccomplishmentChart"></canvas>
        </x-mary-card>

        <x-mary-card class="w-full md:w-1/2 lg:w-1/3">
            <p>Project Duration</p>
            <div class="relative mt-6 w-full max-w-[400px] h-[200px] mx-auto">
                <canvas id="gaugeChart"></canvas>
                <div class="absolute top-[150px] left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                    <p class="text-lg font-semibold text-gray-700">Number of Days</p>
                    <p id="days-count" class="text-3xl font-bold text-gray-900">{{ 1 }} of 12</p>
                </div>
            </div>
        </x-mary-card>

        <x-mary-card class="w-full md:w-1/2 lg:w-1/3">
            <p class="mb-2">Financial Accomplishment</p>
            <div class="relative w-full max-w-[400px] h-[200px] mx-auto">
                <canvas id="financialAccPercentChart"></canvas>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                    <p id="days-count" class="text-3xl font-bold text-gray-900">40%</p>
                </div>
            </div>
            <p class="mt-2 mb-2">Financial Allocation: P 178,112,318.02</p>
            <p class="mb-2">Financial Used: P 123,123,123.00</p>
        </x-mary-card>
    </div>


    <x-mary-card class="mt-10">

        <div class="flex items-center justify-between w-full">
            <p>Project Details</p>
            <div>
                <x-mary-button label="Photo Documentation" class="ms-10 me-1" />
                <x-mary-button label="Scanned POW" />
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto mt-10">
            <table class="table w-full">
                <!-- Table Head -->
                <thead>
                    <tr>
                        <th>Code No.</th>
                        <th>Construction Project</th>
                        <th>Material Cost</th>
                        <th>Labor Cost</th>
                        <th>Total Contract Amount</th>
                        <th>POW Status</th>
                        <th>Physical Accomplishment</th>
                        <th>Duration</th>
                        <th>Implementation Status</th>
                        <th>Remarks</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    @foreach($projects as $project)
                        <tr onclick="window.location.href='{{ route('dashboard', ['id' => $project['code_no']]) }}'"
                            class="cursor-pointer hover:bg-gray-100">
                            <td>{{ $project['code_no'] }}</td>
                            <td>{{ $project['project_name'] }}</td>
                            <td>{{ number_format($project['material_cost'], 2) }}</td>
                            <td>{{ number_format($project['labor_cost'], 2) }}</td>
                            <td>{{ number_format($project['total_contract'], 2) }}</td>
                            <td>{{ $project['pow_status'] }}</td>
                            <td>{{ $project['physical_accomplishment'] }}</td>
                            <td>{{ $project['duration'] }}</td>
                            <td>{{ $project['implementation_status'] }}</td>
                            <td>{{ $project['remarks'] }}</td>
                            <td>
                                @if($project['url'])
                                    <a href="{{ $project['url'] }}" class="text-blue-500 underline" target="_blank">View</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-mary-card>

    {{-- Accomplishment Report --}}
    <x-mary-card class="mt-10">

        <div class="flex items-center justify-between w-full">
            <p>Accomplishment Report</p>
            <div>
                <x-mary-button label="Add Report" />
            </div>
        </div>


        <!-- Table Section -->
        <div class="overflow-x-auto mt-10">
            <table class="table w-full">
                <!-- Table Head -->
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Planned Accomplishment</th>
                        <th>Actual Accomplishment</th>
                        <th>Variance</th>
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    <tr>
                        <td>January</td>
                        <td>10%</td>
                        <td>9%</td>
                        <td>-1%</td>
                    </tr>
                    <tr>
                        <td>Febuary</td>
                        <td>10%</td>
                        <td>11%</td>
                        <td>1%</td>
                    </tr>

                    <tr>
                        <td>March</td>
                        <td>80%</td>
                        <td>80%</td>
                        <td>0%</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>100%</td>
                        <td>100%</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-mary-card>

    {{-- Financial Accomplishment --}}

    <x-mary-card class="mt-10">

        <div class="flex items-center justify-between w-full">
            <p>Financial Report</p>
            <div>
                <x-mary-button label="Add Report" />
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto mt-10">
            <table class="table w-full">
                <!-- Table Head -->
                <thead>
                    <tr>
                        <th>Date/Month</th>
                        <th>Amount Used</th>
                        {{-- <th>Balance</th> --}}
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    <tr>
                        <td>January</td>
                        <td>123,123,123.00</td>
                    </tr>
                    <tr>
                        <td>Febuary</td>
                        <td>123,123,123.00</td>
                    </tr>

                    <tr>
                        <td>March</td>
                        <td>123,123,123.00</td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>123,123,123.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-mary-card>

    <script>
        // const ctx = document.getElementById('accomplishmentChart').getContext('2d');

        // const data = @json($weeks);

        // const labels = data.map(item => item.week);
        // const planned = data.map(item => item.planned);
        // const actual = data.map(item => item.actual);
        // const variance = data.map(item => item.variance);

        // new Chart(ctx, {
        //     type: 'bar',
        //     data: {
        //         labels: labels,
        //         datasets: [
        //             {
        //                 label: 'Planned Accomplishment',
        //                 data: planned,
        //                 backgroundColor: 'rgba(255, 159, 64, 0.8)',
        //             },
        //             {
        //                 label: 'Actual Accomplishment',
        //                 data: actual,
        //                 backgroundColor: 'rgba(54, 162, 235, 0.8)',
        //             },
        //             {
        //                 label: 'Variance',
        //                 data: variance,
        //                 backgroundColor: 'rgba(75, 192, 192, 0.8)',
        //             }
        //         ]
        //     },
        //     options: {
        //         responsive: true,
        //         scales: {
        //             x: { stacked: true },
        //             y: { stacked: true }
        //         }
        //     }
        // });



        const ctx = document.getElementById('weeklyAccomplishmentChart').getContext('2d');

        const data = @json($weeks);

        const labels = data.map(item => item.week);
        const cumulative = data.map(item => item.cumulative);
        const variance = data.map(item => item.variance);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Cumulative Accomplishment',
                        data: cumulative,
                        backgroundColor: 'rgba(255, 140, 0, 0.8)',
                        borderColor: 'rgba(255, 140, 0, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Variance',
                        data: variance,
                        backgroundColor: 'rgba(0, 128, 128, 0.8)',
                        borderColor: 'rgba(0, 128, 128, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                indexAxis: 'y', // Horizontal bar
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { stacked: true }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });




        const maxDays = {{ 12 }};
        const currentDays = {{ 1 }};

        const gaugectx = document.getElementById('gaugeChart').getContext('2d');
        new Chart(gaugectx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [currentDays, maxDays - currentDays],
                    backgroundColor: ['#15803d', '#f59e0b'], // Green, Gray, Orange
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                rotation: -90,
                circumference: 180,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        const financialAccPercent = {{ 40 }};

        const financialAccPercentCtx = document.getElementById('financialAccPercentChart').getContext('2d');
        new Chart(financialAccPercentCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [financialAccPercent, 100 - financialAccPercent],
                    backgroundColor: ['#15803d', '#f59e0b'], // Green, Gray, Orange
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                rotation: -90,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</div>