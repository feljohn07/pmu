<!DOCTYPE html>
<html lang="en">

{{-- TODO : add a button to print specific div as form --}}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program of Work Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Link to a font like Inter if not globally included --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Using Inter font */
            font-size: 0.8rem;
            /* Smaller base font size for report */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        table,
        th,
        td {
            border: 1px solid black;
            /* Basic table borders */
            border-collapse: collapse;
        }

        th,
        td {
            padding: 3px 5px;
            /* Adjusted Padding for table cells */
            text-align: left;
            vertical-align: top;
            /* Align text to top */
        }

        th {
            background-color: #e5e7eb;
            /* Light gray background for headers */
            font-weight: 600;
            /* Semibold for headers */
        }

        .section-title {
            font-weight: bold;
            background-color: #d1d5db;
            /* Slightly darker gray for section titles */
            padding: 5px 8px;
            margin-top: 1rem;
            /* Margin top for sections */
            border: 1px solid black;
            /* Ensure section titles also have borders */
            page-break-after: avoid;
            /* Try to keep title with content below */
        }

        .header-block {
            /* Consider adding page-break-inside: avoid if header often splits */
            /* page-break-inside: avoid; */
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        /* --- Print specific styles --- */
        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm 15mm 20mm 15mm;
                /* top, right, bottom, left */
            }


        }
    </style>
    @livewireStyles
</head>

<body class="p-4 md:p-8 bg-gray-100">

    @if (session()->has('report_error'))
        <div class="max-w-4xl mx-auto mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded no-print">
            <span class="font-bold">Error:</span> {{ session('report_error') }}
        </div>
    @endif

    @if($powItem)
        {{-- Removed the outer .print-container div as page-break-inside:avoid is not suitable here --}}
        <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg border border-gray-300">

            {{-- QR code moved inside the main content area, still floated right --}}
            <div class="float-right ml-4 mb-2 w-[100px] h-[100px] border border-gray-300 p-0.5 bg-white"
                id="report-qr-code-img">
                {{-- QR code will be generated here by JS --}}
            </div>

            {{-- Header Content --}}
            <div class="mb-6 text-sm text-gray-700 header-block"> {{-- Added class for potential page-break control --}}
                {{-- Header Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2"> {{-- Reduced gap slightly --}}

                    {{-- Left Column --}}
                    <div class="space-y-1"> {{-- Reduced space slightly --}}
                        <h1 class="text-lg font-bold mb-4 col-span-1 md:col-span-2">PROGRAM OF WORK</h1> {{-- Added Title
                        --}}
                        <div>
                            <span class="w-48 inline-block font-medium">NAME / LOCATION:</span>
                            <span class="">{{ $project->project_name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="w-48 inline-block font-medium">PROJECT CATEGORY:</span>
                            <span class="">{{ $project->project_category ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="w-48 inline-block font-medium">PROJECT DESCRIPTION:</span>
                            <span class="">{{ $project->work_description ?? 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-1"> {{-- Reduced space slightly --}}
                        <div class="h-[100px] md:h-auto"></div> {{-- Spacer to align with QR code height on small screens,
                        adjust as needed --}}
                        <div class="flex justify-between">
                            <span class="font-medium">Total Item Cost:</span>
                            <span class="text-right font-semibold">Php
                                {{ number_format($powItem->grand_total ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Quantity ({{ $powItem->quantity_unit ?? 'unit' }}):</span>
                            <span class="text-right font-semibold">{{ number_format($powItem->quantity ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Unit Cost (Direct):</span>
                            <span class="text-right font-semibold">Php {{ number_format($totalDirectCost ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Adjusted Unit Cost:</span>
                            <span class="text-right font-semibold">Php
                                {{ number_format($finalAdjustedUnitCost ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Duration (Days):</span>
                            <span class="text-right font-semibold">{{ number_format($powItem->duration ?? 0, 0) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Starting Date:</span>
                            <span class="text-right font-semibold">
                                {{ $powItem->start_date ? \Carbon\Carbon::parse($powItem->start_date)->format('m/d/Y') : 'Upon Approval' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clear float for QR code before the next block element --}}
            <div style="clear: both;"></div>

            {{-- Divider Row / Item Header --}}
            <div class="flex items-center justify-between mt-6 bg-orange-100 text-orange-900 font-bold px-4 py-2 rounded-md border border-orange-300"
                style="page-break-before: avoid;">
                {{-- ^ Added inline style to try and keep this with section below --}}
                <div class="flex items-center space-x-2">
                    <span>ITEM NO.:</span>
                    <span>{{ $powItem->item_number ?? 'N/A' }}</span>
                </div>
                <div class="flex-1 text-center px-4">
                    <span class="font-semibold">WORK DESCRIPTION:</span>
                    <span class="ml-2 font-normal">{{ $powItem->work_description ?? 'N/A' }}</span>
                </div>
            </div>

            {{-- Item Description --}}
            <div class="mt-4 mb-4">
                <div>
                    <span class="w-48 inline-block font-medium">ITEM DESCRIPTION:</span>
                    <span class="">{{ $powItem->item_description ?? 'N/A' }}</span>
                </div>
            </div>


            {{-- Direct Costs Section --}}
            <div class="section-title mb-1">A. DIRECT COST</div>

            {{-- A1 Materials --}}
            <div class="font-semibold mb-1 ml-4">A1 MATERIALS TO BE USED IN DOING THE WORK ITEM</div>
            <table class="w-full border border-black mb-4">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center w-20">QTY</th>
                        <th class="text-center w-16">UNIT</th>
                        <th class="text-center w-24">PRICE (Php)</th>
                        <th class="text-center w-28">MATERIAL COST (Php)</th> {{-- Slightly wider --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($powItem->materialCosts ?? [] as $cost)
                        <tr>
                            <td>{{ $cost->description }}</td>
                            <td class="text-right">{{ number_format($cost->quantity, 2) }}</td>
                            <td class="text-center">{{ $cost->unit }}</td>
                            <td class="text-right">{{ number_format($cost->price, 2) }}</td>
                            <td class="text-right">{{ number_format($cost->cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center italic py-2">No material costs recorded.</td>
                        </tr>
                    @endforelse
                    {{-- Subtotal Row --}}
                    @if(isset($powItem->materialCosts) && $powItem->materialCosts->count() > 0)
                        <tr>
                            <td colspan="4" class="text-right font-semibold border-t-2 border-black">SUB-TOTAL (Materials)</td>
                            <td class="text-right font-semibold border-t-2 border-black">
                                {{ number_format($powItem->material_subtotal ?? 0, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{-- A2 Labor --}}
            <div class="font-semibold mb-1 ml-4">A2 LABOR REQUIREMENTS</div>
            <table class="w-full border border-black mb-4">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center w-28">NO. OF MANPOWER</th>
                        <th class="text-center w-24">NO. OF DAYS</th>
                        <th class="text-center w-24">RATE / DAY (Php)</th>
                        <th class="text-center w-28">LABOR COST (Php)</th> {{-- Slightly wider --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($powItem->laborCosts ?? [] as $cost)
                        <tr>
                            <td>{{ $cost->description }}</td>
                            <td class="text-center">{{ number_format($cost->number_of_manpower, 2) }}</td>
                            <td class="text-center">{{ number_format($cost->number_of_days, 2) }}</td>
                            <td class="text-right">{{ number_format($cost->rate_per_day, 2) }}</td>
                            <td class="text-right">{{ number_format($cost->cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center italic py-2">No labor costs recorded.</td>
                        </tr>
                    @endforelse
                    {{-- Subtotal Row --}}
                    @if(isset($powItem->laborCosts) && $powItem->laborCosts->count() > 0)
                        <tr>
                            <td colspan="4" class="text-right font-semibold border-t-2 border-black">SUB-TOTAL (Labor)</td>
                            <td class="text-right font-semibold border-t-2 border-black">
                                {{ number_format($powItem->labor_subtotal ?? 0, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{-- A3 Equipment --}}
            <div class="font-semibold mb-1 ml-4">A3 EQUIPMENT REQUIRED (CAPACITY)</div>
            <table class="w-full border border-black mb-4">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center w-28">NO. OF UNITS</th>
                        <th class="text-center w-24">NO. OF DAYS</th>
                        <th class="text-center w-24">RATE / DAY (Php)</th>
                        <th class="text-center w-28">EQUIPMENT EXP. (Php)</th> {{-- Slightly wider --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($powItem->equipmentCosts ?? [] as $cost)
                        <tr>
                            <td>{{ $cost->description }}</td>
                            <td class="text-center">{{ number_format($cost->number_of_units, 2) }}</td>
                            <td class="text-center">{{ number_format($cost->number_of_days, 2) }}</td>
                            <td class="text-right">{{ number_format($cost->rate_per_day, 2) }}</td>
                            <td class="text-right">{{ number_format($cost->cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center italic py-2">(NONE) No equipment costs recorded.</td>
                        </tr>
                    @endforelse
                    {{-- Subtotal Row --}}
                    {{-- Show subtotal even if zero for consistency --}}
                    <tr>
                        <td colspan="4" class="text-right font-semibold border-t-2 border-black">SUB-TOTAL (Equipment)</td>
                        <td class="text-right font-semibold border-t-2 border-black">
                            {{ number_format($powItem->equipment_subtotal ?? 0, 2) }}</td>
                    </tr>

                    {{-- Total Direct Cost --}}
                    <tr>
                        <td colspan="4" class="border-t-2 border-black p-1 text-right font-bold">TOTAL DIRECT COST (A)</td>
                        <td class="border-t-2 border-black p-1 text-right font-bold">
                            {{ number_format($totalDirectCost ?? 0, 2) }}
                        </td>
                    </tr>
                    {{-- Unit Cost (Based on Total Direct Cost) - Removed as it's confusing here, shown top right --}}
                    {{-- <tr>
                        <td colspan="4" class="border-none p-1 text-right font-bold">UNIT COST (Direct)</td>
                        <td class="border-none p-1 text-right font-bold">{{ number_format($totalDirectCost ?? 0, 2) }}</td>
                    </tr> --}}
                </tbody>
            </table>


            {{-- Indirect Costs Section --}}
            <div class="section-title mb-1">B. INDIRECT COST</div>
            <table class="w-full border border-black mb-4">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center w-28">BASED COST (Php)</th> {{-- Wider --}}
                        <th class="text-center w-28">MARK-UP (%)</th>
                        <th class="text-center w-28">MARK-UP VALUE (Php)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($powItem->indirectCost)
                            @foreach (range(1, 5) as $i)
                                    @php
                                        $descKey = 'b' . $i . '_description';
                                        $baseCostKey = 'b' . $i . '_base_cost';
                                        $markupPercentKey = 'b' . $i . '_markup_percent';
                                        $markupValueKey = 'b' . $i . '_markup_value';
                                        // Only show row if description exists
                                        $description = $powItem->indirectCost->$descKey ?? null;
                                        $markupValue = $powItem->indirectCost->$markupValueKey ?? 0;
                                    @endphp
                                    @if(!empty($description) || $markupValue != 0) {{-- Show row if description or value exists --}}
                                        <tr>
                                            <td>{{ $description ?? "B{$i} N/A" }}</td>
                                            <td class="text-right">{{ number_format($powItem->indirectCost->$baseCostKey ?? 0, 2) }}</td>
                                            <td class="text-center">{{ number_format($powItem->indirectCost->$markupPercentKey ?? 0, 2) }}%</td>
                                            <td class="text-right">{{ number_format($markupValue, 2) }}</td>
                                        </tr>
                                    @endif
                            @endforeach
                            {{-- Total Indirect Cost --}}
                            <tr>
                                <td colspan="3" class="text-right font-semibold border-t-2 border-black">TOTAL INDIRECT COST (B)
                                </td>
                                <td class="text-right font-semibold border-t-2 border-black">
                                    {{ number_format($powItem->indirect_subtotal ?? 0, 2) }}</td>
                            </tr>
                    @else
                        <tr>
                            <td colspan="4" class="text-center italic py-2">No indirect cost details recorded.</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right font-semibold border-t-2 border-black">TOTAL INDIRECT COST (B)
                            </td>
                            <td class="text-right font-semibold border-t-2 border-black">{{ number_format(0, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{-- Grand Totals --}}
            {{-- Added wrapper div with class for potential page break control --}}
            <div class="totals-section mt-6" style="page-break-inside: avoid;">
                <div class="flex justify-end">
                    <span class="w-56 text-right font-bold mr-4">GRAND TOTAL (A + B)</span>
                    <span
                        class="font-bold w-36 text-right border-t-2 border-b-4 border-double border-black">{{ number_format($powItem->grand_total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-end mt-1">
                    <span class="w-56 text-right font-bold mr-4">ADJUSTED UNIT COST</span>
                    <span class="font-bold w-36 text-right">{{ number_format($finalAdjustedUnitCost ?? 0, 2) }}</span>
                </div>
            </div>

        </div> {{-- End main content container --}}

    @else
        <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg border border-gray-300 text-center text-gray-500">
            Program of Work item details could not be loaded or found.
        </div>
    @endif


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
                    width: 96,      // Slightly smaller to fit padding? Match container w-[100px] minus p-0.5 * 2? Test this.
                    height: 96,     // Match width
                    colorDark: "#000000", // QR code color
                    colorLight: "#ffffff", // Background color
                    correctLevel: QRCode.CorrectLevel.H // High error correction
                });
            } else {
                console.error("QR Code container element (#report-qr-code-img) not found.");
            }
        });
    </script>

    @livewireScripts
</body>

</html>