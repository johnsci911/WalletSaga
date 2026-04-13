<div class="w-full max-w-4xl rounded-2xl bg-slate-700 p-4 shadow-md">
    <h2 class="mb-4 text-center font-fantasque text-xl text-slate-100">Monthly Financial Trend</h2>
    <div id="trend-chart-{{ $this->getId() }}" style="height: 400px"></div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initTrendChart_{{$this->getId()}}() {
            const chartElement = document.getElementById('trend-chart-{{ $this->getId() }}');
            if (!chartElement) return;

            // Store chart instance on window to prevent garbage collection and enable resize
            const chartId = 'trendChart_{{$this->getId()}}';
            if (window[chartId]) {
                window[chartId].destroy();
            }

            // Clear any existing content
            chartElement.innerHTML = '';

            const dailyBalances = @js($dailyBalances);

            // Create canvas element for the chart
            const canvas = document.createElement('canvas');
            chartElement.appendChild(canvas);

            const ctx = canvas.getContext('2d');

            window[chartId] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dailyBalances.map(item => item.date),
                    datasets: [
                        {
                            label: 'Balance',
                            data: dailyBalances.map(item => item.balance),
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Earnings',
                            data: dailyBalances.map(item => item.earnings),
                            borderColor: '#10b981', // emerald-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: false,
                            borderDash: [5, 5]
                        },
                        {
                            label: 'Expenses',
                            data: dailyBalances.map(item => item.expenses),
                            borderColor: '#ef4444', // red-500
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            fill: false,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Amount ($)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                }
            });
        }

        // Render chart when DOM is ready
        initTrendChart_{{$this->getId()}}();

        // Listen for Livewire component updates
        window.addEventListener('livewire:update', function() {
            setTimeout(initTrendChart_{{$this->getId()}}, 100);
        });

        // Handle Livewire navigation (wire:navigate)
        window.addEventListener('livewire:navigating', function() {
            const chartId = 'trendChart_{{$this->getId()}}';
            if (window[chartId]) {
                window[chartId].destroy();
                delete window[chartId];
            }
        });

        window.addEventListener('livewire:navigated', function() {
            setTimeout(initTrendChart_{{$this->getId()}}, 100);
        });

        // Handle browser back/forward buttons (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                setTimeout(initTrendChart_{{$this->getId()}}, 100);
            }
        });

        // Handle responsive resize events
        window.addEventListener('resize', function() {
            const chartId = 'trendChart_{{$this->getId()}}';
            if (window[chartId]) {
                window[chartId].resize();
            }
        });
    </script>
    @endpush
</div>
