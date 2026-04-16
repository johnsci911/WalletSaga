<div class="w-full max-w-4xl rounded-2xl bg-slate-700 p-4 shadow-md">
    <h2 class="mb-4 text-center font-fantasque text-xl text-slate-100">Yearly Financial Trend</h2>
    <div id="yearly-trend-chart-{{ $this->getId() }}" style="height: 400px"></div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initYearlyTrendChart_{{$this->getId()}}() {
            const chartElement = document.getElementById('yearly-trend-chart-{{ $this->getId() }}');
            if (!chartElement) return;

            // Store chart instance on window to prevent garbage collection and enable resize
            const chartId = 'yearlyTrendChart_{{$this->getId()}}';
            if (window[chartId]) {
                window[chartId].destroy();
            }

            // Clear any existing content
            chartElement.innerHTML = '';

            const weeklyBalances = @js($weeklyBalances);

            // Build labels: show month name on first week of each month, otherwise just the day
            let lastMonth = '';
            const labels = weeklyBalances.map(item => {
                const parts = item.week.split(' ');
                const month = parts[0];
                if (month !== lastMonth) {
                    lastMonth = month;
                    return item.week;
                }
                return parts[1];
            });

            // Calculate fractional x-positions for the 1st of each month
            const monthFirstPositions = [];
            const weekStarts = weeklyBalances.map(item => new Date(item.weekStart + 'T00:00:00'));
            const seenMonths = new Set();

            for (let i = 0; i < weekStarts.length; i++) {
                const ws = weekStarts[i];
                const we = new Date(ws);
                we.setDate(we.getDate() + 6);

                // Check each day in this week for a month 1st
                for (let d = new Date(ws); d <= we; d.setDate(d.getDate() + 1)) {
                    if (d.getDate() === 1) {
                        const monthKey = d.getFullYear() + '-' + d.getMonth();
                        if (seenMonths.has(monthKey)) continue;
                        seenMonths.add(monthKey);

                        // Skip if the 1st falls on the very first data point (no line needed at chart start)
                        const dayOffset = (d - ws) / (1000 * 60 * 60 * 24);
                        if (i === 0 && dayOffset < 1) continue;

                        monthFirstPositions.push(i + (dayOffset / 7));
                    }
                }
            }

            // Plugin to draw vertical dotted lines at the 1st of each month
            const monthBoundaryPlugin = {
                id: 'monthBoundaries_' + chartId,
                afterDraw(chart) {
                    const ctx = chart.ctx;
                    const xScale = chart.scales.x;
                    const yScale = chart.scales.y;

                    monthFirstPositions.forEach(pos => {
                        const x = xScale.getPixelForDecimal(pos / (weeklyBalances.length - 1));

                        ctx.save();
                        ctx.beginPath();
                        ctx.setLineDash([4, 4]);
                        ctx.strokeStyle = 'rgba(148, 163, 184, 0.4)';
                        ctx.lineWidth = 1;
                        ctx.moveTo(x, yScale.top);
                        ctx.lineTo(x, yScale.bottom);
                        ctx.stroke();
                        ctx.restore();
                    });
                }
            };

            // Create canvas element for the chart
            const canvas = document.createElement('canvas');
            chartElement.appendChild(canvas);

            const ctx = canvas.getContext('2d');

            window[chartId] = new Chart(ctx, {
                type: 'line',
                plugins: [monthBoundaryPlugin],
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Balance',
                            data: weeklyBalances.map(item => item.balance),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointBorderCapStyle: 'round',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Earnings',
                            data: weeklyBalances.map(item => item.earnings),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointBorderCapStyle: 'round',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            fill: false,
                            borderDash: [5, 5]
                        },
                        {
                            label: 'Expenses',
                            data: weeklyBalances.map(item => item.expenses),
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            borderCapStyle: 'round',
                            pointBorderCapStyle: 'round',
                            pointBorderWidth: 2,
                            pointRadius: 3,
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
                                text: 'Week'
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
        initYearlyTrendChart_{{$this->getId()}}();

        // Listen for Livewire component updates
        window.addEventListener('livewire:update', function() {
            setTimeout(initYearlyTrendChart_{{$this->getId()}}, 100);
        });

        // Handle Livewire navigation (wire:navigate)
        window.addEventListener('livewire:navigating', function() {
            const chartId = 'yearlyTrendChart_{{$this->getId()}}';
            if (window[chartId]) {
                window[chartId].destroy();
                delete window[chartId];
            }
        });

        window.addEventListener('livewire:navigated', function() {
            setTimeout(initYearlyTrendChart_{{$this->getId()}}, 100);
        });

        // Handle browser back/forward buttons (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                setTimeout(initYearlyTrendChart_{{$this->getId()}}, 100);
            }
        });

        // Handle responsive resize events
        window.addEventListener('resize', function() {
            const chartId = 'yearlyTrendChart_{{$this->getId()}}';
            if (window[chartId]) {
                window[chartId].resize();
            }
        });
    </script>
    @endpush
</div>