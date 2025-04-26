{{-- Use a Mary UI Card for consistent styling --}}
<x-mary-card class="w-80" title="{{ $chartName }}">

    <div class="flex items-center mt-2"> {{-- Reduced top margin slightly --}}
        <div class="w-[150px] h-[150px] relative">
            {{-- Use the Mary UI Chart component, binding the config --}}
            {{-- It will create the <canvas> internally --}}
            <x-mary-chart wire:model="chartConfig" />

            {{--
               NOTE: Displaying the total in the center is complex.
               It usually requires a Chart.js plugin or drawing directly on the canvas,
               which isn't directly supported by the basic <x-mary-chart> component setup.
               You might need a more advanced setup or omit this feature if using only <x-mary-chart>.
            --}}
            {{-- <div class="absolute inset-0 flex items-center justify-center text-lg font-semibold text-gray-700">
                 {{ $completed + $ongoing + $pending }}
            </div> --}}
        </div>

        {{-- Data for the legend comes directly from the component's public properties --}}
        <div class="ml-4 space-y-2 flex-1">
            <p class="text-sm text-gray-600 flex items-center">
                <span class="w-3 h-3 bg-orange-500 inline-block rounded-full mr-2"></span>
                Completed: {{ $completed }}
            </p>
            <p class="text-sm text-gray-600 flex items-center">
                <span class="w-3 h-3 bg-green-600 inline-block rounded-full mr-2"></span>
                On-Going: {{ $ongoing }}
            </p>
            <p class="text-sm text-gray-600 flex items-center">
                <span class="w-3 h-3 bg-yellow-400 inline-block rounded-full mr-2"></span>
                Pending: {{ $pending }}
            </p>
        </div>
    </div>

</x-mary-card>

{{-- No <script> block needed here - Mary UI handles Chart.js initialization --}}