<div class="m-auto">
    <h1 class="text-center mt-10 text-4xl">Project Monitoring and Evaluation</h1>

    <div class="flex justify-center items-center bg-gray-100 mt-10">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-chart-card completed="{{  $constructionsChartData['pending']  }}" ongoing="1" pending="6"
                chart-name="Construction Projects" />
            <x-chart-card completed="10" ongoing="1" pending="3" chart-name="Repair Projects" />
            <x-chart-card completed="10" ongoing="1" pending="1" chart-name="Fabrication Projects" />

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
                        <th>Status</th>
                        {{-- <th>Balance</th> --}}
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    <tr>
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
                    </tr>

                </tbody>
            </table>
        </div>
    </x-mary-card>
</div>