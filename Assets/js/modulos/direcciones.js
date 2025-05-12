const btnAccion = document.querySelector("#btnAccion");
let tblDirecciones;
const modalDirecciones = new bootstrap.Modal(document.getElementById("modalDirecciones"));
const titleModal = document.querySelector("#titleModal");

document.addEventListener("DOMContentLoaded", function () {
  // Cargar la tabla de direcciones
  tblDirecciones = $("#tblDirecciones").DataTable({
    ajax: {
      url: base_url + "Direcciones/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id" },
      { data: "number" },
      { data: "street" },
      { data: "city" },
      { data: "state" },
      { data: "zip" },
      { data: "latitude" },
      { data: "longitude" }, 
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
    const number = document.querySelector("#number").value;
    const street = document.querySelector("#street").value;
    const city = document.querySelector("#city").value;
    const state = document.querySelector("#state").value;
    const zip = document.querySelector("#zip").value;
    const latitude = document.querySelector("#latitude").value;
    const longitude = document.querySelector("#longitude").value;

    if (number === "" || street===""|| city === "" || state === "" || zip === ""  || latitude === "" || longitude === "") {
      Swal.fire("Advertencia", "Todos los campos son obligatorios", "warning");
      return;
    }

    const url = base_url + "Direcciones/actualizar";
    const formData = new FormData();
    formData.append("id", id);
    formData.append("number", number);
    formData.append("street", street);
    formData.append("city", city);
    formData.append("state", state);
    formData.append("zip", zip);
    formData.append("latitude", latitude);
    formData.append("longitude", longitude);

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(formData);
    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        const res = JSON.parse(this.responseText);
        
        Swal.fire("Mensaje", res.msg, res.icono);
        if (res.icono === "success") {
          modalDirecciones.hide();
          tblDirecciones.ajax.reload();
        }
      }
    };
  });
});

// Función para cargar los datos en el modal y abrirlo
function editarDireccion(idPro) {
  document.querySelector("#id").value = "";
  titleModal.textContent = "Modificar Dirección";
  modalDirecciones.show();

  const url = base_url + "Direcciones/edit/" + idPro;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
      const res = JSON.parse(this.responseText);
      document.querySelector("#id").value = res.id;
      document.querySelector("#number").value = res.number;
      document.querySelector("#street").value = res.street;
      document.querySelector("#city").value = res.city;
      document.querySelector("#state").value = res.state;
      document.querySelector("#zip").value = res.zip;
      document.querySelector("#latitude").value = res.latitude;
      document.querySelector("#longitude").value = res.longitude;
      btnAccion.textContent = "Actualizar";
    }
  };
}
