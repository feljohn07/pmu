{{-- resources/views/livewire/financial-report-manager.blade.php --}}
<div class="container mx-auto px-4 py-4">

    {{-- Session Messages --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header and Add Button --}}
    <div class="flex justify-between items-center mb-4">
        {{-- Use the $projectName variable passed from render --}}
        <h2 class="text-2xl font-semibold">Financial Report</h2>
        <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Report Entry
        </button>
    </div>

    {{-- Reports Table --}}
    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Report Date (Text) {{-- Changed Label Slightly --}}
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Amount Used
                    </th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- Use the public $reports property (now a Collection) --}}
                @forelse ($reports as $report)
                                <tr>
                                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm">
                                        {{-- MODIFIED: Displaying the date string --}}
                                        {{-- Option 1: Display raw string --}}
                                        {{ $report->report_date }}

                                        {{-- Option 2: Attempt to parse and format (safer) --}}
                                        {{-- @php
                                            try {
                                                // Attempt to parse the string (e.g., 'YYYY-MM-DD' or other formats Carbon recognizes)
                                                // and format it nicely. Adjust 'F Y' as needed.
                                                $formattedDate = \Carbon\Carbon::parse($report->report_date)->format('F Y');
                                            } catch (\Exception $e) {
                                                // If parsing fails, fallback to the raw string stored in the database
                                                $formattedDate = $report->report_date ?? ''; // Use null coalesce for safety
                                            }
                                        @endphp
                                        {{ $formattedDate }} --}}
                                        {{-- End Option 2 --}}
                                    </td>
                                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-right">
                                        {{ number_format($report->amount, 2) }}
                                    </td>
                                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-right">
                                        <button wire:click="edit({{ $report->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                        <button wire:click="confirmDelete({{ $report->id }})"
                                            class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 border-b border-gray-200 bg-white text-sm">No financial
                            reports found for this project.</td>
                    </tr>
                @endforelse

                {{-- Total Row --}}
                {{-- Use the public $totalAmount property --}}
                @if($totalAmount > 0 || $reports->isNotEmpty()) {{-- Check if there are reports or total amount --}}
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm">
                            Total
                        </td>
                        <td class="px-5 py-3 border-b-2 border-gray-200 text-right text-sm">
                            {{ number_format($totalAmount, 2) }}
                        </td>
                        <td class="px-5 py-3 border-b-2 border-gray-200"></td> {{-- Empty cell for actions column --}}
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- REMOVED Pagination Links --}}
    {{-- Make sure no $reports->links() call exists here --}}


    {{-- Create/Edit Modal --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="my-modal">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ $selectedReportId ? 'Edit Report Entry' : 'Add New Report Entry' }}</h3>
                    <form wire:submit.prevent="store" class="mt-2 px-7 py-3">
                        <div class="mb-4">
                            <label for="report_date" class="block text-sm font-medium text-gray-700 text-left">Report Date
                                (Text)</label>
                            {{-- MODIFIED: Changed input type to text --}}
                            {{-- Use text if you want free-form input. --}}
                            <input type="text" id="report_date" wire:model.defer="report_date"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="Enter date as text (e.g., YYYY-MM-DD)">
                            {{-- Alternative: Keep type="date" if you prefer the UI and store 'YYYY-MM-DD' strings --}}
                            {{-- <input type="date" id="report_date" wire:model.defer="report_date"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            --}}
                            @error('report_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror 
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 text-left">Amount
                                Used</label>
                            <input type="number" step="0.01" id="amount" wire:model.defer="amount"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="items-center px-4 py-3">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save
                            </button>
                            <button type="button" wire:click="closeModalFinancial()"
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto ml-2 shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- Deletion Confirmation Modal --}}
    @if ($confirmingDeletion)
        <div class="fixed inset-0 bg-gray-800 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Deletion</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">Are you sure you want to delete this report entry? This action
                            cannot be undone.</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button wire:click="delete()"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-auto shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                        <button type="button" wire:click="closeModalFinancial"
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto ml-2 shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>