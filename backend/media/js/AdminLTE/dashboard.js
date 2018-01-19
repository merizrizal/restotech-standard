$(function() {
    "use strict";           
    
    var chart_penjualan = new Highcharts.Chart({
        chart: {
            renderTo: "chart-penjualan",
            type: "line"
        },
        title: {
            text: "Penjualan Tahun 2014"
        },
        legend: {
            enabled: false
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Nilai Penjualan (Rupiah)'
            }
        },
        series: [
            {
                name: "Nilai",
                data: [9786000, 7786000, 6380000, 8562000, 7486000, 7865000, 8985600]
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
        colors: ['#F56954'],
        xAxis: {
            categories: [
                'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
            ]
        },
        credits: {
            enabled: false
        }
    });
    
    var chart_menu = new Highcharts.Chart({
        chart: {
            renderTo: "chart-menu",
            type: "column",
        },
        title: {
            text: "Top Menu 2014"
        },
        legend: {
            enabled: false
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Jumlah Penjualan'
            }
        },
        series: [{
            name: "Jumlah",
            data: [
                44, 50, 51,
                46, 53, 42,
                43, 45, 49
            ]              
        }],
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        xAxis: {
            categories: [{
                name: "Okt",
                categories: ["Baso Tahu", "Ice Lemon Tea", "Juice Mango"]
            }, {
                name: "Nov",
                categories: ["Baso Tahu", "Juice Mango", "Cappucino"]
            }, {
                name: "Des",
                categories: ["Ice Lemon Tea", "Baso Tahu", "Cappucino"]
            }]
        },
        credits: {
            enabled: false
        }
    });   
});