<div class="p-5 bg-white rounded-lg shadow-md w-[500px]">
    <p>{{ $chartName }}</p>

    <div class="mt-5">
        <!-- Chart Container -->
        <div class="w-full h-48">
            <canvas id="barChart-{{ $chartId }}"></canvas>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('barChart-{{ $chartId }}').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['For Approval', 'Approved', 'No POW'],
            datasets: [{
                data: [ {{ $forApproval }}, {{ $approved }}, {{ $noPow }}],
                backgroundColor: ['#FFA500', '#008000']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true },
            }
        }
    });
</script>