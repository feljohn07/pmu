<div class="p-5 bg-white rounded-lg shadow-md w-80">
    <p>Project Status</p>

    <div class="flex items-center mt-5">
        <!-- Chart Container -->
        <div class="w-[150px] h-[150px] relative">
            <canvas id="donutChart-1"></canvas>
            <div class="absolute inset-0 flex items-center justify-center text-lg font-semibold text-gray-700">
                {{ $summaryStats['completed'] + $summaryStats['ongoing'] + $summaryStats['pending']}}
            </div>
        </div>
        <!-- Text Content -->
        <div class="ml-4 space-y-2 flex-1">
            <p class="text-sm text-gray-600 flex items-center">
                <span class="w-3 h-3  bg-green-600 inline-block rounded-full mr-2"></span>
                Completed: {{ $summaryStats['completed'] }}
            </p>
            <p class="text-sm text-gray-600 flex items-center">
                <span class="w-3 h-3 bg-orange-500 inline-block rounded-full mr-2"></span>
                On-Going: {{ $summaryStats['ongoing'] }}
            </p>
            <p class="text-sm text-gray-600 flex items-center">
                <span class="w-3 h-3 bg-yellow-400 inline-block rounded-full mr-2"></span>
                Pending: {{ $summaryStats['pending'] }}
            </p>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('donutChart-1').getContext('2d');

    var data = [{{ $summaryStats['completed'] }}, {{ $summaryStats['ongoing'] }}, {{ $summaryStats['pending'] }}];
    var colors = ['#008000', '#FFA500', '#FFD700'];

    if (({{ $summaryStats['completed'] }} + {{ $summaryStats['ongoing'] }} + {{ $summaryStats['pending'] }}) == 0) {
        data = [1];
        colors = ['#808080']
    }

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'On-Going', 'Pending'],
            datasets: [{
                data: data,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '50%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true },
            }
        },

    });
</script>