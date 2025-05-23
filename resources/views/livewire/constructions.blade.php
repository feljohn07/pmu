<div class="mx-20 mt-10">
    <p>Projects</p>
    <br>
    <div class="flex flex-wrap gap-6 justify-center lg:justify-start">
        <x-chart-card chartName="" completed="10" ongoing="10" pending="10" />
        <x-line-chart-card chartName="" approved="1" for-approval="5" no-pow="2" />
    </div>

    <x-mary-card class="mt-10">

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
                        <tr style="cursor: pointer;" class="cursor-pointer hover:bg-gray-100">
                            <td><x-nav-link :href="route('construction-view', ['id' => $project['id']])"
                                    :active="request()->routeIs('construction-view')">
                                    {{ __('View Project') }}
                                </x-nav-link></td>
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
                            {{-- <td>
                                @if($project['url'])
                                <a href="{{ $project['url'] }}" class="text-blue-500 underline" target="_blank">View</a>
                                @endif
                            </td> --}}
                            <td>
                                <button
                                    class="btn btn-sm {{ $project['pow_status'] == 'For Approval' ? 'btn-warning' : 'btn-success' }}"
                                    {{ $project['pow_status'] == 'Approved' ? "disabled" : null }}>{{ $project['pow_status'] == 'For Approval' ? 'APPROVE' : 'APPROVED' }}</button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-mary-card>
</div>