{{-- 
<div id="gantt-chart"></div>

<script>
    // Example Gantt chart data
    const tasks = @json($tasks);



    // Find the earliest start and latest end dates
    let minStartDate = Infinity;
    let maxEndDate = -Infinity;

    tasks.forEach(task => {
        const startDate = new Date(task.start).getTime();
        const endDate = new Date(task.end).getTime();

        minStartDate = Math.min(minStartDate, startDate);
        maxEndDate = Math.max(maxEndDate, endDate);
    });

    const seriesData = tasks.map(task => ({
        x: task.name,
        y: [
            new Date(task.start).getTime(),
            new Date(task.end).getTime()
        ]
    }));

    const options = {
        series: [{
            data: seriesData
        }],
        chart: {
            height: 350,
            type: 'rangeBar'
        },
        plotOptions: {
            bar: {
                horizontal: true
            }
        },
        xaxis: {
            type: 'datetime',
            min: minStartDate,
            max: maxEndDate
        },
        yaxis: {
            show: false
        }
    };

    const chart = new ApexCharts(document.querySelector("#gantt-chart"), options);
    chart.render();
</script> --}}
