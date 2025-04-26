{{-- <x-line-chart-card chartName="POW Status" approved="{{ $summaryStats['approved'] ?? '0' }}"
    for-approval="{{ $summaryStats['for-approval'] ?? '0' }}" no-pow="{{ $summaryStats['no-pow'] ?? '0' }}" /> --}}

<div class="p-5 bg-white rounded-lg shadow-md w-[500px]">
    <p>POW Status</p>

    <div class="mt-5">
        <!-- Chart Container -->
        <div class="w-full h-48">
            <canvas id="barChart"></canvas>
        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('barChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['For Approval', 'Approved', 'No POW'],
            datasets: [{
                data: [ {{ $summaryStats['for-approval'] ?? '0' }}, {{$summaryStats['approved'] ?? '0' }},  {{ $summaryStats['no-pow'] ?? '0' }}],
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