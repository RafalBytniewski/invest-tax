const formatAxisDate = (dateString, rangeDays) => {
    if (!dateString) {
        return '';
    }

    const date = new Date(dateString);

    if (Number.isNaN(date.getTime())) {
        return dateString;
    }

    const day = date.getDate();
    const monthShort = date.toLocaleDateString('en-US', { month: 'short' });
    const year = date.getFullYear();

    if (rangeDays <= 185) {
        return `${day} ${monthShort}`;
    }

    if (rangeDays <= 370) {
        return `${monthShort} '${String(year).slice(-2)}`;
    }

    return `${monthShort} ${year}`;
};

const calculateRangeDays = (data, range) => {
    if (range !== 'all') {
        return Number(range) * 365;
    }

    if (!data || data.length < 2) {
        return 0;
    }

    const firstDate = new Date(data[0].date).getTime();
    const lastDate = new Date(data[data.length - 1].date).getTime();

    return Math.max(0, Math.round((lastDate - firstDate) / (1000 * 60 * 60 * 24)));
};

const filterByRange = (chartData, range) => {
    if (range === 'all') {
        return chartData;
    }

    const lastDate = new Date(chartData[chartData.length - 1].date);
    const fromDate = new Date(lastDate);
    fromDate.setFullYear(fromDate.getFullYear() - Number(range));

    const filtered = chartData.filter((point) => new Date(point.date) >= fromDate);

    return filtered.length ? filtered : chartData;
};

const setActiveButton = (buttons, range) => {
    buttons.forEach((button) => {
        const isActive = button.dataset.years === String(range);

        button.classList.toggle('border-blue-600', isActive);
        button.classList.toggle('bg-blue-600', isActive);
        button.classList.toggle('text-white', isActive);
        button.classList.toggle('border-gray-200', !isActive);
        button.classList.toggle('text-gray-600', !isActive);
        button.classList.toggle('dark:border-zinc-700', !isActive);
        button.classList.toggle('dark:text-zinc-300', !isActive);
    });
};

const createHoverHighlightPlugin = (getVisibleData, currency, money) => ({
    id: 'dividendXAxisHoverHighlight',
    afterDraw(chart) {
        const activeElement = chart.getActiveElements()[0];

        if (!activeElement) {
            return;
        }

        const { ctx, chartArea, scales } = chart;
        const xScale = scales.x;
        const yScale = scales.y;
        const activeDataset = chart.data.datasets[activeElement.datasetIndex];
        const activePoint = activeDataset?.data?.[activeElement.index];
        const hoveredIndex = Math.round(Number(activePoint?.x ?? activeElement.index));
        const hoveredValue = Number(activePoint?.y);
        const rawDate = getVisibleData()[hoveredIndex]?.date;

        if (!rawDate || !Number.isFinite(hoveredValue)) {
            return;
        }

        const x = xScale.getPixelForValue(hoveredIndex);
        const y = yScale.getPixelForValue(hoveredValue);

        ctx.save();
        ctx.beginPath();
        ctx.rect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
        ctx.clip();
        ctx.setLineDash([4, 4]);
        ctx.lineWidth = 1;
        ctx.strokeStyle = 'rgba(156, 163, 175, 0.55)';
        ctx.beginPath();
        ctx.moveTo(x, chartArea.top);
        ctx.lineTo(x, chartArea.bottom);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(chartArea.left, y);
        ctx.lineTo(chartArea.right, y);
        ctx.stroke();
        ctx.restore();

        const date = new Date(rawDate);
        const dateLabel = Number.isNaN(date.getTime()) ? rawDate : date.toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
        const valueLabel = `${money.format(hoveredValue)} ${currency}`.trim();

        ctx.save();
        ctx.font = '500 11px sans-serif';

        const labelWidth = ctx.measureText(dateLabel).width + 16;
        const labelHeight = 22;
        const valueLabelWidth = ctx.measureText(valueLabel).width + 16;
        const valueLabelHeight = 22;
        const labelX = Math.min(Math.max(x - labelWidth / 2, chartArea.left), chartArea.right - labelWidth);
        const labelY = xScale.top + 5;
        const valueLabelX = Math.min(Math.max(yScale.left + 4, 0), chart.width - valueLabelWidth - 2);
        const valueLabelY = Math.min(Math.max(y - valueLabelHeight / 2, chartArea.top), chartArea.bottom - valueLabelHeight);

        ctx.fillStyle = '#2563eb';
        ctx.beginPath();
        if (ctx.roundRect) {
            ctx.roundRect(labelX, labelY, labelWidth, labelHeight, 5);
            ctx.roundRect(valueLabelX, valueLabelY, valueLabelWidth, valueLabelHeight, 5);
        } else {
            ctx.rect(labelX, labelY, labelWidth, labelHeight);
            ctx.rect(valueLabelX, valueLabelY, valueLabelWidth, valueLabelHeight);
        }
        ctx.fill();
        ctx.fillStyle = '#ffffff';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(dateLabel, labelX + labelWidth / 2, labelY + labelHeight / 2);
        ctx.fillText(valueLabel, valueLabelX + valueLabelWidth / 2, valueLabelY + valueLabelHeight / 2);
        ctx.restore();
    },
});

const initializeDividendCharts = () => {
    if (!window.Chart) {
        return;
    }

    document.querySelectorAll('[data-dividend-chart]').forEach((canvas) => {
        window.Chart.getChart(canvas)?.destroy();

        const chartData = JSON.parse(canvas.dataset.chartData);
        const currency = canvas.dataset.dividendCurrency || '';
        const chartId = canvas.dataset.dividendChart;
        const buttons = document.querySelectorAll(`[data-dividend-chart-range="${chartId}"]`);
        const money = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        let visibleChartData = chartData;

        const chart = new window.Chart(canvas, {
            type: 'line',
            data: {
                datasets: [{
                    label: `Amount per Share (${currency})`,
                    data: chartData.map((point, index) => ({ x: index, y: point.amount })),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.10)',
                    borderWidth: 2,
                    tension: 0.25,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'nearest',
                    intersect: true,
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 0,
                        max: Math.max(chartData.length - 1, 0),
                        ticks: {
                            color: '#a1a1aa',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8,
                            callback: (value) => {
                                const point = visibleChartData[Math.round(value)];

                                return point ? formatAxisDate(point.date, calculateRangeDays(visibleChartData, 'all')) : '';
                            },
                        },
                        grid: {
                            color: 'rgba(161, 161, 170, 0.12)',
                        },
                    },
                    y: {
                        position: 'right',
                        beginAtZero: true,
                        ticks: {
                            color: '#a1a1aa',
                            callback: (value) => `${money.format(value)} ${currency}`,
                        },
                        grid: {
                            color: 'rgba(161, 161, 170, 0.12)',
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        displayColors: false,
                        callbacks: {
                            title: (items) => visibleChartData[Math.round(items[0]?.parsed.x)]?.date ?? '',
                            label: (context) => `Amount: ${money.format(context.parsed.y)} ${currency}`.trim(),
                        },
                    },
                },
            },
            plugins: [createHoverHighlightPlugin(() => visibleChartData, currency, money)],
        });

        const updateChart = (range) => {
            visibleChartData = filterByRange(chartData, range);
            const rangeDays = calculateRangeDays(visibleChartData, range);

            chart.data.datasets[0].data = visibleChartData.map((point, index) => ({
                x: index,
                y: point.amount,
            }));
            chart.options.scales.x.max = Math.max(visibleChartData.length - 1, 0);
            chart.options.scales.x.ticks.callback = (value) => {
                const point = visibleChartData[Math.round(value)];

                return point ? formatAxisDate(point.date, rangeDays) : '';
            };
            chart.update();
            setActiveButton(buttons, range);
        };

        buttons.forEach((button) => {
            button.onclick = () => updateChart(button.dataset.years);
        });

        updateChart('all');
    });
};

document.addEventListener('DOMContentLoaded', initializeDividendCharts);
document.addEventListener('livewire:navigated', initializeDividendCharts);

document.addEventListener('livewire:init', () => {
    window.Livewire.hook('morph.updated', initializeDividendCharts);
});
