document.addEventListener("DOMContentLoaded", function () {
  const tipoErrorSelect = document.getElementById("error");
  const campoOculto = document.getElementById("field_name");
  const imagenesInput = document.getElementById("imagenesInput");
  const inspectorSelectDiv = document.getElementById("inspectorSelectDiv");
  const inputPropuesto = document.getElementById("proposed_value");
  const inspectorSelect = document.getElementById("inspector_select");
  const proposedValueLabel = document.getElementById("proposed_value_label");
  const frm = document.getElementById("frmErroresUsuario");

  // Año actual para validación dinámica
  const hoy = new Date();
  const anioActual = hoy.getFullYear();

  // 🔠 Forzar MAYÚSCULAS cuando el input es de texto
  if (inputPropuesto) {
    inputPropuesto.addEventListener("input", function () {
      if (this.type === "text") {
        this.value = this.value.toUpperCase();
      }
    });
  }

  // Mostrar/ocultar campos según tipo de error
  tipoErrorSelect.addEventListener("change", function () {
    const campo = this.value;
    campoOculto.value = campo;

    // Ocultar todos los campos primero
    imagenesInput.style.display = "none";
    inspectorSelectDiv.style.display = "none";
    inputPropuesto.style.display = "none";

    // Limpiar y resetear configuración del input propuesto
    inputPropuesto.value = "";
    inputPropuesto.removeAttribute("min");
    inputPropuesto.removeAttribute("max");
    inputPropuesto.type = "text"; // por defecto texto

    const prevDireccionSelect = document.getElementById("direccion_select");
    if (prevDireccionSelect) {
      prevDireccionSelect.remove();
    }

    if (campo === "images") {
      imagenesInput.style.display = "block";
      proposedValueLabel.textContent = "Subir nuevas imágenes";

    } else if (campo === "inspector_id") {
      inspectorSelectDiv.style.display = "block";
      proposedValueLabel.textContent = "Seleccionar Inspector";

      // Cargar inspectores dinámicamente
      fetch(base_url + "ErroresUsuario/getInspectores")
        .then((res) => res.json())
        .then((data) => {
          inspectorSelect.innerHTML =
            '<option value="">-- Seleccione un inspector --</option>';
          data.forEach((inspector) => {
            const option = document.createElement("option");
            option.value = inspector.id;
            option.textContent = inspector.name;
            inspectorSelect.appendChild(option);
          });
        });

    } else if (campo === "address_id") {
      // Crear y mostrar nuevo select de direcciones
      proposedValueLabel.textContent = "Valor Propuesto";
      let selectDireccion = document.createElement("select");
      selectDireccion.className = "form-select mt-2";
      selectDireccion.name = "direccion_select";
      selectDireccion.id = "direccion_select";
      selectDireccion.innerHTML =
        '<option value="">-- Seleccione una dirección --</option>';

      // Insertar debajo del inputPropuesto
      inputPropuesto.parentNode.appendChild(selectDireccion);

      // Cargar direcciones dinámicamente
      fetch(base_url + "ErroresUsuario/getDirecciones")
        .then((res) => res.json())
        .then((data) => {
          data.forEach((dir) => {
            const option = document.createElement("option");
            option.value = dir.id;
            option.textContent = dir.direccion;
            selectDireccion.appendChild(option);
          });
        });

    } else {
      // Cualquier otro campo (vin, marca, modelo, propietario, etc.)
      proposedValueLabel.textContent = "Valor Propuesto";
      inputPropuesto.style.display = "block";

      // 📅 Si el campo a corregir es "year", aplicamos rango 1950 ~ añoActual+1
      if (campo === "year") {
        inputPropuesto.type = "number";
        inputPropuesto.min = 1950;
        inputPropuesto.max = anioActual + 1;
      }
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
  frm.addEventListener("submit", function (e) {
    e.preventDefault();

    const campo = campoOculto.value;

    // Validación para imágenes
    if (campo === "images") {
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

    // 📅 Validación extra si el campo es 'year'
    if (campo === "year") {
      const minYear = 1950;
      const maxYear = anioActual + 1;
      const valorYear = parseInt(inputPropuesto.value, 10);

      if (
        isNaN(valorYear) ||
        valorYear < minYear ||
        valorYear > maxYear
      ) {
        alertas(
          `El año del vehículo debe estar entre ${minYear} y ${maxYear}.`,
          "warning"
        );
        return;
      }
    }

    // Si es inspector, pasar valor al input
    if (campo === "inspector_id") {
      inputPropuesto.value = inspectorSelect.value;
    }

    // Si es dirección, pasar valor seleccionado
    if (campo === "address_id") {
      const direccionSeleccionada =
        document.getElementById("direccion_select").value;
      inputPropuesto.value = direccionSeleccionada;
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
          campoOculto.value = "";
          imagenesInput.style.display = "none";
          inspectorSelectDiv.style.display = "none";
          inputPropuesto.style.display = "block";
          const selectDir = document.getElementById("direccion_select");
          if (selectDir) selectDir.remove();

          document.getElementById("error").selectedIndex = 0;
          document.getElementById("cert_number").selectedIndex = 0;
          inspectorSelect.innerHTML =
            '<option value="">-- Seleccione un inspector --</option>';
        }
      }
    };
  });

  // SweetAlert helper
  function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
  }
});
