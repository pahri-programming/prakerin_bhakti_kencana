document.addEventListener("DOMContentLoaded", function () {
    var chart = {
        series: [
            {
                name: "Selling Product",
                data: [28, 120, 36, 90, 38, 85,],
            },
            {
                name: "Followers",
                data: [50, 100, 65, 140, 32, 60],
            },
            {
                name: "Campaign",
                data: [100, 50, 130, 70, 135, 80],
            },
        ],
        chart: {
            toolbar: {
                show: false,
            },
            type: "line",
            fontFamily: "inherit",
            foreColor: "#adb0bb",
            height: 200,
        },
        colors: ["var(--bs-danger)", "var(--bs-primary)", "var(--bs-secondary)"],
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
        },
        stroke: {
            curve: "smooth",
            width: 3,
        },
        grid: {
            borderColor: "rgba(0,0,0,0.1)",
            strokeDashArray: 3,
            xaxis: {
                lines: {
                    show: false,
                },
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 0,
            },
        },
        xaxis: {
            categories: [
                "1-10 Aug",
                "11-20 Aug",
                "21-30 Aug",
                "1-10 Sept",
                "11-20 Sept",
                "21-30 Sept",
            ],
        },
        yaxis: {
            tickAmount: 4,
        },
        tooltip: {
            theme: "dark",
        },
    };

    var chart = new ApexCharts(document.querySelector("#financial"), chart);
    chart.render();

    var chart2 = {
        series: [
            {
                name: "Hours",
                data: [22.5, 34.3, 24.7, 28.5, 11.4, 30.6, 44.5],
            },
        ],
        chart: {
            height: 350,
            type: "area",
            fontFamily: "inherit",
            foreColor: "#adb0bb",
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true
            },
            dropShadow: {
                enabled: true,
                top: 3,
                left: 0,
                blur: 5,
                color: "#000",
                opacity: 0.2,
            },
        },
        colors: ["var(--bs-primary)"],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
            width: 2,
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 0,
                inverseColors: false,
                opacityFrom: 0.1,
                opacityTo: 0,
                stops: [20, 180],
            },
        },
        markers: {
            show: false,
        },
        grid: {
            show: false,
        },
        yaxis: {
            show: false,
        },
        xaxis: {
            type: "category",
            categories: ["Su", "Mo", "Tu", "Wed", "Th", "Fr", "Sa"],

            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        legend: {
            show: false,
        },
        tooltip: {
            theme: "dark",
        },
    };

    var chart2 = new ApexCharts(
        document.querySelector("#activity-status"),
        chart2
    );
    chart2.render();

    var chart3 = {
        chart: {
            type: "radialBar",
            fontFamily: "inherit",
            foreColor: "#adb0bb",
            height: 305,
        },
        series: [45, 50, 60, 70],
        colors: ["var(--bs-primary)", "var(--bs-danger)", "var(--bs-warning)", "var(--bs-secondary)"],
        plotOptions: {
            radialBar: {
                hollow: {
                    margin: 15,
                    size: "50%"
                },
                dataLabels: {
                    total: {
                        show: true,
                        label: 'Team'
                    }
                }
            }
        },
        fill: {
            type: "gradient",
            gradient: {
                shade: "dark",
                type: "vertical",
                gradientToColors: ["#615dff"],
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: "round",
        },
        labels: ['Team A', 'Team B', 'Team C', 'Team D'],
        tooltip: {
            enabled: true,
            theme: "dark",
            fillSeriesColor: false,
        },
    };

    new ApexCharts(
        document.querySelector("#team-performance"),
        chart3
    ).render();
});
