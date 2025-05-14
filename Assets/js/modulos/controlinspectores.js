const btnAccion = document.querySelector("#btnAccion");
let tblInspectores;
const modalDirecciones = new bootstrap.Modal(document.getElementById("modalInspectores"));
const titleModal = document.querySelector("#titleModal");

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar DataTable
  tblInspectores = $("#tblInspectores").DataTable({
    ajax: {
      url: base_url + "ControlInspectores/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "name" },
      { data: "firma" },
      { data: "accion" },
    ],
    language,
    dom,
    buttons,
  });

  // Enviar formulario de actualización
  btnAccion.addEventListener("click", function (e) {
    e.preventDefault();

    const id = document.querySelector("#id").value;
    const name = document.querySelector("#name").value;
    const firma_actual = document.querySelector("#firma_actual").value;
    const firma = document.querySelector("#firma").files[0];

    if (id === "" || name === "") {
      Swal.fire("Advertencia", "Todos los campos son obligatorios", "warning");
      return;
    }

    const url = base_url + "ControlInspectores/actualizar";
    const formData = new FormData();
    formData.append("id", id);
    formData.append("name", name);
    formData.append("firma_actual", firma_actual);
    if (firma) {
      formData.append("firma", firma);
    }

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        try {
          const res = JSON.parse(this.responseText);
          Swal.fire("Mensaje", res.msg, res.icono);
          if (res.icono === "success") {
            modalDirecciones.hide();
            tblInspectores.ajax.reload();
          }
        } catch (error) {
          console.error("Error al parsear la respuesta:", this.responseText);
          Swal.fire("Error", "Hubo un problema en el servidor", "error");
        }
      }
    };
  });
});
document.getElementById("nuevo_registro").addEventListener("click", function () {
    const nombre = document.getElementById("nombreNuevoInspector").value;
    const firma = document.getElementById("firmaNuevoInspector").files[0];

    const formData = new FormData();
    formData.append("nombreNuevoInspector", nombre);
    formData.append("firmaNuevoInspector", firma);

    fetch(base_url + "ControlInspectores/registrar", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        Swal.fire({
            icon: data.icono,
            title: data.msg
        });
        if (data.icono === "success") {
            document.getElementById("nombreNuevoInspector").value = "";
            document.getElementById("firmaNuevoInspector").value = "";
            tblInspectores.ajax.reload();
        }
    });
});

// Función para cargar datos en el modal y mostrarlo
function editarInspector(idPro) {
  document.querySelector("#id").value = "";
  document.querySelector("#name").value = "";
  document.querySelector("#firma").value = "";
  document.querySelector("#firma_actual").value = "";
  document.querySelector("#imgFirma").src = "";
  titleModal.textContent = "Modificar Inspector";
  modalDirecciones.show();

  const url = base_url + "ControlInspectores/editar/" + idPro;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      try {
        const res = JSON.parse(this.responseText);
        document.querySelector("#id").value = res.id;
        document.querySelector("#name").value = res.name;
        document.querySelector("#firma_actual").value = res.firma || '';
        document.querySelector("#imgFirma").src = base_url + res.firma;
        btnAccion.textContent = "Actualizar";
      } catch (error) {
        console.error("Error al cargar inspector:", this.responseText);
      }
    }
  };
}
