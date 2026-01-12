<div class="w-full max-w-4xl rounded-2xl bg-slate-700 p-4 shadow-md">
    <h2 class="mb-4 text-center font-fantasque text-xl text-slate-100">Monthly Financial Trend</h2>
    <div id="trend-chart-{{ $this->getId() }}" style="height: 400px"></div>
</div>

<script>
    (function () {
        const renderChart = () => {
            const chartElement = document.getElementById('trend-chart-{{ $this->getId() }}');

            if (!chartElement) return;

            // Wait for ApexCharts to be available
            if (typeof window.ApexCharts === 'undefined') {
                setTimeout(renderChart, 50);
                return;
            }

            // Prevent duplicate charts if function runs multiple times
            if (chartElement.querySelector('.apexcharts-canvas')) {
                chartElement.innerHTML = '';
            }

            const dailyBalances = @js($dailyBalances);

            const options = {
                series: [
                    {
                        name: 'Balance',
                        data: dailyBalances.map((item) => parseFloat(item.balance)),
                    },
                    {
                        name: 'Earnings',
                        data: dailyBalances.map((item) => parseFloat(item.earnings)),
                    },
                    {
                        name: 'Expenses',
                        data: dailyBalances.map((item) => parseFloat(item.expenses)),
                    },
                ],
                chart: {
                    type: 'line',
                    height: 400,
                    background: '#334155', // Slate-700
                    foreColor: '#cbd5e1', // Slate-300
                    zoom: {
                        enabled: true,
                        type: 'xy',
                        autoScaleYaxis: true,
                    },
                    selection: {
                        enabled: false,
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: false,
                            selection: false,
                            zoom: false,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true,
                        },
                    },
                },
                xaxis: {
                    categories: dailyBalances.map((item) => item.date),
                    labels: {
                        style: {
                            colors: '#cbd5e1',
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: 'Amount',
                        style: {
                            color: '#cbd5e1',
                        },
                    },
                    labels: {
                        style: {
                            colors: '#cbd5e1',
                        },
                        formatter: (value) => '$' + value.toFixed(2),
                    },
                },
                colors: ['#3b82f6', '#22c55e', '#ef4444'], // Blue, Green, Red
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                legend: {
                    labels: {
                        colors: '#cbd5e1',
                    },
                },
                grid: {
                    borderColor: '#475569', // Slate-600
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: (value) => '$' + value.toFixed(2),
                    },
                },
            };

            const chart = new window.ApexCharts(chartElement, options);
            chart.render();
        };

        // Initial render attempt
        renderChart();

        // Handle Livewire navigation
        document.addEventListener('livewire:navigated', renderChart, {
            once: true,
        });
    })();
</script>
