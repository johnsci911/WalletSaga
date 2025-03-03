<div class="w-full max-w-4xl bg-slate-700 p-4 rounded-2xl shadow-md">
    <h2 class="text-xl text-center mb-4">30-Day Financial Trend</h2>
    <div id="trend-chart" style="height: 400px;"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var dailyBalances = @json($dailyBalances);

        var dates    = [];
        var earnings = [];
        var expenses = [];
        var balances = [];

        dailyBalances.forEach(function(item) {
            dates.push(item.date);
            earnings.push(parseFloat(item.earnings));
            expenses.push(parseFloat(item.expenses));
            balances.push(parseFloat(item.balance));
        });

        var options = {
            series: [{
                name: 'Balance',
                data: balances
            }, {
                name: 'Earnings',
                data: earnings
            }, {
                name: 'Expenses',
                data: expenses
            }],
            chart: {
                type: 'line',
                height: 400,
                background: '#334155', // Slate-700
                foreColor: '#cbd5e1', // Slate-300
                zoom: {
                    enabled: true,
                    type: 'xy',
                    autoScaleYaxis: true
                },
                selection: {
                    enabled: false
                },
                toolbar: {
                    tools: {
                        download: false,
                        selection: false,
                        zoom: false,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                }
            },
            xaxis: {
                categories: dates,
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
                    formatter: function (value) {
                        return '$' + value.toFixed(2);
                    }
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
                y: {
                    formatter: function(value) {
                        return '$' + value.toFixed(2);
                    }
                }
            },
        };

        var chart = new ApexCharts(document.querySelector("#trend-chart"), options);
        chart.render();
    });
</script>
@endpush
