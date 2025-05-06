let tblErrores;
let currentFieldName = "";
let currentProposedValue = "";
const myModal = new bootstrap.Modal(document.getElementById("modalError"));
const btnAccion = document.getElementById("btnAccion");
const titleModal = document.getElementById("titleModal");

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar tablas
  tblErrores = $("#tblErrores").DataTable({
    ajax: {
      url: base_url + "ErroresAdmin/listar",
      dataSrc: "",
    },
    columns: [
      { data: "certificate_id" },
      { data: "user_name" },
      { data: "field_name" },
      { data: "current_value" },
      { data: "proposed_value" },
      { data: "reason" },
      { data: "status" },
      { data: "created_at" },
      { data: "accion" },
    ],
    language,
    dom,
    buttons,
  });

  $("#tblErroresResueltos").DataTable({
    ajax: {
      url: base_url + "ErroresAdmin/listarResueltos",
      dataSrc: "",
    },
    columns: [
      { data: "certificate_id" },
      { data: "user_name" },
      { data: "field_name" },
      { data: "corregido" },
      { data: "status" },
      { data: "reviewed_at" },
      { data: "updated_at" },
    ],
    language,
    dom,
    buttons,
  });

  $("#tblErroresRejected").DataTable({
    ajax: {
      url: base_url + "ErroresAdmin/listarRechazados",
      dataSrc: "",
    },
    columns: [
      { data: "certificate_id" },
      { data: "user_name" },
      { data: "field_name" },
      { data: "corregido" },
      { data: "status" },
      { data: "reviewed_at" },
      { data: "updated_at" },
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

    Swal.fire({
      title: "¿Confirmar corrección?",
      html: `
      <strong>Certificado:</strong> ${certNumber}<br>
      <strong>Campo a corregir:</strong> ${field}<br>
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
      } else {
        document.querySelector("#group_proposed_value label").textContent =
          "Valor Sugerido";
      }

      // Llenar campos
      document.querySelector("#vin").value = res.vin;
      document.querySelector("#make").value = res.make;
      document.querySelector("#model").value = res.model;
      document.querySelector("#owner_name").value = res.owner_name;
      document.querySelector("#mfg_in").value = res.mfg_in;
      document.querySelector("#city").value = res.city;
      document.querySelector("#state").value = res.state;
      document.querySelector("#zip").value = res.zip;
      const direccionActual = `${res.number} ${res.street}, ${res.city}, ${res.state} ${res.zip}`;
      document.querySelector("#inspector").value = res.inspector_name;
      document.querySelector("#year").value = res.year;
      document.querySelector("#license_plate").value = res.license_plate;
      document.querySelector("#street").value = res.street;
      document.querySelector("#proposed_value").value = res.proposed_value;
      if (currentFieldName === "address_id") {
        document.querySelector("#proposed_value").value = direccionActual;
      }
      if (currentFieldName === "inspector_id") {
        // Mostrar inspector actual legible en campo readonly
        document.querySelector("#proposed_value").value = res.inspector_name;
      }

      // Ocultar todos
      document.querySelectorAll(".editable-field").forEach((group) => {
        group.classList.add("d-none");
      });

      // Mostrar grupo correspondiente
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

        // Botón para activar uso de sugeridas
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
        const grupo = document.querySelector("#group_address_id");
        grupo.classList.remove("d-none");
        // Mostrar dirección actual (readonly)
        document
          .querySelector("#group_proposed_value")
          .classList.remove("d-none");
        // Cargar direcciones disponibles
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

            // Selecciona el valor propuesto si existe
            if (currentProposedValue) {
              select.value = currentProposedValue;
            }
          });

        document.getElementById("group_images_preview").style.display = "none";
      } else if (currentFieldName === "inspector_id") {
        const grupo = document.querySelector("#group_inspector_id");
        grupo.classList.remove("d-none");

        // Mostrar valor actual (readonly)
        document
          .querySelector("#group_proposed_value")
          .classList.remove("d-none");

        // Cambiar etiqueta del label
        document.querySelector("#group_proposed_value label").textContent =
          "Inspector actual";

        // Llenar el select con inspectores
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
      }

      btnAccion.textContent = "Actualizar";
      titleModal.textContent = "CORREGIR CAMPO DEL CERTIFICADO";
      myModal.show();
    }
  };
}

function errorDelete(id) {
  const url = base_url + "ErroresAdmin/delete/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
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
