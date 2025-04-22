const btnAccion = document.querySelector("#btnAccion");
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
      { data: "zip_file_path" },
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

        // Filtros por columnas (excepto zip_file_path y accion)
        $("#filterCertNumber").on("keyup change", function () {
            tblCertificados.column(0).search(this.value).draw();
        });

        $("#filterVin").on("keyup change", function () {
            tblCertificados.column(1).search(this.value).draw();
        });

        $("#filterOwner").on("keyup change", function () {
            tblCertificados.column(2).search(this.value).draw();
        });

        $("#filterInspector").on("keyup change", function () {
            tblCertificados.column(3).search(this.value).draw();
        });

        $("#filterMake").on("keyup change", function () {
            tblCertificados.column(4).search(this.value).draw();
        });

        $("#filterCiudad").on("change", function () {
            tblCertificados.column(6).search(this.value).draw();
        });

        $("#filterEstado").on("change", function () {
            tblCertificados.column(7).search(this.value).draw();
        });

        $("#filterZip").on("keyup change", function () {
            tblCertificados.column(8).search(this.value).draw();
        });

        $("#filterMfgIn").on("keyup change", function () {
            tblCertificados.column(9).search(this.value).draw();
        });
  // Llenar dinámicamente filtros de ciudad y estado
  fetch(base_url + "buscarCertificados/obtenerFiltros")
    .then((res) => res.json())
    .then((data) => {
      const ciudadSelect = document.querySelector("#filterCiudad");
      const estadoSelect = document.querySelector("#filterEstado");

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
    });
});

// Función para eliminar certificado
/*
function eliminarCertificado(cert_number) {
  Swal.fire({
    title: "Aviso?",
    text: "Esta seguro de eliminar el registro!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Eliminar!",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "buscarCertificados/delete/" + cert_number;
      const http = new XMLHttpRequest();
      http.open("GET", url, true);
      http.send();
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          if (res.icono == "success") {
            tblCertificados.ajax.reload();
          }
          Swal.fire("Aviso?", res.msg.toUpperCase(), res.icono);
        }
      };
    }
  });
}*/
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
      console.log("Verificando existencia del archivo en:", verificarUrl);

      const verificarHttp = new XMLHttpRequest();
      verificarHttp.open("GET", verificarUrl, true);
      verificarHttp.send();
      verificarHttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          try {
            const data = JSON.parse(this.responseText);
            console.log("Respuesta de verificación:", data);

            if (data.existe) {
              const descargarUrl = base_url + "buscarCertificados/descargar/" + cert_number;
              console.log("Iniciando descarga desde:", descargarUrl);

              const descargarHttp = new XMLHttpRequest();
              descargarHttp.open("GET", descargarUrl, true);
              descargarHttp.send();
              descargarHttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                  try {
                    const res = JSON.parse(this.responseText);
                    console.log("Respuesta de descarga:", res);

                    if (res.icono === "success" && res.url) {
                      console.log("URL generada por el backend:", res.url);
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



