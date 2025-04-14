fechaChart();
ciudadesCertificados();
estadosCertificado();
certificadosPorInspector();


function ciudadesCertificados() {
    const url = base_url + "admin/ciudadesCertificados";
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
                porcentajes.push(parseFloat(res[i]['porcentaje']).toFixed(2));  // 2 decimales
            }

            var ctx = document.getElementById("ciudadesGrafico").getContext("2d");

            // Gradientes originales
            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, "#ee0979");
            gradientStroke1.addColorStop(1, "#ff6a00");

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, "#283c86");
            gradientStroke2.addColorStop(1, "#39bd3c");

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, "#7f00ff");
            gradientStroke3.addColorStop(1, "#e100ff");

            // Repetimos los gradientes si hay más de 3 ciudades
            const gradients = [gradientStroke1, gradientStroke2, gradientStroke3];
            const backgroundColors = ciudades.map((_, i) => gradients[i % gradients.length]);

            var myChart = new Chart(ctx, {
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
                        legend: {
                            position: "bottom",
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return `${label}: ${parseFloat(value).toFixed(2)}%`;
                                }
                            }
                        }
                    }
                }
            });
        }
    };
}


function estadosCertificado() {
    const url = base_url + "admin/estadosCertificados";  // Cambié la URL para que apunte al método correcto
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    
    http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            //console.log(this.responseText); // Esto te ayuda a ver la respuesta en la consola
            const res = JSON.parse(this.responseText);
            let estados = [];
            let porcentajes = [];

            // Iteramos sobre los datos recibidos
            for (let i = 0; i < res.length; i++) {
                estados.push(res[i]['state']);  // Aquí usamos 'state' para obtener el nombre del estado
                porcentajes.push(res[i]['cantidad']);  // Usamos 'porcentaje' para obtener el porcentaje
            }

            var ctx = document.getElementById("estadosGrafico").getContext("2d");

            // Crear gradientes para los colores del gráfico
            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, "#ee0979");
            gradientStroke1.addColorStop(1, "#ff6a00");

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, "#283c86");
            gradientStroke2.addColorStop(1, "#39bd3c");

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, "#7f00ff");
            gradientStroke3.addColorStop(1, "#e100ff");

            // Crear el gráfico de barras con los datos y gradientes
            var myChart = new Chart(ctx, {
                type: "bar",  // Cambiar a 'bar' para un gráfico de barras
                data: {
                    labels: estados,  // Las etiquetas serán los estados
                    datasets: [{
                        backgroundColor: [
                            gradientStroke1,
                            gradientStroke2,
                            gradientStroke3,
                        ],  // Colores de las barras
                        hoverBackgroundColor: [
                            gradientStroke1,
                            gradientStroke2,
                            gradientStroke3,
                        ],  // Colores cuando se pasa el mouse
                        data: porcentajes,  // Los datos de porcentaje
                        borderWidth: 1,  // Borde de las barras
                    }],
                },
                options: {
                    maintainAspectRatio: false,  // Permite que el gráfico se adapte al tamaño del contenedor
                    scales: {
                        y: {
                            beginAtZero: true,  // Asegura que las barras empiecen desde cero en el eje Y
                        },
                    },
                    legend: {
                        display: false,  // No es necesario mostrar la leyenda para un gráfico de barras
                    },
                    tooltips: {
                        displayColors: false,  // No mostrar los colores en el tooltip
                    },
                    responsive: true,  // Hace que el gráfico sea responsivo
                },
            });
        }
    };
}
function fechaChart() {
    const url = base_url + "admin/certificadosPorMes"; 
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();

    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            //console.log("Respuesta:", this.responseText);
            const res = JSON.parse(this.responseText);
            const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];


            // Crear objeto dinámico para almacenar datos por año
            let datosPorAnio = {
                2024: new Array(12).fill(0),
                2025: new Array(12).fill(0),
                2026: new Array(12).fill(0)  // Deja espacio preparado para 2026
            };

            for (let i = 0; i < res.length; i++) {
                const anio = res[i]['anio'];
                const mes = res[i]['mes'] - 1;
                const total = parseInt(res[i]['total']);
                if (!datosPorAnio[anio]) {
                    datosPorAnio[anio] = new Array(12).fill(0); // Por si llega otro año
                }
                datosPorAnio[anio][mes] = total;
            }

            var ctx = document.getElementById("fechaChart").getContext("2d");

            const datasets = [
                {
                    label: "2024",
                    data: datosPorAnio[2024],
                    borderColor: "#007bff",
                    backgroundColor: "rgba(0, 123, 255, 0.1)",
                    fill: false,
                    tension: 0.4
                },
                {
                    label: "2025",
                    data: datosPorAnio[2025],
                    borderColor: "#ff4d4d",
                    backgroundColor: "rgba(255, 77, 77, 0.1)",
                    fill: false,
                    tension: 0.4
                },
                {
                    label: "2026",
                    data: datosPorAnio[2026],
                    borderColor: "#28a745",
                    backgroundColor: "rgba(40, 167, 69, 0.1)",
                    fill: false,
                    tension: 0.4
                }
            ];

            var myChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: meses,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Certificados'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: "top"
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: true,
                            text: "Certificados por Mes (2024 - 2026)"
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    };
}
function certificadosPorInspector() {
    const url = base_url + "admin/certificadosPorInspector";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();

    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            //console.log(this.responseText);
            let inspectores = [];
            let cantidades = [];

            for (let i = 0; i < res.length; i++) {
                inspectores.push(res[i]['inspector_name']);
                cantidades.push(parseInt(res[i]['total']));
            }

            var ctx = document.getElementById("inspectoresChart").getContext("2d");

            // Gradientes
            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, "#ee0979");
            gradientStroke1.addColorStop(1, "#ff6a00");

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, "#283c86");
            gradientStroke2.addColorStop(1, "#39bd3c");

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, "#7f00ff");
            gradientStroke3.addColorStop(1, "#e100ff");

            const gradients = [gradientStroke1, gradientStroke2, gradientStroke3];
            const backgroundColors = inspectores.map((_, i) => gradients[i % gradients.length]);

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: inspectores,
                    datasets: [{
                        data: cantidades,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        borderWidth: 1,
                        // ✅ le damos nombre si se necesita, pero no se muestra
                        label: "Certificados"
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false // 🔇 no mostrar leyenda
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.label}: ${context.raw} certificados`;
                                }
                            }
                        }
                    }
                }
            });
        }
    };
}





 