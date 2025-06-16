document.addEventListener("DOMContentLoaded", function () {
 
  tblDuplicados = $("#tblDuplicados").DataTable({
    ajax: {
      url: base_url + "Estadisticas/listarDuplicados",
      dataSrc: "",
    },
    columns: [
      { data: "cert_number", title: "Certificado" },	
      { data: "vin", title: "VIN" },
      { data: "make", title: "Marca" },
      { data: "model", title: "Modelo" },
      { data: "year", title: "Año" },
      { data: "owner_name", title: "Propietario" },
      { data: "address_id", title: "Dirección" },
      { data: "inspector_name", title: "Inspector" },
      { data: "city", title: "Ciudad" },
      { data: "state", title: "Estado" },
      //{ data: "accion", title: "Acciones" },
    ],
    language,
    dom,
    buttons,
  });
  $("#tblDuplicadosCantidadDeIncidencias").DataTable({
    ajax: {
      url: base_url + "estadisticas/listarCantidadIncidencias",
      dataSrc: "",
    },
    columns: [
      { data: "inspector", title: "Nombre del Inspector" },
    
      { data: "certificados_duplicados", title: "Certificados Duplicados" },
      //{data: "accion", title: "Acciones"},
    ],
    language,
    dom,
    buttons,
  });
 
});