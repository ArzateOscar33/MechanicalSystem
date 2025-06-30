document.addEventListener("DOMContentLoaded", function () {
    duplicadosPorCiudad();
    duplicadosPorEstado();
    duplicadosCiudadMensual();
});

function duplicadosPorCiudad() {
    const url = base_url + "estadisticas/duplicadosPorCiudad";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();

    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let ciudades = [];
            let porcentajes = [];

            for (let i = 0; i < res.length; i++) {
                ciudades.push(res[i]['city']);
                porcentajes.push(parseFloat(res[i]['porcentaje']).toFixed(2));
            }

            const canvas = document.getElementById("ciudadesDuplicadosGrafico");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");

            let gradients = generarGradientes(ctx, ciudades.length);
            let backgroundColors = ciudades.map((_, i) => gradients[i % gradients.length]);

            new Chart(ctx, {
                type: "pie",
                data: {
                    labels: ciudades,
                    datasets: [{
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        data: porcentajes,
                        borderWidth: 1,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            anchor: 'inside',
                            align: 'end',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: value => `${value}%`
                        },
                        legend: {
                            position: "bottom",
                            labels: { boxWidth: 10 }
                        }, 
                        tooltip: {
                            callbacks: {
                                label: context => `${context.label}: ${context.raw}%`
                            }
                        }
                    }
                }
            });
        }
    };
}
function generarGradientes(ctx, cantidad) {
    const g1 = ctx.createLinearGradient(0, 0, 0, 300);
    g1.addColorStop(0, "#ee0979");
    g1.addColorStop(1, "#ff6a00");

    const g2 = ctx.createLinearGradient(0, 0, 0, 300);
    g2.addColorStop(0, "#283c86");
    g2.addColorStop(1, "#39bd3c");

    const g3 = ctx.createLinearGradient(0, 0, 0, 300);
    g3.addColorStop(0, "#7f00ff");
    g3.addColorStop(1, "#e100ff");

    return [g1, g2, g3];
}

function duplicadosPorEstado() {
    const url = base_url + "estadisticas/duplicadosPorEstado";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();

    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            let estados = [];
            let porcentajes = [];

            for (let i = 0; i < res.length; i++) {
                estados.push(res[i]['state']);
                porcentajes.push(parseFloat(res[i]['porcentaje']).toFixed(2));
            }

            const canvas = document.getElementById("estadosGraficoDuplicados");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");

            let gradients = generarGradientes(ctx, estados.length);
            let backgroundColors = estados.map((_, i) => gradients[i % gradients.length]);

            new Chart(ctx, {
                type: "pie",
                data: {
                    labels: estados,
                    datasets: [{
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        data: porcentajes,
                        borderWidth: 1,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            anchor: 'inside',
                            align: 'end',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: value => `${value}%`
                        },
                        legend: {
                            position: "bottom",
                            labels: { boxWidth: 10 }
                        },
                        tooltip: {
                            callbacks: {
                                label: context => `${context.label}: ${context.raw}%`
                            }
                        }
                    }
                }
            });
        }
    };
}


function duplicadosCiudadMensual() {
    const url = base_url + "estadisticas/duplicadosMensualesPorCiudad";
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const canvas = document.getElementById("duplicadosCiudadMensual");
            if (!canvas) return;

            const ctx = canvas.getContext("2d");

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels.map(m => formatearMes(m)), // ejemplo: "2025-04" → "Abr"
                    datasets: data.datasets
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    stacked: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Duplicados por Ciudad (Mensual)',
                                

                        },
                                                datalabels: {
                            anchor: 'end',
                            align: 'upper',
                            color: '#000',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.dataset.label}: ${ctx.formattedValue}`
                            }
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de duplicados'
                            }
                        }
                    }
                }
            });
        });
}

// Convertir "2025-04" → "Abr"
function formatearMes(fecha) {
    const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
    const [a, m] = fecha.split("-");
    return meses[parseInt(m) - 1] + " " + a;
}

