let tblReporte;

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar DataTable
  tblReporte = $("#tblReporteCertificados").DataTable({
    ajax: {
      url: base_url + "Reportes/listar",
      dataSrc: "",
    },
    columns: [
      { data: "cert_number" },
      { data: "vin" },
      { data: "cliente" },
      { data: "realizado_por" },
      { data: "secomext" },
      { data: "bdd_m" },
      { data: "fecha" },
      { data: "comentario" },
    ],
    language,
    dom,
    buttons,
  });

  // ==============================
  // FILTROS (frontend tipo tu otro módulo)
  // ==============================

  $("#filterCertNumber").on("keyup change", function () {
    tblReporte.column(0).search(this.value).draw();
  });

  $("#filterVin").on("keyup change", function () {
    tblReporte.column(1).search(this.value).draw();
  });

  $("#filterCliente").on("change", function () {
    tblReporte.column(2).search(this.value).draw();
  });

  $("#filterRealizadoPor").on("change", function () {
    tblReporte.column(3).search(this.value).draw();
  });

  $("#filterSecomext").on("change", function () {
    tblReporte.column(4).search(this.value).draw();
  });

  $("#filterBddm").on("change", function () {
    tblReporte.column(5).search(this.value).draw();
  });

  $("#filterFechaInicio, #filterFechaFin").on("change", function () {
    filtrarPorFecha();
  });

  $("#filterComentario").on("keyup change", function () {
    tblReporte.column(7).search(this.value).draw();
  });

  // ==============================
  // FILTRO PERSONALIZADO POR FECHA
  // ==============================
  function filtrarPorFecha() {
    let fechaInicio = document.getElementById("filterFechaInicio").value;
    let fechaFin = document.getElementById("filterFechaFin").value;

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      let fechaTabla = data[6]; // columna FECHA

      if (!fechaInicio && !fechaFin) return true;

      let fecha = new Date(fechaTabla);
      let inicio = fechaInicio ? new Date(fechaInicio) : null;
      let fin = fechaFin ? new Date(fechaFin) : null;

      if (inicio && fecha < inicio) return false;
      if (fin && fecha > fin) return false;

      return true;
    });

    tblReporte.draw();

    // limpiar filtro para no duplicarlo
    $.fn.dataTable.ext.search.pop();
  }

  // ==============================
  // CARGAR FILTROS DINÁMICOS
  // ==============================

  fetch(base_url + "Reportes/obtenerFiltros")
    .then((res) => res.json())
    .then((data) => {
      const clienteSelect = document.querySelector("#filterCliente");
      const realizadoSelect = document.querySelector("#filterRealizadoPor");

      data.clientes.forEach((c) => {
        const option = document.createElement("option");
        option.value = c.cliente;
        option.textContent = c.cliente;
        clienteSelect.appendChild(option);
      });

      data.realizados_por.forEach((r) => {
        const option = document.createElement("option");
        option.value = r.realizado_por;
        option.textContent = r.realizado_por;
        realizadoSelect.appendChild(option);
      });
    });
});
