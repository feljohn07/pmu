<div style="width: 500px;"> 
    <canvas id="myChart"></canvas>

    <script>
        // Example data (replace with your actual data)
        const chartData = [
            { date: '2023-11-01', value: 10 },
            { date: '2023-11-02', value: 15 },
            { date: '2023-11-03', value: 8 },
            { date: '2023-11-04', value: 22 },
            { date: '2023-11-05', value: 12 },
            { date: '2023-11-06', value: 18 },
            { date: '2023-11-07', value: 25 },
            { date: '2023-11-08', value: 20 },
            { date: '2023-11-09', value: 16 },
            { date: '2023-11-10', value: 28 },
        ];

        // Extract labels (dates) and values from the data
        const labels = chartData.map(item => item.date);
        const values = chartData.map(item => item.value);

        // Get the canvas element
        const ctx = document.getElementById('myChart').getContext('2d');

        // Create the chart
        const myChart = new Chart(ctx, {
            type: 'pie', // You can change the chart type (e.g., 'bar', 'pie')
            data: {
                labels: labels,
                datasets: [{
                    label: 'My Data',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Example of updating the chart data dynamically
        function updateChart(newData) {
            myChart.data.labels = newData.map(item => item.date);
            myChart.data.datasets[0].data = newData.map(item => item.value);
            myChart.update();
        }

        //Example of changing the chart type.
        function changeChartType(type) {
            myChart.config.type = type;
            myChart.update();
        }

        //Example usage of updateChart.
        //setTimeout(()=>{
        //    const newChartData = [
        //      { date: '2023-11-11', value: 30 },
        //      { date: '2023-11-12', value: 35 },
        //      { date: '2023-11-13', value: 28 },
        //    ];
        //    updateChart(newChartData);
        //}, 3000);

        //Example usage of changeChartType.
        //setTimeout(()=>{
        //    changeChartType('bar');
        //}, 5000);

    </script>
</div>