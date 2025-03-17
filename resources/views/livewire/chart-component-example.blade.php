<div wire:ignore class="p-5 bg-gray-100 rounded-lg shadow-md w-80">
    <p>{{ $chartName }}</p>

    <div class="flex items-center mt-5">
        <!-- Chart Container -->
        <div class="w-[150px] h-[150px]">
            <canvas id="donutChart-{{ $chartId }}"></canvas>
        </div>
        <!-- Text Content -->
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
</div>


<script>

    var ctx = document.getElementById('donutChart-{{ $chartId }}').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'On-Going', 'Pending'],
            datasets: [{
                data: [{{ $completed }}, {{ $ongoing }}, {{ $pending }}],
                backgroundColor: ['#FFA500', '#008000', '#FFD700']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false }
            }
        }
    });

</script>