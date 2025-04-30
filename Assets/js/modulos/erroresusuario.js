document.addEventListener("DOMContentLoaded", function () {
  const tipoErrorSelect = document.getElementById("error");
  const campoOculto = document.getElementById("field_name");

  tipoErrorSelect.addEventListener("change", function () {
    campoOculto.value = this.value;
  });
  // Cargar certificados del día
  fetch(base_url + "ErroresUsuario/getCertificados")
    .then((res) => res.json())
    .then((data) => {
      const select = document.getElementById("cert_number");
      data.forEach((cert) => {
        const option = document.createElement("option");
        option.value = cert.cert_number;
        option.textContent = cert.cert_number;
        select.appendChild(option);
      });
    });

  // Cargar campos corregibles
  fetch(base_url + "ErroresUsuario/getCamposPermitidos")
    .then((res) => res.json())
    .then((data) => {
      const select = document.getElementById("field_name");
      data.forEach((campo) => {
        const option = document.createElement("option");
        option.value = campo.field_name;
        option.textContent = campo.label;
        select.appendChild(option);
      });
    });

  // Envío del formulario
  const frm = document.getElementById("frmErroresUsuario");
  frm.addEventListener("submit", function (e) {
    e.preventDefault();
    const data = new FormData(frm);
    const url = base_url + "ErroresUsuario/crear";

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);

    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        console.log(this.responseText);
        const res = JSON.parse(this.responseText);
        alertas(res.msg, res.icono);
        if (res.icono === "success") {
          frm.reset();
        }
      }
    };
  });
});
  // Función para mostrar alertas con SweetAlert
  function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
  }