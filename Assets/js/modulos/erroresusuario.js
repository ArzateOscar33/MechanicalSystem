document.addEventListener("DOMContentLoaded", function () {
  const tipoErrorSelect = document.getElementById("error");
  const campoOculto = document.getElementById("field_name");
  const imagenesInput = document.getElementById("imagenesInput");
  const inspectorSelectDiv = document.getElementById("inspectorSelectDiv");
  const inputPropuesto = document.getElementById("proposed_value");
  const inspectorSelect = document.getElementById("inspector_select");

  // Mostrar/ocultar campos según tipo de error
  tipoErrorSelect.addEventListener("change", function () {
    const campo = this.value;
    campoOculto.value = campo;

    if (campo === 'images') {
      imagenesInput.style.display = "block";
      inspectorSelectDiv.style.display = "none";
      inputPropuesto.style.display = "none";
    } else if (campo === 'inspector_id') {
      imagenesInput.style.display = "none";
      inspectorSelectDiv.style.display = "block";
      inputPropuesto.style.display = "none";

      // Cargar inspectores dinámicamente
      fetch(base_url + "ErroresUsuario/getInspectores")
        .then((res) => res.json())
        .then((data) => {
          inspectorSelect.innerHTML = '<option value="">-- Seleccione un inspector --</option>';
          data.forEach((inspector) => {
            const option = document.createElement("option");
            option.value = inspector.id;
            option.textContent = inspector.name;
            inspectorSelect.appendChild(option);
          });
        });
    } else {
      imagenesInput.style.display = "none";
      inspectorSelectDiv.style.display = "none";
      inputPropuesto.style.display = "block";
    }
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
      const select = document.getElementById("error");
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

    const campo = campoOculto.value;

    // Validación para imágenes
    if (campo === 'images') {
      const archivos = document.getElementById("imagenes").files;
      if (!archivos.length) {
        alertas("Debe subir al menos una imagen", "warning");
        return;
      }

      if (archivos.length > 9) {
        alertas("Máximo 9 imágenes permitidas", "warning");
        return;
      }
    }

    // Si es inspector, pasar valor al input
    if (campo === 'inspector_id') {
      inputPropuesto.value = inspectorSelect.value;
    }

    const data = new FormData(frm);
    const url = base_url + "ErroresUsuario/crear";
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);

    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        alertas(res.msg, res.icono);
        if (res.icono === "success") {
          frm.reset();
          campoOculto.value = '';
          imagenesInput.style.display = "none";
          inspectorSelectDiv.style.display = "none";
          inputPropuesto.style.display = "block";

          document.getElementById("error").selectedIndex = 0;
          document.getElementById("cert_number").selectedIndex = 0;
          inspectorSelect.innerHTML = '<option value="">-- Seleccione un inspector --</option>';
        }
      }
    };
  });

  // SweetAlert helper
  function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
  }
});
