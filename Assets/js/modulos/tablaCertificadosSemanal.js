document.addEventListener("DOMContentLoaded", () => {
  const inputDesde = document.getElementById("filtroDesdeRangoTabla");
  const inputHasta = document.getElementById("filtroHastaRangoTabla");

  inputDesde.addEventListener("change", cargarTablaSemana);
  inputHasta.addEventListener("change", cargarTablaSemana);

  // === Config ===
  const ciudades = ["Calexico", "El Paso", "National City", "Nogales", "San Diego", "Tijuana", "VIN"];

  // Mapa de índice de día (1..7) -> id en español que usas en el HTML
  const diasIdx = { 1: "domingo", 2: "lunes", 3: "martes", 4: "miercoles", 5: "jueves", 6: "viernes", 7: "sabado" };

  // Fallback si backend aún manda nombres de día (inglés/español)
  const dayNameToIdx = {
    // inglés
    "sunday": 1, "monday": 2, "tuesday": 3, "wednesday": 4, "thursday": 5, "friday": 6, "saturday": 7,
    // español
    "domingo": 1, "lunes": 2, "martes": 3, "miércoles": 4, "miercoles": 4, "jueves": 5, "viernes": 6, "sábado": 7, "sabado": 7
  };

  // Normalizador de ciudades recibidas del backend
  function normCiudad(s) {
    return String(s || "").trim().replace(/\s+/g, " ").toLowerCase();
  }
  // Canon para pintar exactamente como tus encabezados
  const canonCiudad = {
    "calexico": "Calexico",
    "el paso": "El Paso",
    "national city": "National City",
    "nogales": "Nogales",
    "san diego": "San Diego",
    "tijuana": "Tijuana",
    "vin": "VIN"
  };

  async function cargarTablaSemana() {
    const desde = inputDesde.value;
    const hasta = inputHasta.value;
    if (!desde || !hasta) return;

    // Validación rápida de rango
    if (hasta < desde) {
      Swal?.fire?.({
        icon: "warning",
        title: "Rango inválido",
        text: "La fecha 'Hasta' no puede ser menor que 'Desde'.",
      }) || alert("Rango inválido: 'Hasta' < 'Desde'");
      return;
    }

    const res = await fetch(base_url + "estadisticas/certificadosPorSemanaPorCiudad", {
      method: "POST",
      body: new URLSearchParams({ desde, hasta }),
    });

    const data = await res.json();
    if (data.error) {
      Swal?.fire?.({ icon: "error", title: "Error", text: data.error }) || alert(data.error);
      return;
    }

    // Inicializa matriz [1..7] x ciudades con 0
    const matriz = {};
    for (let i = 1; i <= 7; i++) {
      matriz[i] = {};
      for (const c of ciudades) matriz[i][c] = 0;
    }

    // Vuelca resultados
    for (const item of data) {
      // Preferimos 'dow' (1..7). Si no existe, convertimos 'dia_semana' a índice.
      let idx = parseInt(item.dow, 10);
      if (!(idx >= 1 && idx <= 7)) {
        const name = String(item.dia_semana || "").toLowerCase();
        idx = dayNameToIdx[name] || null;
      }
      if (!(idx >= 1 && idx <= 7)) continue; // si no se pudo mapear, se ignora

      const ciudadCanon = canonCiudad[normCiudad(item.city)];
      const total = parseInt(item.total, 10) || 0;

      if (ciudadCanon && matriz[idx] && ciudadCanon in matriz[idx]) {
        matriz[idx][ciudadCanon] = total;
      }
    }

    // Totales
    let totalGeneral = 0;
    const totalPorCiudad = Object.fromEntries(ciudades.map(c => [c, 0]));

    // Pintado por día (1..7)
    for (let i = 1; i <= 7; i++) {
      let totalDia = 0;
      for (const ciudad of ciudades) {
        const valor = matriz[i][ciudad] || 0;
        const celdaId = `${diasIdx[i]}${ciudad.replace(/\s/g, "")}`; // p.ej. 'lunesElPaso'
        const celda = document.getElementById(celdaId);
        if (celda) celda.textContent = valor;

        totalDia += valor;
        totalPorCiudad[ciudad] += valor;
      }
      const celdaTotalDia = document.getElementById(`total${capitalize(diasIdx[i])}`); // p.ej. 'totalLunes'
      if (celdaTotalDia) celdaTotalDia.textContent = totalDia;

      totalGeneral += totalDia;
    }

    // Totales por ciudad
    for (const ciudad of ciudades) {
      const celda = document.getElementById(`total${ciudad.replace(/\s/g, "")}`); // p.ej. 'totalElPaso', 'totalVIN'
      if (celda) celda.textContent = totalPorCiudad[ciudad] || 0;
    }

    // Total general
    const celdaTotalGeneral = document.getElementById("totalGeneral");
    if (celdaTotalGeneral) celdaTotalGeneral.textContent = totalGeneral;
  }

  function capitalize(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : str;
  }
});

// --------- Exportar PDF ----------
document.getElementById("imprimirTabla").addEventListener("click", () => {
  const fechaDesde = document.getElementById("filtroDesdeRangoTabla").value;
  const fechaHasta = document.getElementById("filtroHastaRangoTabla").value;

  if (!fechaDesde || !fechaHasta) {
    Swal?.fire?.({
      icon: "warning",
      title: "Rango de fechas requerido",
      text: "Por favor selecciona las fechas 'Desde' y 'Hasta' antes de generar el PDF.",
    }) || alert("Selecciona rango de fechas");
    return;
  }

  const { jsPDF } = window.jspdf || {};
  if (!jsPDF) {
    Swal?.fire?.({ icon: "error", title: "Error", text: "No se cargó jsPDF." }) || alert("No se cargó jsPDF");
    return;
  }
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

  if (typeof doc.autoTable !== "function") {
    Swal?.fire?.({
      icon: "error",
      title: "Error de librería",
      text: "No se cargó jsPDF AutoTable. Revisa que esté incluida la librería.",
    }) || alert("No se cargó jsPDF AutoTable");
    return;
  }

  doc.autoTable({
    startY: 20,
    head: [headers],
    body: rows,
    theme: "grid",
    styles: { fontSize: 9 }
  });

  doc.save(`Certificados_Semanal_${fechaDesde}_a_${fechaHasta}.pdf`);
});
