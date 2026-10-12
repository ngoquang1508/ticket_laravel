{{-- Thư viện ApexCharts cho biểu đồ Doanh thu theo thời gian --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    /**
     * Xử lý thay đổi khoảng thời gian
     */
    function handlePeriodChange(value) {
        const customRange = document.getElementById('customDateRange');
        if (value === 'custom') {
            if (customRange) customRange.classList.remove('hidden');
        } else {
            if (customRange) customRange.classList.add('hidden');
            document.getElementById('dashboardFilterForm').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const revenueLabels = @json($revenueChartLabels);
        const revenueData = @json($revenueChartData);

        // Hàm format tiền tệ VND
        const formatVND = function (val) {
            if (val === undefined || val === null) return '0 ₫';
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(val);
        };

        // Khởi tạo biểu đồ Doanh thu (Area/Line Chart)
        const revenueChartEl = document.querySelector("#revenueLineChart");
        if (revenueChartEl) {
            const revenueChartOptions = {
                series: [{
                    name: 'Doanh thu',
                    data: revenueData
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    fontFamily: 'inherit',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#2563eb'], // Primary Blue
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                markers: {
                    size: revenueData.length > 30 ? 0 : 3,
                    strokeColors: '#ffffff',
                    strokeWidth: 2,
                    hover: { size: 6 }
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: { left: 10, right: 10, top: 0, bottom: 0 }
                },
                xaxis: {
                    categories: revenueLabels,
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' },
                        rotate: 0,
                        hideOverlappingLabels: true
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' },
                        formatter: function (val) {
                            if (val >= 1000000) {
                                return (val / 1000000).toFixed(1) + ' tr';
                            }
                            if (val >= 1000) {
                                return (val / 1000).toFixed(0) + ' k';
                            }
                            return val;
                        }
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: function (val) {
                            return formatVND(val);
                        }
                    }
                }
            };

            const revenueChart = new ApexCharts(revenueChartEl, revenueChartOptions);
            revenueChart.render();
        }
    });
</script>
