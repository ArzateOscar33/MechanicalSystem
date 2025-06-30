document.addEventListener("DOMContentLoaded", () => {
    const inputDesde = document.getElementById("filtroDesdeRangoTabla");
    const inputHasta = document.getElementById("filtroHastaRangoTabla");

    inputDesde.addEventListener("change", cargarTablaSemana);
    inputHasta.addEventListener("change", cargarTablaSemana);

    async function cargarTablaSemana() {
        const desde = inputDesde.value;
        const hasta = inputHasta.value;

        if (!desde || !hasta) return;

        const res = await fetch(base_url + "estadisticas/certificadosPorSemanaPorCiudad", {
            method: "POST",
            body: new URLSearchParams({ desde, hasta }),
        });

        const data = await res.json();
        if (data.error) {
            alert(data.error);
            return;
        }

        const ciudades = ["Calexico", "El Paso", "National City", "Nogales", "San Diego","Tijuana"];
        const dias = {
            "Monday": "lunes",
            "Tuesday": "martes",
            "Wednesday": "miercoles",
            "Thursday": "jueves",
            "Friday": "viernes",
            "Saturday": "sabado",
            "Sunday": "domingo"
        };

        // Inicializa matriz de datos
        const matriz = {};
        for (const d in dias) {
            matriz[d] = {};
            for (const c of ciudades) {
                matriz[d][c] = 0;
            }
        }

        for (const item of data) {
            const dia = item.dia_semana;
            const ciudad = item.city;
            const total = parseInt(item.total);

            if (matriz[dia] && matriz[dia][ciudad] !== undefined) {
                matriz[dia][ciudad] = total;
            }
        }

        let totalGeneral = 0;
        const totalPorCiudad = {
            "Calexico": 0,
            "El Paso": 0,
            "National City": 0,
            "Nogales": 0,
            "San Diego": 0,
            "Tijuana": 0,
        };

        // Llenar la tabla
        for (const [diaIng, diaEsp] of Object.entries(dias)) {
            let totalDia = 0;

            for (const ciudad of ciudades) {
                const valor = matriz[diaIng][ciudad];
                const celdaId = `${diaEsp}${ciudad.replace(/\s/g, "")}`;
                const celda = document.getElementById(celdaId);
                if (celda) celda.textContent = valor;

                totalDia += valor;
                totalPorCiudad[ciudad] += valor;
            }

            const celdaTotalDia = document.getElementById(`total${capitalize(diaEsp)}`);
            if (celdaTotalDia) celdaTotalDia.textContent = totalDia;

            totalGeneral += totalDia;
        }

        // Llenar totales por ciudad
        for (const ciudad of ciudades) {
            const celda = document.getElementById(`total${ciudad.replace(/\s/g, "")}`);
            if (celda) celda.textContent = totalPorCiudad[ciudad];
        }

        // Llenar total general
        const celdaTotalGeneral = document.getElementById("totalGeneral");
        if (celdaTotalGeneral) {
            celdaTotalGeneral.textContent = totalGeneral;
        }
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
});

 document.getElementById("imprimirTabla").addEventListener("click", () => {
    const fechaDesde = document.getElementById("filtroDesdeRangoTabla").value;
    const fechaHasta = document.getElementById("filtroHastaRangoTabla").value;

    if (!fechaDesde || !fechaHasta) {
        Swal.fire({
            icon: "warning",
            title: "Rango de fechas requerido",
            text: "Por favor selecciona las fechas 'Desde' y 'Hasta' antes de generar el PDF.",
        });
        return;
    }

    // Esperar a que jsPDF esté listo
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const encabezado = `Certificados por Ciudad - Semana del ${fechaDesde} al ${fechaHasta}`;
    doc.setFontSize(12);
    doc.text(encabezado, 14, 15);

    const tabla = document.getElementById("tblEstadisticasSemanal");
    const rows = [];

    // Encabezados
    const headers = Array.from(tabla.querySelectorAll("thead th")).map(th => th.textContent.trim());

    // Filas del cuerpo
    const filas = tabla.querySelectorAll("tbody tr");
    filas.forEach(tr => {
        const fila = Array.from(tr.querySelectorAll("td")).map(td => td.textContent.trim());
        rows.push(fila);
    });

    // Verifica si autoTable está disponible
    if (typeof doc.autoTable !== "function") {
        Swal.fire({
            icon: "error",
            title: "Error de librería",
            text: "No se cargó correctamente jsPDF AutoTable. Revisa que esté incluida la librería.",
        });
        return;
    }

    doc.autoTable({
        startY: 20,
        head: [headers],
        body: rows,
        theme: 'grid',
        styles: { fontSize: 9 },
    });

    doc.save(`Certificados_Semanal_${fechaDesde}_a_${fechaHasta}.pdf`);
});

