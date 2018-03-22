$(document).ready(function(){
	alert("Shazam!");
	/*
	var MONTHS = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
	var config = {
            type: 'line',
            data: {
                labels: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                datasets: [{
                    label: "Compras",
					backgroundColor:  "rgba(0, 0, 255,0.5)",
					borderColor:  "rgba(0, 0, 255,0.5)",
                    data: [<?php foreach($array["Compras"] as $compra){ echo $compra .",";}?>],
                    fill: false,
                },  {
					fill: false,
                    label: "Vendas",
					backgroundColor:  "rgba(255, 0, 0,0.5)",
					borderColor:  "rgba(255, 0, 0,0.5)",
                    data: [<?php foreach($array["Vendas"] as $venda){ echo $venda .",";}?>],
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:true,
                    text:'Balanço de Compras/Vendas - GYCam'
                },
                tooltips: {
                    mode: 'label',
                },
                hover: {
                    mode: 'dataset'
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            show: true,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            show: true,
                            labelString: 'Value'
                        },
                    }]
                }
            }
        };
        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx, config);
        };
	*/
});