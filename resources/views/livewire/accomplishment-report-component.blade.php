{{-- resources/views/livewire/accomplishment-report-component.blade.php --}}
<div>
    {{-- Session Message Handling (Kept from original for user feedback) --}}
    {{-- @if (session()->has('message'))
    <x-mary-alert title="Success!" description="{{ session('message') }}" icon="o-check-circle"
        class="alert-success mb-4" />
    @endif
    @if (session()->has('error'))
    <x-mary-alert title="Error!" description="{{ session('error') }}" icon="o-exclamation-triangle"
        class="alert-error mb-4" />
    @endif --}}

    {{-- Accomplishment Report Card (Structure from reference) --}}
    <x-mary-card class="mt-10">

        {{-- Header Section (Structure from reference) --}}
        <div class="flex items-center justify-between w-full">
            <p class="text-lg font-semibold">Accomplishment Report</p> {{-- Adjusted styling slightly --}}
            <div>

                @hasanyrole(['admin', 'staff'])
                {{-- Button adapted from original component logic --}}
                <x-mary-button class="btn-sm" label="Add Planned Accomplishment" wire:click="createPlanReport"
                    icon="o-plus" primary spinner />
                @endhasanyrole


            </div>
        </div>

        {{-- Table Section (Structure from reference, Content from original component) --}}
        <div class="overflow-x-auto mt-10">
            <table class="table w-full">
                {{-- Table Head (Headers from reference, keys match original component) --}}
                <thead>
                    <tr>
                        <th>Month/Date</th> {{-- Changed from 'Month' to match original component data --}}
                        <th>Planned Accomplishment (%)</th> {{-- Added (%) for clarity like reference --}}
                        <th>Actual Accomplishment (%)</th> {{-- Added (%) for clarity like reference --}}
                        <th>Variance (%)</th> {{-- Added (%) for clarity like reference --}}
                        <th>Documentations</th> {{-- Combined Documentation and Actions --}}
                        @hasanyrole(['admin', 'staff'])
                        <th>Actions</th> {{-- Combined Documentation and Actions --}}
                        @endhasanyrole


                    </tr>
                </thead>

                {{-- Table Body (Looping through data from original component) --}}
                <tbody>
                    @forelse ($accomplishmentReports as $report)
                        <tr>
                            <td>{{ $report->report_date }}</td>
                            <td>{{ number_format($report->planned_accomplishment ?? 0, 0) }}%</td> {{-- Simplified
                            formatting --}}
                            <td>
                                @if ($report->actual_accomplishment !== null)
                                    {{ number_format($report->actual_accomplishment, 0) }}% {{-- Simplified formatting --}}
                                @else
                                    <span class="text-gray-400 italic text-xs">Not Submitted</span> {{-- Adjusted display --}}
                                @endif
                            </td>
                            <td>
                                @if ($report->variance !== null)
                                    <span class="{{ $report->variance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($report->variance, 0) }}% {{-- Simplified formatting --}}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{-- Documentation Buttons --}}
                                <div class="flex flex-wrap gap-1">
                                    <x-mary-button label="Upload" wire:click="uploadDocumentation({{ $report->id }})"
                                        icon="o-arrow-up-tray" class="btn-sm" tooltip="Upload Supporting Documents"
                                        spinner />
                                    <x-mary-button label="View All" wire:click="viewDocumentation({{ $report->id }})"
                                        icon="o-eye" class="btn-sm" tooltip="View Uploaded Documents" spinner />
                                </div>
                            </td>
                            <td>
                                {{-- Action Buttons --}}
                                <div class="flex flex-wrap gap-1">

                                    @hasanyrole(['admin', 'staff'])
                                    {{-- Show Edit Plan button ONLY if actual is not yet submitted --}}
                                    @if ($report->actual_accomplishment === null)
                                        <x-mary-button label="Edit Plan" wire:click="editPlanReport({{ $report->id }})"
                                            icon="o-pencil" class="btn-sm btn-warning" spinner tooltip="Edit Planned Entry" />
                                    @endif

                                    {{-- Submit/Update Actual Buttons --}}
                                    @if ($report->actual_accomplishment === null)
                                        <x-mary-button label="Submit Actual %"
                                            wire:click="openActualFormModal({{ $report->id }})" icon="o-pencil-square"
                                            class="btn-sm btn-success" spinner />
                                    @else
                                        <x-mary-button label="Update Actual %"
                                            wire:click="openActualFormModal({{ $report->id }})" icon="o-pencil"
                                            class="btn-sm btn-warning" spinner />
                                    @endif

                                    <x-mary-button wire:click="confirmDelete({{ $report->id }})" icon="o-trash"
                                        class="btn-sm btn-error btn-outline" spinner tooltip="Delete Entry" />
                                    @endhasanyrole


                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center italic text-gray-500">No accomplishment plans added yet.</td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Table Footer (Structure from reference, Totals from original component) --}}
                <tfoot>
                    <tr class="font-semibold bg-base-200"> {{-- Added background for emphasis --}}
                        <td>Total</td>
                        <td>{{ number_format($this->totalPlanned ?? 0, 0) }}%</td>
                        <td>{{ number_format($this->totalActual ?? 0, 0) }}%</td>
                        <td>
                            <span class="{{ $totalVariance >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ number_format($totalVariance, 0) }}%
                            </span>
                        </td>
                        <td>{{-- Footer actions cell - can be left empty or add summary actions --}}</td>
                        <td>{{-- Footer actions cell - can be left empty or add summary actions --}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-mary-card>

    {{-- Modals (Kept from original component for functionality) --}}
    {{-- Create Plan Modal --}}
    <x-mary-modal wire:model="showPlanFormModal" title="Create New Plan Entry">
        <x-mary-form wire:submit.prevent="savePlanReport">
            <x-mary-input label="Month/Date Reference" wire:model="reportDate"
                placeholder="e.g., January Week 1 or 2024-01-15" required />
            <x-mary-input label="Planned Accomplishment (%)" wire:model="plannedAccomplishment" type="number" step="1"
                min="0" max="100" placeholder="Enter percentage (0-100)" required /> {{-- Adjusted step/placeholder --}}
            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showPlanFormModal = false" />
                <x-mary-button label="Save Plan" type="submit" class="btn-primary" spinner="savePlanReport" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    {{-- Submit Actual Accomplishment Modal --}}
    <x-mary-modal wire:model="showActualFormModal" title="Submit Actual Accomplishment">
        @if($currentReport)
            <div class="mb-4 p-2 bg-base-200 rounded">
                <p><strong>Month/Date:</strong> {{ $currentReport->report_date }}</p>
                <p><strong>Planned:</strong> {{ number_format($currentReport->planned_accomplishment ?? 0, 0) }}%</p>
            </div>
            <x-mary-form wire:submit.prevent="saveActualAccomplishment">
                <x-mary-input label="Actual Accomplishment (%)" wire:model="currentActualAccomplishment" type="number"
                    step="1" min="0" max="100" placeholder="Enter percentage (0-100)" required /> {{-- Adjusted
                step/placeholder --}}
                <x-slot:actions>
                    <x-mary-button label="Cancel" @click="$wire.showActualFormModal = false" />
                    <x-mary-button label="Submit Actual" type="submit" class="btn-primary"
                        spinner="saveActualAccomplishment" />
                </x-slot:actions>
            </x-mary-form>
        @else
            <p>Loading report details...</p>
            <x-slot:actions>
                <x-mary-button label="Cancel" @click="$wire.showActualFormModal = false" />
            </x-slot:actions>
        @endif
    </x-mary-modal>

    {{-- Delete Confirmation Modal --}}
    <x-mary-modal wire:model="showDeleteConfirmationModal" title="Confirm Deletion">
        <p>Are you sure you want to delete this accomplishment report entry?</p>
        <p class="font-semibold">This action cannot be undone.</p>
        @if($reportToDeleteId && $report = $accomplishmentReports->firstWhere('id', $reportToDeleteId))
            <div class="mt-2 p-2 bg-base-200 rounded border border-base-300">
                <p><strong>Month/Date:</strong> {{ $report->report_date }}</p>
                <p><strong>Planned:</strong> {{ number_format($report->planned_accomplishment ?? 0, 0) }}%</p>
                <p><strong>Actual:</strong>
                    {{ $report->actual_accomplishment !== null ? number_format($report->actual_accomplishment, 0) . '%' : 'N/A' }}
                </p>
            </div>
        @endif
        <x-slot:actions>
            <x-mary-button label="Cancel" wire:click="cancelDelete" />
            <x-mary-button label="Confirm Delete" wire:click="deleteReport" class="btn-error" spinner="deleteReport" />
        </x-slot:actions>
    </x-mary-modal>


    {{-- NEW: Documentation Upload Modal --}}
    <x-mary-modal wire:model="showUploadModal" title="Upload Documentation">
        {{-- Display Report Info (Optional) --}}
        @if($reportIdForUpload && $currentReportForUpload = $accomplishmentReports->firstWhere('id', $reportIdForUpload))
            <div class="mb-4 p-2 bg-base-200 rounded border border-base-300">
                <p><strong>Uploading for Report:</strong> {{ $currentReportForUpload->report_date }}</p>
                <p><strong>Planned:</strong> {{ number_format($currentReportForUpload->planned_accomplishment ?? 0, 0) }}%
                </p>
            </div>
        @endif

        <x-mary-form wire:submit.prevent="saveDocumentation">
            {{-- File Input - Use Mary UI's file input component --}}
            {{-- The `wire:model="filesToUpload"` binds this input to the component property --}}
            <x-mary-file wire:model="filesToUpload" label="Select Images" hint="Max 5MB per image" multiple />

            {{-- Loading Indicator --}}
            <div wire:loading wire:target="filesToUpload" class="text-sm text-gray-500 italic mt-1">
                Uploading...
            </div>

            {{-- Display Validation Errors Specific to Files --}}
            @error('filesToUpload.*') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror


            <x-slot:actions>
                {{-- Add wire:click.prevent to stop modal closing immediately if inside form --}}
                <x-mary-button label="Cancel" @click="$wire.showUploadModal = false" />
                <x-mary-button label="Upload Files" type="submit" class="btn-primary" spinner="saveDocumentation" />
            </x-slot:actions>
        </x-mary-form>

        {{-- Display general upload progress/spinner --}}
        <div wire:loading wire:target="saveDocumentation" class="w-full mt-2 text-center">
            <x-mary-loading class="text-primary loading-lg" />
            <span class="text-sm italic">Processing upload...</span>
        </div>

    </x-mary-modal>

</div>