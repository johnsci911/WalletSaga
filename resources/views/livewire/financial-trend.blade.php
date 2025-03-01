<div class="w-full max-w-4xl bg-slate-700 p-4 rounded-2xl shadow-md">
    <h2 class="text-xl text-center mb-4">30-Day Financial Trend</h2>
    <div id="trend-chart" style="height: 400px;"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Balance',
                data: [500, 700, 600, 800, 950, 1100, 1050, 1200, 1300, 1150]
            }, {
                name: 'Earnings',
                data: [1000, 1200, 1100, 1300, 1400, 1600, 1500, 1700, 1800, 1650]
            }, {
                name: 'Expenses',
                data: [50, 500, 500, 500, 450, 500, 450, 500, 500, 200]
            }],
            chart: {
                type: 'line',
                height: 400,
                background: '#334155', // Slate-700
                foreColor: '#cbd5e1', // Slate-300
            },
            xaxis: {
                categories: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7', 'Day 8', 'Day 9', 'Day 10'],
                labels: {
                    style: {
                        colors: '#cbd5e1', // Slate-300
                    },
                },
            },
            yaxis: {
                title: {
                    text: 'Amount',
                    style: {
                        color: '#cbd5e1', // Slate-300
                    },
                },
                labels: {
                    style: {
                        colors: '#cbd5e1', // Slate-300
                    },
                },
            },
            colors: ['#3b82f6', '#22c55e', '#ef4444'], // Blue, Green, Red
            stroke: {
                curve: 'smooth'
            },
            legend: {
                labels: {
                    colors: '#cbd5e1', // Slate-300
                },
            },
            grid: {
                borderColor: '#475569', // Slate-600
            },
            tooltip: {
                theme: 'dark',
            },
        };

        var chart = new ApexCharts(document.querySelector("#trend-chart"), options);
        chart.render();
    });
</script>
@endpush
