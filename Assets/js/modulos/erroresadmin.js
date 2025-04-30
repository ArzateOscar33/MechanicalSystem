let tblErrores;
const myModal = new bootstrap.Modal(document.getElementById("modalError"));
document.addEventListener("DOMContentLoaded", function () {
    //myModal.show();
  // Inicializar DataTable
  tblCertificados = $("#tblErrores").DataTable({
    ajax: {
      url: base_url + "ErroresAdmin/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "certificate_id" },
      { data: "user_id" },
      { data: "field_name" },
      { data: "current_value" }, 
      { data: "proposed_value" },
      { data: "reason" }, 
      { data: "created_at" },
      { data: "accion" }
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
    tblCertificados.column(6).search(this.value).draw();
  });

  $("#filterEstado").on("change", function () {
    tblCertificados.column(7).search(this.value).draw();
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

function editCertificate(certificate_id) {
  console.log(certificate_id);
  const url = base_url + "ErroresAdmin/editCertificate/" + certificate_id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
         /* document.querySelector('#id').value = res.id;
          document.querySelector('#username').value = res.username;
          document.querySelector('#nombre').value = res.first_name;
          document.querySelector('#apellido').value = res.last_name;
          document.querySelector('#correo').value = res.correo;
          document.querySelector('#phone').value = res.phone;
          document.querySelector('#rol').value = res.role_id;  
          document.querySelector('#clave').setAttribute('readonly', 'readonly');*/
          btnAccion.textContent = 'Actualizar';
          titleModal.textContent = "MODIFICAR CERTIFICADO";
          myModal.show();
      }
  }
}

