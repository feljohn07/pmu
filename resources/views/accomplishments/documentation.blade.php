{{-- resources/views/accomplishments/documentation.blade.php --}}
{{-- You might want to extend a main layout file --}}
{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<x-app-layout>
    {{-- resources/views/accomplishments/documentation.blade.php --}}
    {{-- @extends('layouts.app') --}}

    {{-- @section('content') --}}
    <div class="container mx-auto px-4 py-8">
        {{-- Display Status/Error Messages Flashed from Controller --}}
        @if (session('status'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                x-transition.duration.500ms>
                <x-mary-alert title="Success!" description="{{ session('status') }}" icon="o-check-circle"
                    class="alert-success my-4" />
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                x-transition.duration.500ms>
                <x-mary-alert title="Error!" description="{{ session('error') }}" icon="o-exclamation-triangle"
                    class="alert-error my-4" />
            </div>
        @endif


        {{-- Report Details (Keep existing code) --}}
        <div class="p-4 bg-white shadow rounded-lg">
            {{-- ... existing report details dl list ... --}}
            <div class="flex justify-between items-center">
                <a href="{{ route('project-view', ['id' => $report->project_id]) }}" class="btn btn-sm">
                    Back to Project
                </a>
                <div>
                    {{ $report->report_date }} Report
                </div>
            </div>
        </div>


        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Uploaded Documentation</h2>

            @if($report->documentationUploads->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($report->documentationUploads as $upload)
                        <div class="border rounded-lg overflow-hidden shadow-md bg-white flex flex-col">
                            {{-- Image Display --}}
                            {{-- <img src="{{ Storage::url($upload->url) }}" alt="Documentation Image" --}} 
                            <img
                                src="{{ url('storage/app/public/' . $upload->url) }}" alt="Documentation Image"
                                class="w-full h-48 object-cover">

                            {{-- Details and Actions --}}
                            <div class="p-3 flex-grow flex flex-col justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">Uploaded: {{ $upload->created_at->format('Y-m-d H:i') }}
                                    </p>
                                    <p
                                        class="text-sm font-medium {{ $upload->approved ? 'text-green-600' : 'text-orange-500' }}">
                                        Status: {{ $upload->approved ? 'Approved' : 'Pending Approval' }}
                                    </p>
                                </div>
                                {{-- Approval Button Form --}}
                                <div class="mt-2">
                                    @hasanyrole('admin|staff')
                                    <form action="{{ route('documentation.toggle-approval', $upload->id) }}" method="POST">
                                        @csrf {{-- CSRF Protection --}}
                                        @method('PATCH') {{-- Method Spoofing for PATCH request --}}


                                        <x-mary-button type="submit"
                                            label="{{ $upload->approved ? 'Revoke Approval' : 'Approve' }}"
                                            class="{{ $upload->approved ? 'btn-warning btn-outline btn-sm' : 'btn-success btn-sm' }} w-full"
                                            icon="{{ $upload->approved ? 'o-x-circle' : 'o-check-circle' }}" spinner />
                                    </form>
                                    {{-- Delete Button Form --}}
                                    <form action="{{ route('documentation.destroy', $upload->id) }}" method="POST"
                                        class="flex-1 mt-2"
                                        onsubmit="return confirm('Are you sure you want to delete this document? This action cannot be undone.');">
                                        {{-- Added JS Confirmation --}}
                                        @csrf
                                        @method('DELETE') {{-- Method Spoofing for DELETE request --}}
                                        <x-mary-button type="submit" label="Delete" class="btn-error btn-sm w-full"
                                            icon="o-trash" tooltip="Delete this document permanently" {{-- Added tooltip --}}
                                            spinner />
                                    </form>
                                    @endhasanyrole

                                </div>

                                {{-- <a href="{{ Storage::url($upload->url) }}" class="mt-5 btn btn-success btn-sm w-full"
                                    icon="o-trash" target="_blank" rel="noopener noreferrer">
                                    View Image
                                </a> --}}

                                <a href="{{ url('storage/app/public/' . $upload->url) }}"
                                    class="mt-5 btn btn-success btn-sm w-full" target="_blank" rel="noopener noreferrer">
                                    View Image
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                {{-- No documentation message (Keep existing code) --}}
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                    <p>No documentation has been uploaded for this report entry yet.</p>
                </div>
            @endif
        </div>

    </div>
    {{-- @endsection --}}

</x-app-layout>