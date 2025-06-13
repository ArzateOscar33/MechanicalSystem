let tblErrores;
let currentFieldName = "";
let currentProposedValue = "";
const myModal = new bootstrap.Modal(document.getElementById("modalError"));
const btnAccion = document.getElementById("btnAccion");
const titleModal = document.getElementById("titleModal");

document.addEventListener("DOMContentLoaded", function () {
  const nombresCampos = {
    vin: "Número de Serie",
    make: "Marca",
    model: "Modelo",
    year: "Año",
    owner_name: "Propietario",
    address_id: "Dirección",
    inspector_id: "Inspector",
    ebitn: "EBITN",
    latitud: "Latitud",
    longitud: "Longitud",
    test_date: "Fecha de Prueba",
    license_plate: "Placa",
    // Agrega aquí más equivalencias si lo necesitas
  };

  tblErrores = $("#tblErrores").DataTable({
    ajax: {
      url: base_url + "ErroresAdmin/listar",
      dataSrc: "",
    },
    columns: [
      { data: "certificate_id", title: "ID Certificado" },
      { data: "user_name", title: "Usuario" },
      {
        data: "field_name",
        title: "Campo con Error",
        render: function (data, type, row) {
          return nombresCampos[data] || data;
        },
      },
      { data: "current_value", title: "Valor Actual" },
      { data: "proposed_value", title: "Valor Propuesto" },
      { data: "reason", title: "Motivo" },
      { data: "status", title: "Estado" },
      { data: "created_at", title: "Fecha de Creación" },
      { data: "accion", title: "Acciones" },
    ],
    language,
    dom,
    buttons,
  });
});

document
  .getElementById("frmEditar")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const field_name = document.getElementById("field_name").value;
    const certNumber = document.getElementById("cert_number").value;
    const formData = new FormData(this);

    if (field_name === "images") {
      const usarSugeridas =
        document.getElementById("btnUsarSugeridas").dataset.usar === "1";

      Swal.fire({
        title: "¿Reemplazar imágenes?",
        text: usarSugeridas
          ? "¿Deseas reemplazar con las imágenes sugeridas?"
          : "¿Deseas reemplazar con las nuevas imágenes seleccionadas?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, reemplazar",
        cancelButtonText: "Cancelar",
      }).then(async (result) => {
        if (result.isConfirmed) {
          if (usarSugeridas) {
            const rutas = await fetch(
              base_url + "ErroresAdmin/verImagenesTemporales/" + certNumber
            ).then((res) => res.json());

            for (let i = 0; i < rutas.length; i++) {
              const respuesta = await fetch(rutas[i]);
              const blob = await respuesta.blob();
              const nombre = rutas[i].split("/").pop();
              formData.append("imagenes[]", blob, nombre);
            }
          }

          fetch(base_url + "ErroresAdmin/corregirCertificado", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((res) => {
              Swal.fire(res.msg, "", res.icono);
              tblErrores.ajax.reload();
              myModal.hide();
            });
        }
      });

      return; // importante: evita envío doble
    }

    const field = formData.get("field_name");
    const newValue = formData.get(field);

    // Diccionario de nombres legibles
    const nombresCampos = {
      vin: "Número de Serie",
      make: "Marca",
      model: "Modelo",
      year: "Año",
      owner_name: "Propietario",
      address_id: "Dirección",
      inspector_id: "Inspector",
      ebitn: "EBITN",
      latitud: "Latitud",
      longitud: "Longitud",
      test_date: "Fecha de Prueba",
       license_plate: "Placa",
    };

    // Buscar nombre legible o mostrar el original si no está en el diccionario
    const nombreLegible = nombresCampos[field] || field;

    Swal.fire({
      title: "¿Confirmar corrección?",
      html: `
    <strong>Certificado:</strong> ${certNumber}<br>
    <strong>Campo a corregir:</strong> ${nombreLegible}<br>
    <strong>Nuevo valor:</strong> ${newValue}
  `,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, corregir",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        fetch(base_url + "ErroresAdmin/corregirCertificado", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((res) => {
            Swal.fire(res.msg, "", res.icono);
            tblErrores.ajax.reload();
            myModal.hide();
          });
      }
    });
  });

function editCertificate(certificate_id) {
  const url = base_url + "ErroresAdmin/editCertificate/" + certificate_id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      document.querySelector("#id").value = res.id;
      document.querySelector("#cert_number").value = res.cert_number;
      document.querySelector("#field_name").value = res.field_name;
      currentFieldName = res.field_name;
      currentProposedValue = res.proposed_value;

      if (currentFieldName === "address_id") {
        document.querySelector("#group_proposed_value label").textContent =
          "Dirección actual";
      } else if (currentFieldName === "inspector_id") {
        document.querySelector("#group_proposed_value label").textContent =
          "Inspector actual";
      } else {
        document.querySelector("#group_proposed_value label").textContent =
          "Valor Propuesto";
      }

      // Llenar campos del certificado
      document.querySelector("#vin").value = res.vin;
      document.querySelector("#make").value = res.make;
      document.querySelector("#model").value = res.model;
      document.querySelector("#owner_name").value = res.owner_name;
      document.querySelector("#mfg_in").value = res.mfg_in;
      document.querySelector("#city").value = res.city;
      document.querySelector("#state").value = res.state;
      document.querySelector("#zip").value = res.zip;
      document.querySelector("#street").value = res.street;
      document.querySelector("#year").value = res.year;
      document.querySelector("#license_plate").value = res.license_plate;
      document.querySelector("#inspector").value = res.inspector_name;

      const direccionActual = `${res.number} ${res.street}, ${res.city}, ${res.state} ${res.zip}`;

      // Ocultar todos los grupos
      document.querySelectorAll(".editable-field").forEach((group) => {
        group.classList.add("d-none");
      });

      // Casos especiales
      if (currentFieldName === "images") {
        document.querySelector("#group_images").classList.remove("d-none");
        document.querySelector("#group_proposed_value").classList.add("d-none");

        const contenedor = document.getElementById("imagenes_sugeridas");
        contenedor.innerHTML = "";

        fetch(
          base_url + "ErroresAdmin/verImagenesTemporales/" + res.cert_number
        )
          .then((res) => res.json())
          .then((data) => {
            if (Array.isArray(data) && data.length > 0) {
              data.forEach((url) => {
                const img = document.createElement("img");
                img.src = url;
                img.classList.add("img-thumbnail");
                img.style.maxWidth = "120px";
                img.style.margin = "5px";
                contenedor.appendChild(img);
              });
              document.getElementById("group_images_preview").style.display =
                "block";
              document.getElementById("btnUsarSugeridas").dataset.usar = "1";
            } else {
              document.getElementById("group_images_preview").style.display =
                "none";
              document.getElementById("btnUsarSugeridas").dataset.usar = "0";
            }
          });

        document
          .getElementById("btnUsarSugeridas")
          .addEventListener("click", function () {
            Swal.fire(
              "Imágenes sugeridas activadas",
              "Se usarán las imágenes temporales.",
              "info"
            );
            this.dataset.usar = "1";
          });
      } else if (currentFieldName === "address_id") {
        document.querySelector("#group_address_id").classList.remove("d-none");
        document
          .querySelector("#group_proposed_value")
          .classList.remove("d-none");

        document.querySelector("#proposed_value").value = direccionActual;

        fetch(base_url + "ErroresUsuario/getDirecciones")
          .then((res) => res.json())
          .then((data) => {
            const select = document.getElementById("direccion_admin");
            select.innerHTML =
              '<option value="">-- Seleccione una dirección --</option>';
            data.forEach((dir) => {
              const option = document.createElement("option");
              option.value = dir.id;
              option.textContent = dir.direccion;
              select.appendChild(option);
            });

            if (currentProposedValue) {
              select.value = currentProposedValue;
            }
          });
      } else if (currentFieldName === "inspector_id") {
        document
          .querySelector("#group_inspector_id")
          .classList.remove("d-none");
        document
          .querySelector("#group_proposed_value")
          .classList.remove("d-none");

        document.querySelector("#proposed_value").value = res.inspector_name;

        fetch(base_url + "ErroresUsuario/getInspectores")
          .then((res) => res.json())
          .then((data) => {
            const select = document.getElementById("inspector_select_admin");
            select.innerHTML =
              '<option value="">-- Seleccione un inspector --</option>';
            data.forEach((inspector) => {
              const option = document.createElement("option");
              option.value = inspector.id;
              option.textContent = inspector.name;
              select.appendChild(option);
            });

            if (currentProposedValue) {
              select.value = currentProposedValue;
            }
          });
      } else {
        // Campos normales (vin, make, owner_name, etc.)
        const grupoCampo = document.querySelector("#group_" + currentFieldName);
        if (grupoCampo) {
          grupoCampo.classList.remove("d-none");
        }

        document
          .querySelector("#group_proposed_value")
          .classList.remove("d-none");

        const inputEditable = document.getElementById(currentFieldName);
        if (inputEditable) {
          inputEditable.value = res.current_value || "";
        }

        const inputPropuesto = document.getElementById("proposed_value");
        inputPropuesto.value = res.proposed_value || "";
        inputPropuesto.readOnly = true;
      }

      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "CORREGIR CAMPO DEL CERTIFICADO";
      myModal.show();
    }
  };
  // Limpiar todos los campos del modal al cerrarlo
  document
    .getElementById("modalError")
    .addEventListener("hidden.bs.modal", function () {
      // Ocultar todos los campos editables
      document.querySelectorAll(".editable-field").forEach((group) => {
        group.classList.add("d-none");
        const input = group.querySelector("input, select");
        if (input) {
          input.value = "";
        }
      });

      // Limpiar imágenes sugeridas
      const contenedorImagenes = document.getElementById("imagenes_sugeridas");
      if (contenedorImagenes) contenedorImagenes.innerHTML = "";

      // Ocultar preview de imágenes sugeridas
      const preview = document.getElementById("group_images_preview");
      if (preview) preview.style.display = "none";

      // Reiniciar botón de sugeridas
      const btnUsar = document.getElementById("btnUsarSugeridas");
      if (btnUsar) btnUsar.dataset.usar = "0";

      // Reset de valores auxiliares
      currentFieldName = "";
      currentProposedValue = "";

      // Restaurar etiqueta por defecto
      const labelPropuesta = document.querySelector(
        "#group_proposed_value label"
      );
      if (labelPropuesta) labelPropuesta.textContent = "Valor Sugerido";
    });
}

function errorDelete(id) {
  const url = base_url + "ErroresAdmin/delete/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const res = JSON.parse(this.responseText);

      if (res.icono === "success") {
        Swal.fire(res.msg, "", "success");
        tblErrores.ajax.reload();
      } else {
        Swal.fire(res.msg, "", "error");
      }
    }
  };
}
