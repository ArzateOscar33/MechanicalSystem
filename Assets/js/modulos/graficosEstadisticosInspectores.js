

document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("filtroInspectorFechaCiudad");
    const input = document.getElementById("filtroFechaCiudad");

    if (select && input) {
        cargarInspectoresPorCiudadYFecha(); // rellena el select

        select.addEventListener("change", actualizarGraficoCiudadInspector);
        input.addEventListener("change", actualizarGraficoCiudadInspector);
    }
});
function cargarInspectoresPorCiudadYFecha() {
    const url = base_url + "estadisticas/inspectoresDisponibles";
    const select = document.getElementById("filtroInspectorFechaCiudad");
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
function actualizarGraficoCiudadInspector() {
    const inspector = document.getElementById("filtroInspectorFechaCiudad").value;
    const fecha = document.getElementById("filtroFechaCiudad").value;

    if (!inspector || !fecha) return;

    const url = base_url + "estadisticas/certificadosPorCiudadPorInspectorYFecha";
    const formData = new FormData();
    formData.append("inspector", inspector);
    formData.append("fecha", fecha);

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);

    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            const ciudades = res.map(item => item.city);
            const cantidades = res.map(item => parseInt(item.total));

            const canvas = document.getElementById("graficoCiudadInspectorFecha");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");

            if (window.chartCiudadInspectorFecha) {
                window.chartCiudadInspectorFecha.destroy();
            }

            window.chartCiudadInspectorFecha = new Chart(ctx, {
                type: "horizontalBar",
                data: {
                    labels: ciudades,
                    datasets: [{
                        label: "Certificados por Ciudad",
                        data: cantidades,
                        backgroundColor: "rgba(40, 167, 69, 0.6)",
                        borderColor: "rgba(40, 167, 69, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins:{
                        datalabels: {
                            anchor: 'end',
                            align: 'left',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: function (value) {
                                return value;
                            }
                        },
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: { beginAtZero: true },
                            scaleLabel: {
                                display: true,
                                labelString: 'Cantidad'
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: 'Ciudad'
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: `Certificados por Ciudad para ${inspector} (${fecha})`
                    }
                }
            });
        }
    };
}

document.getElementById("btnGenerarPdfInspectores")?.addEventListener("click", generarPdfTodosInspectores);

function generarGraficoCiudadInspector(inspector, fecha, hasta = null, callback) {
    const url = hasta === null
        ? base_url + "estadisticas/certificadosPorCiudadPorInspectorYFecha"
        : base_url + "estadisticas/certificadosPorCiudadPorInspectorYRango";

    const formData = new FormData();
    formData.append("inspector", inspector);

    if (hasta === null) {
        formData.append("fecha", fecha);
    } else {
        formData.append("desde", fecha);
        formData.append("hasta", hasta);
    }

    fetch(url, { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            const contenedor = document.getElementById("contenedorGraficosInspectores");
            const safeInspector = inspector.replace(/\s+/g, "_");
            const labelFecha = hasta === null ? fecha : `${fecha} a ${hasta}`;
            const canvasId = `grafico_${safeInspector}_${labelFecha.replace(/[^a-zA-Z0-9]/g, "_")}`;

            const canvasWrapper = document.createElement("div");
            canvasWrapper.style.marginBottom = "40px";
            canvasWrapper.innerHTML = `
                <h5>${inspector} - ${labelFecha}</h5>
                <canvas id="${canvasId}" width="600" height="300"></canvas>
            `;
            contenedor.appendChild(canvasWrapper);

            const ctx = document.getElementById(canvasId).getContext("2d");

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: data.map(item => item.city),
                    datasets: [{
                        label: `Certificados por Ciudad`,
                        data: data.map(item => item.total),
                        backgroundColor: "rgba(75, 192, 192, 0.6)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        datalabels: {
                            anchor: 'top',
                            align: 'inside',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: value => value
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: `${inspector} - ${labelFecha}`
                        }
                    },
                    responsive: false,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            setTimeout(callback, 10);
        });
}



function generarPdfTodosInspectores() {
    Swal.fire({
        title: 'Selecciona una fecha',
        html: `
            <input type="date" id="fechaGraficoPDF" class="form-control" max="${new Date().toISOString().split('T')[0]}">
        `,
        confirmButtonText: 'Generar PDF',
        focusConfirm: false,
        preConfirm: () => {
            const fecha = document.getElementById('fechaGraficoPDF').value;
            if (!fecha) {
                Swal.showValidationMessage('Por favor selecciona una fecha');
            }
            return fecha;
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        const fecha = result.value;

        // 🔔 Mostrar alerta de carga INMEDIATAMENTE
        Swal.fire({
            title: 'Generando PDF...',
            html: 'Esto puede tardar unos segundos',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        //  Esperar al siguiente frame para que se dibuje el loading
        setTimeout(() => {
            const url = base_url + "estadisticas/inspectoresDisponibles";
            fetch(url)
                .then(res => res.json())
                .then(inspectores => {
                    const contenedor = document.getElementById("contenedorGraficosInspectores");
                    contenedor.innerHTML = "";

                    let index = 0;

                    const procesarSiguiente = () => {
                        if (index >= inspectores.length) {
                            capturarGraficosEnPdf(fecha);
                            return;
                        }

                        const inspector = inspectores[index].inspector_name;
                        generarGraficoCiudadInspector(inspector, fecha,null, () => {
                            index++;
                            setTimeout(procesarSiguiente, 10);
                        });
                    };

                    procesarSiguiente();
                });
        }, 100); // <= esto permite que el spinner se dibuje ANTES
    });
}



async function capturarGraficosEnPdf(fechaInput) {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    const contenedor = document.getElementById("contenedorGraficosInspectores");
    contenedor.style.display = "block";

    Swal.fire({
        title: 'Generando PDF...',
        html: 'Esto puede tardar unos segundos',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const children = contenedor.children;
    const pageHeight = 297;
    const margin = 10;
    const spacing = 10;
    let currentY = margin;

    for (let i = 0; i < children.length; i++) {
        const div = children[i];

        div.style.display = "block";
        div.offsetHeight;

        const canvas = await html2canvas(div, {
            useCORS: true,
            scale: 1,
            allowTaint: false
        });

        const imgData = canvas.toDataURL("image/png");
        const imgProps = pdf.getImageProperties(imgData);
        const pageWidth = 210;
        const pdfWidth = pageWidth - 2 * margin;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        if (currentY + pdfHeight > pageHeight - margin) {
            pdf.addPage();
            currentY = margin;
        }

        pdf.addImage(imgData, "PNG", margin, currentY, pdfWidth, pdfHeight);
        currentY += pdfHeight + spacing;
    }

    contenedor.style.display = "none";
    Swal.close();

    // ✅ usar directamente la fecha recibida
    pdf.save(`Graficos_${fechaInput}.pdf`);

    Swal.fire({
        icon: 'success',
        title: 'PDF generado',
        text: 'El archivo se ha descargado correctamente.',
        timer: 1200,
        showConfirmButton: false
    });
}

function generarPdfInspectoresPorRango() {
    Swal.fire({
        title: 'Selecciona el rango de fechas',
        html: `
            <label>Desde:</label>
            <input type="date" id="fechaInicio" class="form-control mb-2" max="${new Date().toISOString().split('T')[0]}">
            <label>Hasta:</label>
            <input type="date" id="fechaFin" class="form-control" max="${new Date().toISOString().split('T')[0]}">
        `,
        confirmButtonText: 'Generar PDF',
        focusConfirm: false,
        preConfirm: () => {
            const desde = document.getElementById('fechaInicio').value;
            const hasta = document.getElementById('fechaFin').value;

            if (!desde || !hasta) {
                Swal.showValidationMessage('Selecciona ambas fechas');
                return false;
            }

            if (hasta < desde) {
                Swal.showValidationMessage('La fecha final no puede ser menor que la inicial');
                return false;
            }

            return { desde, hasta };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        const { desde, hasta } = result.value;

        Swal.fire({
            title: 'Generando PDF...',
            html: 'Esto puede tardar unos segundos',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            const url = `${base_url}estadisticas/inspectoresPorRango?desde=${desde}&hasta=${hasta}`;
            fetch(url)
                .then(res => res.json())
                .then(inspectores => {
                    const contenedor = document.getElementById("contenedorGraficosInspectores");
                    contenedor.innerHTML = "";

                    let index = 0;

                    const procesarSiguiente = () => {
                        if (index >= inspectores.length) {
                            capturarGraficosEnPdf(`${desde}_a_${hasta}`);
                            return;
                        }

                        const inspector = inspectores[index].inspector_name;
                        generarGraficoCiudadInspector(inspector, desde, hasta, () => {
                            index++;
                            setTimeout(procesarSiguiente, 10);
                        });
                    };

                    procesarSiguiente();
                });
        }, 100);
    });
}


document.addEventListener('DOMContentLoaded', () => {
    cargarInspectoresEnSelectRango();

    const selects = [
        document.getElementById('filtroInspectorRango'),
        document.getElementById('filtroDesdeRango'),
        document.getElementById('filtroHastaRango')
    ];

    selects.forEach(element => {
        element.addEventListener('change', generarGraficoCiudadRangoIndividual);
    });
});

function cargarInspectoresEnSelectRango() {
    fetch(base_url + "estadisticas/inspectoresDisponibles")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById("filtroInspectorRango");
            data.forEach(inspector => {
                const option = document.createElement("option");
                option.value = inspector.inspector_name;
                option.textContent = inspector.inspector_name;
                select.appendChild(option);
            });
        });
}

function generarGraficoCiudadRangoIndividual() {
    const inspector = document.getElementById("filtroInspectorRango").value;
    const desde = document.getElementById("filtroDesdeRango").value;
    const hasta = document.getElementById("filtroHastaRango").value;

    if (!inspector || !desde || !hasta || hasta < desde) {
        return; // No hace nada hasta que todo esté correcto
    }

    const formData = new FormData();
    formData.append("inspector", inspector);
    formData.append("desde", desde);
    formData.append("hasta", hasta);

    fetch(base_url + "estadisticas/certificadosPorCiudadPorInspectorYRango", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById("graficoCiudadInspectorRango").getContext("2d");
            if (window.graficoCiudadRango) {
                window.graficoCiudadRango.destroy();
            }

            window.graficoCiudadRango = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: data.map(item => item.city),
                    datasets: [{
                        label: "Certificados",
                        data: data.map(item => item.total),
                        backgroundColor: "rgba(75, 192, 192, 0.6)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        datalabels: {
                            anchor: 'top',
                            align: 'inside',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: value => value
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: `${inspector} (${desde} a ${hasta})`
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
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
                        backgroundColor: "rgba(121, 175, 19, 0.2)",
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#ff0',
                        font: {
                            weight: 'bold'
                        },
                        formatter: function (value) {
                            return value;
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: function (value) {
                                return value;
                            }
                        },
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
                        datalabels: {
                        anchor: 'bottom',
                        align: 'inside',
                        color: '#000',
                        font: {
                            weight: 'bold'
                        },
                        formatter: function (value) {
                            return value;
                        }
                    },
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


document.addEventListener("DOMContentLoaded", function () {
    const inputFecha = document.getElementById("filtroFechaDia");
    if (inputFecha) {
        inputFecha.addEventListener("change", function () {
            const fecha = this.value;
            if (fecha) certificadosInspectorPorFecha(fecha);
        });
    }
});
function certificadosInspectorPorFecha(fecha) {
    const url = base_url + "estadisticas/certificadosPorInspectorPorFecha";
    const formData = new FormData();
    formData.append("fecha", fecha);

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);

    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const inspectores = res.map(item => item.inspector_name);
            const cantidades = res.map(item => parseInt(item.total));

            const canvas = document.getElementById("graficoInspectorPorFecha");
            if (!canvas) return;
            const ctx = canvas.getContext("2d");

            if (window.inspectorFechaChartInstance) {
                window.inspectorFechaChartInstance.destroy();
            }

            window.inspectorFechaChartInstance = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: inspectores,
                    datasets: [{
                        label: "Certificados realizados por día",
                        data: cantidades,
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Cantidad de Certificados'
                            }
                        }]
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#000',
                            font: {
                                weight: 'bold'
                            },
                            formatter: function (value) {
                                return value;
                            }
                        }
                    }
                }
            });
        }
    };
}
