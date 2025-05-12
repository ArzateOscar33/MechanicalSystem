let tblCertificados;

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar DataTable
  tblCertificados = $("#tblCertificados").DataTable({
    ajax: {
      url: base_url + "buscarCertificados/listar",
      dataSrc: "",
    },
    columns: [
      { data: "cert_number" },
      { data: "vin" },
      { data: "owner_name" },
      { data: "inspector_name" },
      { data: "make" },
      //{ data: "zip_file_path" },
      { data: "city" },
      { data: "state" },
      { data: "zip" },
      { data: "mfg_in" },
      { data: "accion" },
    ],
    language,
    dom,
    buttons,
  });

  // Filtros por columnas
  $("#filterCertNumber").on("keyup change", function () {
    tblCertificados.column(0).search(this.value).draw();
  });

  $("#filterVin").on("keyup change", function () {
    tblCertificados.column(1).search(this.value).draw();
  });

  $("#filterOwner").on("change", function () {
    tblCertificados.column(2).search(this.value).draw();
  });

  $("#filterInspector").on("change", function () {
    tblCertificados.column(3).search(this.value).draw();
  });

  $("#filterMake").on("keyup change", function () {
    tblCertificados.column(4).search(this.value).draw();
  });

  $("#filterCiudad").on("change", function () {
    tblCertificados.column(5).search(this.value).draw();
  });

  $("#filterEstado").on("change", function () {
    tblCertificados.column(6).search(this.value).draw();
  });

  $("#filterZip").on("keyup change", function () {
    tblCertificados.column(8).search(this.value).draw();
  });

  $("#filterMfgIn").on("change", function () {
    tblCertificados.column(9).search(this.value).draw();
  });

  // Llenar dinámicamente filtros de ciudad, estado, propietario, inspector y origen
  fetch(base_url + "buscarCertificados/obtenerFiltros")
    .then((res) => res.json())
    .then((data) => {
      const ciudadSelect = document.querySelector("#filterCiudad");
      const estadoSelect = document.querySelector("#filterEstado");
      const propietarioSelect = document.querySelector("#filterOwner");
      const inspectorSelect = document.querySelector("#filterInspector");
      const mfgInSelect = document.querySelector("#filterMfgIn");

      data.ciudades.forEach((ciudad) => {
        const option = document.createElement("option");
        option.value = ciudad.city;
        option.textContent = ciudad.city;
        ciudadSelect.appendChild(option);
      });

      data.estados.forEach((estado) => {
        const option = document.createElement("option");
        option.value = estado.state;
        option.textContent = estado.state;
        estadoSelect.appendChild(option);
      });

      data.propietarios.forEach((prop) => {
        const option = document.createElement("option");
        option.value = prop.owner_name;
        option.textContent = prop.owner_name;
        propietarioSelect.appendChild(option);
      });

      data.inspectores.forEach((insp) => {
        const option = document.createElement("option");
        option.value = insp.inspector_name;
        option.textContent = insp.inspector_name;
        inspectorSelect.appendChild(option);
      });

      data.origenes.forEach((ori) => {
        const option = document.createElement("option");
        option.value = ori.mfg_in;
        option.textContent = ori.mfg_in;
        mfgInSelect.appendChild(option);
      });

      // Lógica de desactivación mutua entre ciudad y estado
      ciudadSelect.addEventListener("change", function () {
        estadoSelect.disabled = this.value !== "";
      });

      estadoSelect.addEventListener("change", function () {
        ciudadSelect.disabled = this.value !== "";
      });
    });
});

// Función para descargar certificado
function descargarCertificado(cert_number) {
  Swal.fire({
    title: "Aviso?",
    text: "Desea Descargar Certificado " + cert_number + "?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, Descargar!",
  }).then((result) => {
    if (result.isConfirmed) {
      const verificarUrl = base_url + "buscarCertificados/verificarArchivo/" + cert_number;

      const verificarHttp = new XMLHttpRequest();
      verificarHttp.open("GET", verificarUrl, true);
      verificarHttp.send();
      verificarHttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          try {
            const data = JSON.parse(this.responseText);

            if (data.existe) {
              const descargarUrl = base_url + "buscarCertificados/descargar/" + cert_number;

              const descargarHttp = new XMLHttpRequest();
              descargarHttp.open("GET", descargarUrl, true);
              descargarHttp.send();
              descargarHttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                  try {
                    const res = JSON.parse(this.responseText);

                    if (res.icono === "success" && res.url) {
                      const a = document.createElement("a");
                      a.href = res.url;
                      a.download = "";
                      document.body.appendChild(a);
                      a.click();
                      document.body.removeChild(a);
                    } else {
                      Swal.fire("Error", res.msg, "error");
                    }
                  } catch (e) {
                    console.error("Error al parsear respuesta de descarga:", this.responseText);
                  }
                }
              };
            } else {
              Swal.fire("Error", data.msg, "error");
            }
          } catch (e) {
            console.error("Error al parsear respuesta de verificación:", this.responseText);
          }
        }
      };
    }
  });
}
