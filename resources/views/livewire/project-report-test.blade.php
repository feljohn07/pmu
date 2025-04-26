{{--
Livewire View: resources/views/livewire/project-report.blade.php

This view renders the project report using data prepared by the
App\Livewire\ProjectReport component.
Includes client-side QR code generation for the current page URL.
Uses Tailwind CSS for styling.
--}}
<div> {{-- Livewire components must have a single root element --}}

    {{-- Container applying base styles and layout --}}
    <div class="max-w-[800px] my-5 mx-auto border border-gray-200 p-5 bg-white shadow-sm font-sans leading-normal text-sm text-gray-800">

        {{-- Display Error Message if Project Loading Failed --}}
        @if ($errorMessage)
            <div class="text-red-800 bg-red-100 border border-red-300 px-4 py-2.5 m-4 rounded text-center">
                {{ $errorMessage }}
            </div>
        @elseif ($project) {{-- Only display the report if project loaded successfully --}}

            {{-- Report Header --}}
            <div class="text-center mb-5 border-b border-gray-400 pb-4 relative min-h-[80px]">
                {{-- QR Code Container: Positioned absolutely top-right --}}
                <div id="report-qr-code-img" class="absolute top-[10px] right-[10px] w-[100px] h-[100px] border border-gray-300 p-0.5 bg-white">
                    {{-- QR code will be generated here by JS --}}
                </div>
                <div class="mb-2.5">
                    {{-- Add logos here if needed, e.g., <img src="..." alt="..." class="mx-auto h-12"> --}}
                </div>
                <p class="my-[3px] text-xs text-gray-700">Republic of the Philippines</p>
                <h1 class="text-lg font-bold text-[#003366]">CARAGA STATE UNIVERSITY</h1> {{-- Using specific hex color --}}
                <p class="my-[3px] text-xs text-gray-700">Ampayon, Butuan City 8600, Philippines</p>
                <p class="my-[3px] text-xs text-gray-700">Website: //http:www.carsu.edu.ph Email: op@carsu.edu.ph</p>
                <p class="font-bold mt-[5px] text-xs text-gray-700">OFFICE OF THE PLANNING AND DEVELOPMENT</p>
            </div>

            {{-- Project Details Section - Grid Layout --}}
            <div class="grid grid-cols-2 gap-x-[25px] gap-y-3 mb-5 text-xs">

                {{-- Project Identification --}}
                <div class="flex py-[3px] col-span-2"> {{-- Full width --}}
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">NAME/LOCATION OF PROJECT:</span>
                    <span class="flex-grow text-gray-800 font-bold">{{ $project->project_name ?? 'N/A' }}</span>
                </div>
                <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">PROJECT CATEGORY:</span>
                    <span class="flex-grow text-gray-800">{{ $project->project_category ?? 'N/A' }}</span>
                </div>

                {{-- Financial Information --}}
                <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">Appropriation:</span>
                    <span class="flex-grow text-gray-800">Php {{ number_format($project->appropriation ?? 0, 2) }}</span>
                </div>
                <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">Source of Funds:</span>
                    <span class="flex-grow text-gray-800">{{ $project->source_of_funds ?? 'N/A' }}</span>
                </div>
                 {{-- <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">Issued Obligation Authority:</span>
                    <span class="flex-grow text-gray-800">{{ $project->obligation_authority ?? 'N/A' }}</span> 
                </div>
                 <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">Allotment Released:</span>
                    <span class="flex-grow text-gray-800">{{ $project->allotment_released ?? 'N/A' }}</span>
                </div> --}}

                {{-- Timing --}}
                <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">Projection Duration:</span>
                    <span class="flex-grow text-gray-800">{{ $project->duration ?? 'N/A' }}</span>
                </div>
                <div class="flex py-[3px]">
                    <span class="font-bold w-40 flex-shrink-0 text-gray-700">Desirable Starting Date:</span>
                    <span class="flex-grow text-gray-800">
                        {{ isset($project->start_date) ? \Carbon\Carbon::parse($project->start_date)->format('F d, Y') : 'Upon Approval' }}
                    </span>
                </div>

            </div>

            {{-- Cost Breakdown Table --}}
            <div class="font-bold mt-5 mb-2.5 text-base underline text-[#003366]">BREAKDOWN OF ESTIMATED EXPENDITURES</div>
            <table class="w-full border-collapse mb-5 text-xs">
                <thead>
                    <tr>
                        {{-- Base cell style: border border-gray-400 py-[5px] px-2 text-left align-top --}}
                        {{-- TH specific style: bg-gray-100 font-bold text-center text-gray-700 --}}
                        <th class="border border-gray-400 py-[5px] px-2 align-top bg-gray-100 font-bold text-center text-gray-700">PARTICULARS</th>
                        <th class="border border-gray-400 py-[5px] px-2 align-top bg-gray-100 font-bold text-center text-gray-700">% OF TOTAL</th> {{-- TH Percent alignment --}}
                        <th class="border border-gray-400 py-[5px] px-2 align-top bg-gray-100 font-bold text-gray-700 text-right">AMOUNT (PHP)</th> {{-- TH Amount alignment --}}
                    </tr>
                </thead>
                <tbody>
                    {{-- A. DIRECT COST Section --}}
                    <tr>
                        <td colspan="3" class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold">A. DIRECT COST:</td>
                    </tr>
                    @forelse ($directCosts as $cost)
                        <tr>
                            <td class="border border-gray-400 py-[5px] px-2 text-left align-top pl-[25px]">{{ $cost->cost_description }}</td> {{-- Indent 1 --}}
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-center"> {{-- Percent alignment --}}
                                {{ $cost->percentage > 0 ? number_format($cost->percentage, 2) . ' %' : '' }}
                            </td>
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-right">{{ $cost->amount > 0 ? number_format($cost->amount, 2) : '' }}</td> {{-- Amount alignment --}}
                        </tr>
                    @empty
                        <tr>
                            {{-- Empty row style: text-gray-500 italic text-center --}}
                            <td colspan="3" class="border border-gray-400 py-[5px] px-2 align-top pl-[25px] text-gray-500 italic text-center">No direct costs entered.</td> {{-- Indent 1 --}}
                        </tr>
                    @endforelse
                    <tr> {{-- Subtotal row style: font-bold bg-gray-50 --}}
                        <td class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold bg-gray-50">SUB-TOTAL (DIRECT COST)</td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-center"> {{-- Percent alignment --}}
                            {{ number_format($this->calculatePercentage($directCostSubtotal, $totalProjectCost), 2) }} %
                        </td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-right">{{ number_format($directCostSubtotal, 2) }}</td> {{-- Amount alignment --}}
                    </tr>

                    {{-- B. INDIRECT COST Section --}}
                    <tr>
                        <td colspan="3" class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold">B. INDIRECT COST:</td>
                    </tr>
                    @forelse ($indirectCosts as $cost)
                         <tr>
                            <td class="border border-gray-400 py-[5px] px-2 text-left align-top pl-[25px]">{{ $cost->cost_description }}</td> {{-- Indent 1 --}}
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-center"> {{-- Percent alignment --}}
                                {{ $cost->percentage > 0 ? number_format($cost->percentage, 2) . ' %' : '' }}
                            </td>
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-right">{{ $cost->amount > 0 ? number_format($cost->amount, 2) : '' }}</td> {{-- Amount alignment --}}
                        </tr>
                    @empty
                        <tr>
                             {{-- Empty row style: text-gray-500 italic text-center --}}
                            <td colspan="3" class="border border-gray-400 py-[5px] px-2 align-top pl-[25px] text-gray-500 italic text-center">No indirect costs entered.</td> {{-- Indent 1 --}}
                        </tr>
                    @endforelse
                     <tr> {{-- Subtotal row style: font-bold bg-gray-50 --}}
                        <td class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold bg-gray-50">SUB-TOTAL (INDIRECT COST)</td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-center"> {{-- Percent alignment --}}
                            {{ number_format($this->calculatePercentage($indirectCostSubtotal, $totalProjectCost), 2) }} %
                        </td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-right">{{ number_format($indirectCostSubtotal, 2) }}</td> {{-- Amount alignment --}}
                    </tr>

                     {{-- II. ESTIMATED GOVERNMENT EXPENDITURES Section --}}
                    <tr>
                        <td colspan="3" class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold">II. ESTIMATED GOVERNMENT EXPENDITURES:</td>
                    </tr>
                    @forelse ($governmentExpenditures as $cost)
                         <tr>
                            <td class="border border-gray-400 py-[5px] px-2 text-left align-top pl-[25px]">{{ $cost->cost_description }}</td> {{-- Indent 1 --}}
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-center"> {{-- Percent alignment --}}
                                {{ $cost->percentage > 0 ? number_format($cost->percentage, 2) . ' %' : ''}}
                            </td>
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-right">{{ $cost->amount > 0 ? number_format($cost->amount, 2) : '' }}</td> {{-- Amount alignment --}}
                        </tr>
                    @empty
                         <tr>
                             {{-- Empty row style: text-gray-500 italic text-center --}}
                            <td colspan="3" class="border border-gray-400 py-[5px] px-2 align-top pl-[25px] text-gray-500 italic text-center">No government expenditures entered.</td> {{-- Indent 1 --}}
                        </tr>
                    @endforelse
                     <tr> {{-- Subtotal row style: font-bold bg-gray-50 --}}
                        <td class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold bg-gray-50">SUB-TOTAL (GOVT. EXPENDITURES)</td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-center"> {{-- Percent alignment --}}
                            {{ number_format($this->calculatePercentage($governmentExpendituresSubtotal, $totalProjectCost), 2) }} %
                        </td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-right">{{ number_format($governmentExpendituresSubtotal, 2) }}</td> {{-- Amount alignment --}}
                    </tr>

                     {{-- III. PHYSICAL CONTINGENCIES Section --}}
                    <tr>
                        <td colspan="3" class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold">III. PHYSICAL CONTINGENCIES:</td>
                    </tr>
                    @forelse ($physicalContingencies as $cost)
                         <tr>
                            <td class="border border-gray-400 py-[5px] px-2 text-left align-top pl-[25px]">{{ $cost->cost_description }}</td> {{-- Indent 1 --}}
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-center"> {{-- Percent alignment --}}
                                {{ $cost->percentage > 0 ? number_format($cost->percentage, 2) . ' %' : '' }}
                            </td>
                            <td class="border border-gray-400 py-[5px] px-2 align-top text-right">{{ $cost->amount > 0 ?number_format($cost->amount, 2) : '' }}</td> {{-- Amount alignment --}}
                        </tr>
                    @empty
                         <tr>
                             {{-- Empty row style: text-gray-500 italic text-center --}}
                            <td colspan="3" class="border border-gray-400 py-[5px] px-2 align-top pl-[25px] text-gray-500 italic text-center">No physical contingencies entered.</td> {{-- Indent 1 --}}
                        </tr>
                    @endforelse
                     <tr> {{-- Subtotal row style: font-bold bg-gray-50 --}}
                        <td class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold bg-gray-50">SUB-TOTAL (PHYS. CONTINGENCIES)</td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-center"> {{-- Percent alignment --}}
                             {{ number_format($this->calculatePercentage($physicalContingenciesSubtotal, $totalProjectCost), 2) }}
                        </td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold bg-gray-50 text-right">{{ number_format($physicalContingenciesSubtotal, 2) }}</td> {{-- Amount alignment --}}
                    </tr>

                    {{-- Grand Total Row --}}
                     {{-- Total row style: font-bold text-sm bg-gray-200 text-[#003366] --}}
                    <tr>
                        <td class="border border-gray-400 py-[5px] px-2 text-left align-top font-bold text-sm bg-gray-200 text-[#003366]">TOTAL ESTIMATED PROJECT COST</td>
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold text-sm bg-gray-200 text-[#003366] text-center">100.00</td> {{-- Percent alignment --}}
                        <td class="border border-gray-400 py-[5px] px-2 align-top font-bold text-sm bg-gray-200 text-[#003366] text-right">{{ number_format($totalProjectCost, 2) }}</td> {{-- Amount alignment --}}
                    </tr>
                </tbody>
            </table>

            {{-- Signatures Section - Responsive Grid --}}
            {{-- Grid: grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 --}}
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-[25px] text-xs text-center pt-4 border-t border-gray-300">
                {{-- Signature Block style: flex flex-col items-center --}}
                <div class="flex flex-col items-center">
                    {{-- Label style: text-[8pt] mb-10 text-gray-500 --}}
                    <span class="text-[8pt] mb-10 text-gray-500">Prepared By:</span>
                    {{-- Name style: font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800 --}}
                    <span class="font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800">MARIEL M. DELO, RCE</span>
                    {{-- Title style: text-[8pt] text-gray-700 --}}
                    <span class="text-[8pt] text-gray-700">Quantity Surveyor</span>
                </div>
                 <div class="flex flex-col items-center">
                    <span class="text-[8pt] mb-10 text-gray-500">Checked and Reviewed By:</span>
                    <span class="font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800">DERWIN T. GUMBAN, RLA, UAP</span>
                    <span class="text-[8pt] text-gray-700">University Architect</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[8pt] mb-10 text-gray-500">Checked and Reviewed By:</span>
                    <span class="font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800">CLARK AIAN L. INGLES, RN, REB</span>
                    <span class="text-[8pt] text-gray-700">Director,<br>Office of Planning and Development</span>
                </div>
                 <div class="flex flex-col items-center">
                    <span class="text-[8pt] mb-10 text-gray-500">Recommending Approval:</span>
                    <span class="font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800">MICHELLE V. JAPITANA, D.Eng.</span>
                    <span class="text-[8pt] text-gray-700">VP for Executive Operations<br>& Auxiliary Services</span>
                </div>
                 <div class="flex flex-col items-center">
                    <span class="text-[8pt] mb-10 text-gray-500">Recommending Approval:</span>
                    <span class="font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800">ALEXANDER T. DEMETILLO, D.Eng.</span>
                    <span class="text-[8pt] text-gray-700">VP for Administration & Finance</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="text-[8pt] mb-10 text-gray-500">Approved By:</span>
                    <span class="font-bold mt-[5px] border-t border-gray-700 pt-1 w-[85%] text-gray-800">ROLYN C. DAGUIL, Ph.D.</span>
                    <span class="text-[8pt] text-gray-700">University President</span>
                </div>
            </div>

        @endif {{-- End check for $project --}}

    </div> {{-- End report-container --}}

    {{-- Include the qrcode.js library from a CDN --}}
    {{-- Place this towards the end of the component's view --}}
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    {{-- Add the script to generate the QR code (Unchanged from original) --}}
    <script>
        // Wait for the DOM to be fully loaded before running the script
        document.addEventListener('DOMContentLoaded', function () {
            // Get the container element where the QR code will be placed
            const qrCodeContainer = document.getElementById('report-qr-code-img');

            // Check if the container element exists
            if (qrCodeContainer) {
                // Clear any existing content (like placeholder text)
                qrCodeContainer.innerHTML = '';

                // Get the current page URL
                const currentPageUrl = window.location.href;

                // Generate the QR code
                new QRCode(qrCodeContainer, {
                    text: currentPageUrl, // The data to encode (current URL)
                    width: 100,            // Match the Tailwind CSS width w-[75px]
                    height: 100,           // Match the Tailwind CSS height h-[75px]
                    colorDark: "#000000", // QR code color
                    colorLight: "#ffffff", // Background color
                    correctLevel: QRCode.CorrectLevel.H // High error correction
                });
            } else {
                console.error("QR Code container element (#report-qr-code-img) not found.");
            }
        });
    </script>

</div>