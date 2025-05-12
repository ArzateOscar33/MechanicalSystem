document.addEventListener("DOMContentLoaded", function () {
  const frm = document.querySelector("#formularioCertificado");
  const generarQRBtn = document.querySelector("#generarQR");
  const qrCodeContainer = document.querySelector("#codigoQR");
  const selectDireccion = document.getElementById("direccion_existente");
  const camposNuevaDireccion = document.getElementById("nuevaDireccionCampos");
  const telefonoInput = document.querySelector('input[name="telefono"]');
  const fechaInput = document.getElementById("fecha");
  const expiracionInput = document.getElementById("fecha_expiracion");
  const firmaPreview = document.getElementById("firmaPreview");
  const imagenFirma = document.getElementById("imagenFirma");
  const inspectorSelect = document.getElementById("inspector");
  const nuevoInspectorCheck = document.getElementById("nuevo_inspector_check");
  const nuevoInspectorInput = document.getElementById("nuevo_inspector");
  const requiredInputs = document.querySelectorAll(
    "#formularioCertificado input[required], #formularioCertificado select[required]"
  );

  // Establecer fecha mÃnima hoy
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const fechaMinima = `${yyyy}-${mm}-${dd}`;
  fechaInput.setAttribute("min", fechaMinima);
  expiracionInput.readOnly = true;

  if (inspectorSelect) {
    inspectorSelect.addEventListener("change", function () {
      const id = this.value;
      if (!id) {
        firmaPreview.style.display = "none";
        imagenFirma.src = "";
        return;
      }
      fetch(base_url + "CrearCertificados/obtenerFirmaInspector/" + id)
        .then(res => res.json())
        .then(data => {
          if (data.firma) {
            imagenFirma.src = data.firma;
            firmaPreview.style.display = "block";
          } else {
            firmaPreview.style.display = "none";
            imagenFirma.src = "";
          }
        });
    });
  }

  fechaInput.addEventListener("change", function () {
    const valor = fechaInput.value;
    if (valor < fechaMinima) {
      alertas("La fecha no puede ser anterior a hoy.", "warning");
      fechaInput.value = "";
      expiracionInput.value = "";
      return;
    }
    const fecha = new Date(valor);
    fecha.setFullYear(fecha.getFullYear() + 1);
    const yyyy = fecha.getFullYear();
    const mm = String(fecha.getMonth() + 1).padStart(2, "0");
    const dd = String(fecha.getDate()).padStart(2, "0");
    expiracionInput.value = `${yyyy}-${mm}-${dd}`;
  });

  function actualizarVisibilidadDireccion() {
    const usandoDireccionExistente = selectDireccion && selectDireccion.value !== "";
    if (usandoDireccionExistente) {
      camposNuevaDireccion.style.display = "none";
      telefonoInput.required = false;
    } else {
      camposNuevaDireccion.style.display = "block";
      telefonoInput.required = true;
    }
    verificarCamposCompletos();
  }

  if (selectDireccion) {
    selectDireccion.addEventListener("change", function () {
      actualizarVisibilidadDireccion();
      cargarLatLonDireccion(selectDireccion.value);
    });
    actualizarVisibilidadDireccion();
    cargarLatLonDireccion(selectDireccion.value);
  }

  function verificarCamposCompletos() {
    const usandoDireccionExistente = selectDireccion && selectDireccion.value !== "";
    const incompletos = Array.from(requiredInputs).some((input) => {
      if (usandoDireccionExistente && camposNuevaDireccion.contains(input)) {
        return false;
      }
      return input.type !== "file" && !input.value.trim();
    });
    generarQRBtn.disabled = incompletos;
  }

  requiredInputs.forEach((input) => {
    input.addEventListener("input", verificarCamposCompletos);
    input.addEventListener("change", verificarCamposCompletos);
  });

  verificarCamposCompletos();

  generarQRBtn.addEventListener("click", function () {
    const vin = document.querySelector("#vin").value.trim();
    const propietario = document.querySelector("#propietario").value.trim();
    const fabricadoEn = document.querySelector("#fabricado_en").value.trim();
    const year = document.querySelector("#year").value.trim();
    const modelo = document.querySelector("#modelo").value.trim();
    const marca = document.querySelector("#marca").value.trim();

    if (!vin || !propietario || !fabricadoEn || !year || !modelo || !marca) {
      alertas("Faltan campos para generar el cÃģdigo QR", "warning");
      return;
    }

    const contenidoQR = `${vin}|${propietario}|${fabricadoEn}|${year}|${modelo}|${marca}`;
    qrCodeContainer.innerHTML = "";

    new QRCode(qrCodeContainer, {
      text: contenidoQR,
      width: 256,
      height: 256,
    });
  });

  frm.addEventListener("submit", function (e) {
    e.preventDefault();

    const valorFecha = fechaInput.value;
    const valorExp = expiracionInput.value;

    if (!valorFecha) {
      alertas("La fecha del certificado es obligatoria.", "warning");
      return;
    }

    if (valorFecha < fechaMinima) {
      alertas("La fecha del certificado no puede ser anterior a hoy.", "warning");
      return;
    }

    if (valorExp <= valorFecha) {
      alertas("La fecha de expiraciÃģn debe ser posterior a la del certificado.", "warning");
      return;
    }

    if (nuevoInspectorCheck && nuevoInspectorCheck.checked) {
      inspectorSelect.value = nuevoInspectorInput.value;
    }

    const data = new FormData(frm);
    const url = base_url + "CrearCertificados/crear";

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);

    http.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        const res = JSON.parse(this.responseText);
        alertas(res.msg, res.icono);
        if (res.icono === "success") {
          frm.reset();
          qrCodeContainer.innerHTML = "";
          imagenFirma.src = "";
          firmaPreview.style.display = "none";
          verificarCamposCompletos();
          window.open(res.pdf_url, "_blank");
          window.location.href = res.zip_url;
        }
      }
    };
  });
});

function cargarLatLonDireccion(id) {
  const url = base_url + "CrearCertificados/obtenerLatLon/" + id;
  const latInput = document.getElementById("latitud");
  const lonInput = document.getElementById("longitud");

  if (id) {
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        latInput.value = data.latitude || "";
        lonInput.value = data.longitude || "";
        latInput.parentElement.parentElement.style.display = "none";
        latInput.required = false;
        lonInput.required = false;
      })
      .catch((error) => {
        console.error("Error al obtener lat/lon:", error);
        latInput.value = "";
        lonInput.value = "";
        latInput.parentElement.parentElement.style.display = "block";
        latInput.required = true;
        lonInput.required = true;
      });
  } else {
    latInput.value = "";
    lonInput.value = "";
    latInput.parentElement.parentElement.style.display = "block";
    latInput.required = true;
    lonInput.required = true;
  }
}

function alertas(msg, icono) {
  Swal.fire("Aviso", msg.toUpperCase(), icono);
}