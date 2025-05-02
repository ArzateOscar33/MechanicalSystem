let tblErrores;
let currentFieldName = '';  
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
      { data: "certificate_id" },
      { data: "user_name" },
      { data: "field_name" },
      { data: "current_value" }, 
      { data: "proposed_value" },
      { data: "reason" },
      { data: "status" },
      { data: "created_at" },
      { data: "accion" }
    ],
    language,
    dom,
    buttons,
  });

  tblCertificados = $("#tblErroresResueltos").DataTable({
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

  
});
document.getElementById("frmEditar").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const certNumber = formData.get("cert_number");
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
    cancelButtonText: "Cancelar"
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(base_url + "ErroresAdmin/corregirCertificado", {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(res => {
        Swal.fire(res.msg, '', res.icono);
        tblCertificados.ajax.reload();
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

      // Llenar datos básicos
      document.querySelector('#id').value = res.id;
      document.querySelector('#cert_number').value = res.cert_number;
      document.querySelector('#field_name').value = res.field_name;
      currentFieldName = res.field_name;
      currentProposedValue = res.proposed_value;
      console.log("Campo editable:", res.field_name, "Valor propuesto:", res.proposed_value);
      // Llenar todos los campos con sus valores
      document.querySelector('#vin').value = res.vin;
      document.querySelector('#make').value = res.make;
      document.querySelector('#model').value = res.model;
      document.querySelector('#owner_name').value = res.owner_name;
      document.querySelector('#mfg_in').value = res.mfg_in;
      document.querySelector('#city').value = res.city;
      document.querySelector('#state').value = res.state;
      document.querySelector('#zip').value = res.zip;
      document.querySelector('#inspector').value = res.inspector_name;
      document.querySelector('#year').value = res.year; 
      document.querySelector('#license_plate').value = res.license_plate; 
      document.querySelector('#street').value = res.street;
      document.querySelector('#proposed_value').value = res.proposed_value;

      // Ocultar todos los grupos
      document.querySelectorAll('.editable-field').forEach(group => {
        group.classList.add('d-none');
      });

      // Mostrar solo el grupo editable
      const targetGroup = document.querySelector('#group_' + currentFieldName);
      const proposedGroup = document.querySelector('#group_proposed_value');
      if (targetGroup) {
        targetGroup.classList.remove('d-none');
        proposedGroup.classList.remove('d-none');
      } else {
        console.warn("No se encontró el grupo para el campo:", currentFieldName);
      }

      // Mostrar modal
      btnAccion.textContent = 'Actualizar';
      titleModal.textContent = "CORREGIR CAMPO DEL CERTIFICADO";
      myModal.show();
    }
  };
}

