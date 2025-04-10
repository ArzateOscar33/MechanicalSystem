
ciudadesCertificados();
estadosCertificado();

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
            console.log(this.responseText); // Esto te ayuda a ver la respuesta en la consola
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
