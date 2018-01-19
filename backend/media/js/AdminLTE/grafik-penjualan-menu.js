$(function() {
    "use strict";           
    
    var chart_penjualan = new Highcharts.Chart({
        chart: {
            renderTo: "chart-penjualan-menu-11",
            type: "column"
        },
        title: {
            text: "Penjualan Menu Bulan November"
        },
        legend: {
            enabled: false
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah'
            }
        },
        series: [
            {
                name: "Jumlah",
                data: [54, 54, 50, 48, 45, 44, 44, 41, 30, 22]
            }
        ],
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        colors: ['#F39C12'],
        xAxis: {
            categories: [
                "Baso Tahu", "Bebek Bakar", "Cappucino", "Ice Tea", "Mie Baso Urat", "Ice Lemon Tea",
                "Tenderloin Steak", "Milk Shake", "Hot Chocolate", "Double Espresso"
            ]
        },
        credits: {
            enabled: false
        }
    });
    
    var chart_penjualan2 = new Highcharts.Chart({
        chart: {
            renderTo: "chart-penjualan-menu-12",
            type: "column"
        },
        title: {
            text: "Penjualan Menu Bulan Desember"
        },
        legend: {
            enabled: false
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah'
            }
        },
        series: [
            {
                name: "Jumlah",
                data: [65, 54, 48, 48, 47, 45, 42, 41, 35, 24]
            }
        ],
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        colors: ['#00C0EF'],
        xAxis: {
            categories: [
                "Ice Tea", "Baso Tahu", "Cappucino", "Mie Baso Urat", "Tenderloin Steak", "Bebek Bakar",
                "Milk Shake", "Ice Lemon Tea", "Hot Chocolate", "Juice Mango"
            ]
        },
        credits: {
            enabled: false
        }
    });    
});