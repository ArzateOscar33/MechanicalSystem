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
            const canvas = document.getElementById("ciudadesGrafico");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            //var ctx = document.getElementById("ciudadesGrafico").getContext("2d");

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

    http.onreadystatechange = function () {
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
            const canvas = document.getElementById("estadosGrafico");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            //var ctx = document.getElementById("estadosGrafico").getContext("2d");

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
            const gradients = [gradientStroke1, gradientStroke2, gradientStroke3];
            const backgroundColors = estados.map((_, i) => gradients[i % gradients.length]);
            // Crear el gráfico de barras con los datos y gradientes
            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: estados,
                    datasets: [{
                        data: porcentajes,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors,
                        borderWidth: 1,


                    }]
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
            const canvas = document.getElementById("fechaChart");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            //  var ctx = document.getElementById("fechaChart").getContext("2d");

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
            const canvas = document.getElementById("inspectoresChart");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            // var ctx = document.getElementById("inspectoresChart").getContext("2d");

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


                    }]
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

function certificadosPorDia(anio, mes, estado = '', ciudad = '') {
    const url = base_url + "admin/certificadosPorDia";
    const http = new XMLHttpRequest();
    const formData = new FormData();
    formData.append("anio", anio);
    formData.append("mes", mes);
    formData.append("estado", estado);
    formData.append("ciudad", ciudad);

    http.open("POST", url, true);
    http.send(formData);

    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const dias = res.map(item => item.dia);
            const totales = res.map(item => parseInt(item.total));
            const canvas = document.getElementById("certificadosDiaChart");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            //const ctx = document.getElementById("certificadosDiaChart").getContext("2d");

            if (window.diaChartInstance) {
                window.diaChartInstance.destroy();
            }

            window.diaChartInstance = new Chart(ctx, {
                type: "line",
                data: {
                    labels: dias,
                    datasets: [{
                        label: "Certificados por Día",
                        data: totales,
                        borderColor: "#17a2b8",
                        backgroundColor: "rgba(23, 162, 184, 0.2)",
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                maxRotation: 90,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: "index",
                            intersect: false
                        },
                        title: {
                            display: true,
                            text: "Distribución Diaria de Certificados"
                        }
                    }
                }
            });
        }
    };
}
document.addEventListener("DOMContentLoaded", function () {
    // Comunes para todos
    fechaChart();
    ciudadesCertificados();
    estadosCertificado();
    certificadosPorInspector();

    // === Condicionales por elementos ===

    // filtroMes, filtroEstado, filtroCiudad
    if (document.getElementById("filtroMes")) {
        configurarFiltroMes();
        aplicarFiltro();

        const filtroMes = document.getElementById("filtroMes");
        const filtroEstado = document.getElementById("filtroEstado");
        const filtroCiudad = document.getElementById("filtroCiudad");

        filtroMes.addEventListener("change", aplicarFiltro);
        filtroEstado.addEventListener("change", aplicarFiltro);
        filtroCiudad.addEventListener("change", aplicarFiltro);

        cargarEstados();
        cargarCiudades();
    }

    // filtros para inspector por mes
    if (document.getElementById("filtroInspector") && document.getElementById("filtroAnioInspector")) {
        cargarAniosInspector();
        cargarInspectores();

        const filtroInspector = document.getElementById("filtroInspector");
        const filtroAnioInspector = document.getElementById("filtroAnioInspector");

        if (filtroInspector && filtroAnioInspector) {
            filtroInspector.addEventListener("change", aplicarFiltroInspector);
            filtroAnioInspector.addEventListener("change", aplicarFiltroInspector);
        }

    }

    // filtro inspector por ciudad
    if (document.getElementById("filtroInspectorCiudad")) {
        cargarInspectoresCiudad();
        document.getElementById("filtroInspectorCiudad").addEventListener("change", function () {
            const inspector = this.value;
            if (inspector) certificadosPorCiudadPorInspector(inspector);
        });
    }
});




function cargarEstados() {
    const select = document.getElementById("filtroEstado");
    if (!select) return;

    const url = base_url + "admin/getEstados";
    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.state;
                option.textContent = item.state;
                select.appendChild(option);
            });
        });
}


function cargarCiudades() {
    const select = document.getElementById("filtroCiudad");
    if (!select) return;

    const url = base_url + "admin/getCiudades";
    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.city;
                option.textContent = item.city;
                select.appendChild(option);
            });
        });
}

function bloquearCiudad() {
    const estado = document.getElementById("filtroEstado").value;
    const ciudad = document.getElementById("filtroCiudad");

    ciudad.disabled = estado !== "";
    if (estado !== "") ciudad.value = "";
}

function bloquearEstado() {
    const ciudad = document.getElementById("filtroCiudad").value;
    const estado = document.getElementById("filtroEstado");

    estado.disabled = ciudad !== "";
    if (ciudad !== "") estado.value = "";
}

function aplicarFiltro() {
    const fecha = document.getElementById("filtroMes").value;
    const estado = document.getElementById("filtroEstado").value;
    const ciudad = document.getElementById("filtroCiudad").value;

    if (fecha) {
        const [anio, mes] = fecha.split("-");
        certificadosPorDia(anio, mes, estado, ciudad);
    }
}
function configurarFiltroMes() {
    const filtroMes = document.getElementById("filtroMes");
    if (!filtroMes) return;

    const fechaHoy = new Date();
    const anio = fechaHoy.getFullYear();
    const mes = String(fechaHoy.getMonth() + 1).padStart(2, '0');
    const hoy = `${anio}-${mes}`;

    filtroMes.min = "2024-12";
    filtroMes.max = hoy;
    filtroMes.value = hoy;
}

function cargarInspectores() {
    const url = base_url + "admin/inspectoresDisponibles";
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById("filtroInspector");
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.inspector_name;
                option.textContent = item.inspector_name;
                select.appendChild(option);
            });
        });
}

function cargarAniosInspector() {
    const select = document.getElementById("filtroAnioInspector");
    if (!select) return;

    const anioActual = new Date().getFullYear();
    for (let i = 2024; i <= anioActual; i++) {
        const option = document.createElement("option");
        option.value = i;
        option.textContent = i;
        select.appendChild(option);
    }
    select.value = anioActual;
}


function certificadosPorMesInspector(anio, inspector) {
    const url = base_url + "admin/certificadosPorMesInspector";
    const formData = new FormData();
    formData.append("anio", anio);
    formData.append("inspector", inspector);

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);

    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
            let datos = new Array(12).fill(0);

            res.forEach(item => {
                const index = parseInt(item.mes) - 1;
                datos[index] = parseInt(item.total);
            });
            const canvas = document.getElementById("certificadosInspectorChart");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            //  const ctx = document.getElementById("certificadosInspectorChart").getContext("2d");
            if (window.inspectorChartInstance) {
                window.inspectorChartInstance.destroy();
            }

            window.inspectorChartInstance = new Chart(ctx, {
                type: "line",
                data: {
                    labels: meses,
                    datasets: [{
                        label: "Certificados por Mes",
                        data: datos,
                        borderColor: "#ffc107",
                        backgroundColor: "rgba(255, 193, 7, 0.2)",
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: "Actividad del Inspector"
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    };
}


function aplicarFiltroInspector() {
    const anio = document.getElementById("filtroAnioInspector").value;
    const inspector = document.getElementById("filtroInspector").value;

    if (inspector) {
        certificadosPorMesInspector(anio, inspector);
    }
}
function actualizarOrigenInspector(inspector) {
    const url = base_url + "admin/lugarInspector";
    const formData = new FormData();
    formData.append("inspector", inspector);

    fetch(url, {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            const origen = data && data.city && data.state
                ? `${data.city}, ${data.state}`
                : "No disponible";
            document.getElementById("origenTexto").textContent = origen;
        })
        .catch(() => {
            document.getElementById("origenTexto").textContent = "Error al obtener origen";
        });
}

function aplicarFiltroInspector() {
    const anio = document.getElementById("filtroAnioInspector").value;
    const inspector = document.getElementById("filtroInspector").value;

    if (inspector) {
        certificadosPorMesInspector(anio, inspector);
        actualizarOrigenInspector(inspector);
    }
}

function certificadosPorCiudadPorInspector(inspector) {
    const url = base_url + "admin/certificadosPorCiudadPorInspector";
    const formData = new FormData();
    formData.append("inspector", inspector);

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);

    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const ciudades = res.map(item => item.city);
            const cantidades = res.map(item => parseInt(item.cantidad_certificados));
            const canvas = document.getElementById("ciudadesInspectorGrafico");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            //const ctx = document.getElementById("ciudadesInspectorGrafico").getContext("2d");

            if (window.ciudadInspectorChartInstance) {
                window.ciudadInspectorChartInstance.destroy();
            }

            window.ciudadInspectorChartInstance = new Chart(ctx, {
                type: "bar",  // Usamos un gráfico de barras
                data: {
                    labels: ciudades,  // Las ciudades en el eje X
                    datasets: [{
                        label: 'Certificados por Inspector',  // Nombre de la serie de datos
                        data: cantidades,  // El número de certificados en el eje Y
                        backgroundColor: "rgba(23, 162, 184, 0.5)",  // Color de las barras
                        borderColor: "rgba(23, 162, 184, 1)",  // Color de los bordes de las barras
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,  // Comienza desde 0 en el eje Y
                            title: {
                                display: true,
                                text: 'Cantidad de Certificados'  // Título del eje Y
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Ciudades'  // Título del eje X
                            },
                            ticks: {
                                maxRotation: 45,  // Rotación máxima de los nombres de las ciudades
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false  // No necesitamos la leyenda en este gráfico
                        },
                        title: {
                            display: true,
                            text: 'Certificados por Ciudad (Filtrado por Inspector)'  // Título del gráfico
                        }
                    }
                }
            });
        }
    };
}
const selectInspectorCiudad = document.getElementById("filtroInspectorCiudad");
if (selectInspectorCiudad) {
    selectInspectorCiudad.addEventListener("change", function () {
        const inspector = this.value;
        if (inspector) {
            certificadosPorCiudadPorInspector(inspector);
        }
    });
}

function cargarInspectoresCiudad() {
    const select = document.getElementById("filtroInspectorCiudad");
    if (!select) return;

    const url = base_url + "admin/inspectoresDisponibles";
    fetch(url)
        .then(res => res.json())
        .then(data => {
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.inspector_name;
                option.textContent = item.inspector_name;
                select.appendChild(option);
            });
        });
}






