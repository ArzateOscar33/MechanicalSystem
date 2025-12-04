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
  const yearInput = document.getElementById("year");
  const numeroCertInput = document.getElementById("numero_certificado");

  // Establecer fecha mínima hoy
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const fechaMinima = `${yyyy}-${mm}-${dd}`;
  fechaInput.setAttribute("min", fechaMinima);
  expiracionInput.readOnly = true;

  // Flag para evitar envíos múltiples
  let enviando = false;
  // Botón submit (ajusta si tienes un id específico)
  const btnSubmit = frm.querySelector('button[type="submit"], input[type="submit"]');
  const originalBtnText =
    btnSubmit && btnSubmit.tagName === "BUTTON" ? btnSubmit.textContent : null;

  if (yearInput) {
    const minYear = 1950;
    const maxYear = yyyy + 1; // año actual + 1

    yearInput.setAttribute("min", minYear);
    yearInput.setAttribute("max", maxYear);

    yearInput.addEventListener("change", function () {
      const valor = parseInt(yearInput.value, 10);
      if (isNaN(valor) || valor < minYear || valor > maxYear) {
        alertas(
          `El año del vehículo debe estar entre ${minYear} y ${maxYear}.`,
          "warning"
        );
        yearInput.value = "";
      }
    });
  }

  if (inspectorSelect) {
    inspectorSelect.addEventListener("change", function () {
      const id = this.value;
      if (!id) {
        firmaPreview.style.display = "none";
        imagenFirma.src = "";
        return;
      }
      fetch(base_url + "CrearCertificados/obtenerFirmaInspector/" + id)
        .then((res) => res.json())
        .then((data) => {
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

    // Ahora sumamos 3 meses en lugar de 1 año
    fecha.setMonth(fecha.getMonth() + 3);

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

  // CAMPOS QUE QUEREMOS SIEMPRE EN MAYÚSCULAS
  const camposMayusculasIds = [
    "numero",
    "calle",
    "ciudad",
    "estado",
    "zip",
    "vin",
    "marca",
    "modelo",
    "fabricado_en",
    "placa",
    "propietario",
    "ebitn",
  ];

  camposMayusculasIds.forEach((id) => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("input", function () {
        this.value = this.value.toUpperCase();
      });
    }
  });

  function actualizarNumeroCertificadoLocal() {
    if (!numeroCertInput || !numeroCertInput.value) {
      return;
    }

    const actual = numeroCertInput.value.trim(); // ej: "MEX-00000012"
    const partes = actual.split("-");

    if (partes.length !== 2) {
      return; // formato inesperado, no hacemos nada
    }

    const prefijo = partes[0] + "-"; // "MEX-"
    const parteNumerica = partes[1]; // "00000012"
    const numero = parseInt(parteNumerica, 10);

    if (isNaN(numero)) {
      return;
    }

    const siguiente = (numero + 1).toString().padStart(parteNumerica.length, "0");
    numeroCertInput.value = prefijo + siguiente; // "MEX-00000013"
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
      alertas("Faltan campos para generar el código QR", "warning");
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

    // Si ya se está enviando, ignoramos el submit
    if (enviando) {
      return;
    }

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
      alertas("La fecha de expiración debe ser posterior a la del certificado.", "warning");
      return;
    }

    if (nuevoInspectorCheck && nuevoInspectorCheck.checked) {
      inspectorSelect.value = nuevoInspectorInput.value;
    }

    // Bloqueamos el envío
    enviando = true;
    if (btnSubmit) {
      btnSubmit.disabled = true;
      if (btnSubmit.tagName === "BUTTON") btnSubmit.textContent = "Generando...";
    }

    const data = new FormData(frm);
    const url = base_url + "CrearCertificados/crear";

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);

    http.onreadystatechange = function () {
      if (this.readyState !== 4) {
        return;
      }

      // En este punto la petición terminó
      enviando = false;

      if (this.status !== 200) {
        // Error HTTP, reactivamos botón
        if (btnSubmit) {
          btnSubmit.disabled = false;
          if (btnSubmit.tagName === "BUTTON" && originalBtnText) {
            btnSubmit.textContent = originalBtnText;
          }
        }
        alertas("Ocurrió un error al generar el certificado.", "error");
        return;
      }

      console.log(this.responseText);
      let res;
      try {
        res = JSON.parse(this.responseText);
      } catch (err) {
        if (btnSubmit) {
          btnSubmit.disabled = false;
          if (btnSubmit.tagName === "BUTTON" && originalBtnText) {
            btnSubmit.textContent = originalBtnText;
          }
        }
        alertas("Respuesta inválida del servidor.", "error");
        return;
      }

      alertas(res.msg, res.icono);

      if (res.icono === "success") {
        // Guardamos el número actual ANTES del reset
        const certActual = numeroCertInput ? numeroCertInput.value : "";

        frm.reset();
        qrCodeContainer.innerHTML = "";
        imagenFirma.src = "";
        firmaPreview.style.display = "none";

        // Restaurar y calcular el siguiente número
        if (numeroCertInput && certActual) {
          numeroCertInput.value = certActual;
          actualizarNumeroCertificadoLocal();
        }

        verificarCamposCompletos();
        window.open(res.pdf_url, "_blank");
        window.location.href = res.zip_url;
        // No reactivamos el botón aquí porque la página cambiará
        return;
      }

      // Si no fue success, reactivamos el botón
      if (btnSubmit) {
        btnSubmit.disabled = false;
        if (btnSubmit.tagName === "BUTTON" && originalBtnText) {
          btnSubmit.textContent = originalBtnText;
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
