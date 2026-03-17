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
  const yearInput = document.getElementById("year");
  const numeroCertInput = document.getElementById("numero_certificado");
  const vinInput = document.getElementById("vin");
  const ebitnInput = document.getElementById("ebitn");

  // ===== NUEVO: fotos requeridas por API (8) =====
  const EXPECTED_PHOTOS = [
    "Foto VIN (fotoVin)",
    "Foto Frente (fotoFrente)",
    "Foto Atrás (fotoAtras)",
    "Foto Piloto (fotoPiloto)",
    "Foto Pasajero (fotoPasajero)",
    "Foto Puerta (fotoPuerta)",
    "Foto Scanner (fotoScanner)",
    "Foto Taller (fotoTaller)",
  ];

  // ===== Base URL (si no existe en tu global, deja esto como está) =====
  // Tu código ya usa base_url; asumo que existe globalmente.
  // const base_url = window.base_url || "";

  // Establecer fecha mínima hoy
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const fechaMinima = `${yyyy}-${mm}-${dd}`;
  if (fechaInput) fechaInput.setAttribute("min", fechaMinima);
  if (expiracionInput) expiracionInput.readOnly = true;

  // Flag para evitar envíos múltiples
  let enviando = false;

  // Botón submit
  const btnSubmit = frm
    ? frm.querySelector('button[type="submit"], input[type="submit"]')
    : null;
  const originalBtnText =
    btnSubmit && btnSubmit.tagName === "BUTTON" ? btnSubmit.textContent : null;

  // Inputs required (NO usamos esto para validar fotos; fotos las validamos aparte)
  const requiredInputs = document.querySelectorAll(
    "#formularioCertificado input[required], #formularioCertificado select[required]",
  );

  // ===== Helpers: mayúsculas =====
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

  // ===== Validación year =====
  if (yearInput) {
    const minYear = 1950;
    const maxYear = yyyy + 1;
    yearInput.setAttribute("min", minYear);
    yearInput.setAttribute("max", maxYear);

    yearInput.addEventListener("change", function () {
      const valor = parseInt(yearInput.value, 10);
      if (isNaN(valor) || valor < minYear || valor > maxYear) {
        alertas(
          `El año del vehículo debe estar entre ${minYear} y ${maxYear}.`,
          "warning",
        );
        yearInput.value = "";
      }
    });
  }

  // ===== Validación VIN =====
  function validarVIN(valor) {
    const vin = String(valor || "")
      .trim()
      .toUpperCase();

    return /^[A-Z0-9]{17}$/.test(vin);
  }

  // ===== Validación EEI ITN / EBITN =====
  function validarEBITN(valor) {
    const ebitn = String(valor || "")
      .trim()
      .toUpperCase();

    return /^X\d{14}$/.test(ebitn);
  }

  if (vinInput) {
    vinInput.addEventListener("input", function () {
      this.value = this.value
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, "")
        .slice(0, 17);
    });

    vinInput.addEventListener("blur", function () {
      const vin = this.value.trim();

      if (!vin) return;

      if (!validarVIN(vin)) {
        alertas(
          "El VIN debe contener exactamente 17 caracteres alfanuméricos.",
          "warning",
        );
        this.focus();
      }
    });
  }

  if (ebitnInput) {
    ebitnInput.addEventListener("input", function () {
      this.value = this.value
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, "")
        .slice(0, 15);
    });

    ebitnInput.addEventListener("blur", function () {
      const ebitn = this.value.trim();

      if (!ebitn) return;

      if (!validarEBITN(ebitn)) {
        alertas(
          "El EEI ITN debe iniciar con X y contener exactamente 14 dígitos después. Ejemplo: X20260317123456.",
          "warning",
        );
        this.focus();
      }
    });
  }

  // ===== Firma inspector =====
  if (inspectorSelect) {
    inspectorSelect.addEventListener("change", function () {
      const id = this.value;
      if (!id) {
        if (firmaPreview) firmaPreview.style.display = "none";
        if (imagenFirma) imagenFirma.src = "";
        return;
      }

      fetch(base_url + "CrearCertificados/obtenerFirmaInspector/" + id)
        .then((res) => res.json())
        .then((data) => {
          if (data.firma) {
            if (imagenFirma) imagenFirma.src = data.firma;
            if (firmaPreview) firmaPreview.style.display = "block";
          } else {
            if (firmaPreview) firmaPreview.style.display = "none";
            if (imagenFirma) imagenFirma.src = "";
          }
        })
        .catch(() => {
          if (firmaPreview) firmaPreview.style.display = "none";
          if (imagenFirma) imagenFirma.src = "";
        });
    });
  }

  // ===== Fecha expiración +3 meses =====
  if (fechaInput && expiracionInput) {
    fechaInput.addEventListener("change", function () {
      const valor = fechaInput.value;
      if (valor < fechaMinima) {
        alertas("La fecha no puede ser anterior a hoy.", "warning");
        fechaInput.value = "";
        expiracionInput.value = "";
        return;
      }

      const fecha = new Date(valor);
      fecha.setMonth(fecha.getMonth() + 3);

      const y = fecha.getFullYear();
      const m = String(fecha.getMonth() + 1).padStart(2, "0");
      const d = String(fecha.getDate()).padStart(2, "0");
      expiracionInput.value = `${y}-${m}-${d}`;
    });
  }

  // ===== Dirección existente vs nueva =====
  function actualizarVisibilidadDireccion() {
    const usandoDireccionExistente =
      selectDireccion && selectDireccion.value !== "";

    if (usandoDireccionExistente) {
      if (camposNuevaDireccion) camposNuevaDireccion.style.display = "none";
      if (telefonoInput) telefonoInput.required = false;
    } else {
      if (camposNuevaDireccion) camposNuevaDireccion.style.display = "block";
      if (telefonoInput) telefonoInput.required = true;
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

  // ===== Número certificado local +1 (solo UI) =====
  function actualizarNumeroCertificadoLocal() {
    if (!numeroCertInput || !numeroCertInput.value) return;

    const actual = numeroCertInput.value.trim();
    const partes = actual.split("-");
    if (partes.length !== 2) return;

    const prefijo = partes[0] + "-";
    const parteNumerica = partes[1];
    const numero = parseInt(parteNumerica, 10);
    if (isNaN(numero)) return;

    const siguiente = (numero + 1)
      .toString()
      .padStart(parteNumerica.length, "0");

    numeroCertInput.value = prefijo + siguiente;
  }

  // ===== QR enable/disable (no considera files) =====
  function verificarCamposCompletos() {
    const usandoDireccionExistente =
      selectDireccion && selectDireccion.value !== "";

    const incompletos = Array.from(requiredInputs).some((input) => {
      if (
        usandoDireccionExistente &&
        camposNuevaDireccion &&
        camposNuevaDireccion.contains(input)
      ) {
        return false;
      }

      if (input.type === "file") return false;

      return !String(input.value || "").trim();
    });

    if (generarQRBtn) generarQRBtn.disabled = incompletos;
  }

  requiredInputs.forEach((input) => {
    input.addEventListener("input", verificarCamposCompletos);
    input.addEventListener("change", verificarCamposCompletos);
  });

  verificarCamposCompletos();

  // ===== QR Generación =====
  if (generarQRBtn) {
    generarQRBtn.addEventListener("click", function () {
      const vin = (document.querySelector("#vin")?.value || "").trim();
      const propietario = (
        document.querySelector("#propietario")?.value || ""
      ).trim();
      const fabricadoEn = (
        document.querySelector("#fabricado_en")?.value || ""
      ).trim();
      const year = (document.querySelector("#year")?.value || "").trim();
      const modelo = (document.querySelector("#modelo")?.value || "").trim();
      const marca = (document.querySelector("#marca")?.value || "").trim();

      if (!vin || !propietario || !fabricadoEn || !year || !modelo || !marca) {
        alertas("Faltan campos para generar el código QR", "warning");
        return;
      }

      const contenidoQR = `${vin}|${propietario}|${fabricadoEn}|${year}|${modelo}|${marca}`;

      if (qrCodeContainer) qrCodeContainer.innerHTML = "";

      new QRCode(qrCodeContainer, {
        text: contenidoQR,
        width: 256,
        height: 256,
      });
    });
  }

  // ===== NUEVO: Validación estricta de fotos para API =====
  function getPhotoInputs() {
    return Array.from(
      frm.querySelectorAll('input[type="file"][name="imagenes[]"]'),
    );
  }

  function isJpgFile(file) {
    if (!file) return false;

    const name = (file.name || "").toLowerCase();
    const type = (file.type || "").toLowerCase();

    const byExt = name.endsWith(".jpg") || name.endsWith(".jpeg");
    const byMime = type === "image/jpeg";

    return byExt || byMime;
  }

  function validarFotosApi() {
    const photoInputs = getPhotoInputs();

    if (photoInputs.length !== EXPECTED_PHOTOS.length) {
      alertas(
        `La vista no coincide con la configuración esperada. Se requieren ${EXPECTED_PHOTOS.length} campos de foto.`,
        "error",
      );
      return false;
    }

    for (let i = 0; i < photoInputs.length; i++) {
      const input = photoInputs[i];
      const files = input.files;

      if (!files || files.length !== 1) {
        alertas(`Falta: ${EXPECTED_PHOTOS[i]}`, "warning");
        input.focus();
        return false;
      }

      const file = files[0];

      if (!isJpgFile(file)) {
        alertas(
          `La ${EXPECTED_PHOTOS[i]} debe ser JPG/JPEG. Archivo: ${file.name}`,
          "warning",
        );
        input.focus();
        return false;
      }
    }

    const ext = document.getElementById("foto_Extension");
    if (ext) ext.value = "jpg";

    return true;
  }

  // ===== Submit =====
  if (frm) {
    frm.addEventListener("submit", function (e) {
      e.preventDefault();

      if (enviando) return;

      const valorFecha = fechaInput ? fechaInput.value : "";
      const valorExp = expiracionInput ? expiracionInput.value : "";

      if (!valorFecha) {
        alertas("La fecha del certificado es obligatoria.", "warning");
        return;
      }

      if (valorFecha < fechaMinima) {
        alertas(
          "La fecha del certificado no puede ser anterior a hoy.",
          "warning",
        );
        return;
      }

      if (valorExp && valorExp <= valorFecha) {
        alertas(
          "La fecha de expiración debe ser posterior a la del certificado.",
          "warning",
        );
        return;
      }

      if (nuevoInspectorCheck && nuevoInspectorInput && inspectorSelect) {
        if (nuevoInspectorCheck.checked) {
          inspectorSelect.value = nuevoInspectorInput.value;
        }
      }

      const vinValor = vinInput ? vinInput.value.trim() : "";
      if (!validarVIN(vinValor)) {
        alertas(
          "El VIN debe contener exactamente 17 caracteres alfanuméricos.",
          "warning",
        );
        if (vinInput) vinInput.focus();
        return;
      }

      const ebitnValor = ebitnInput ? ebitnInput.value.trim() : "";
      if (!validarEBITN(ebitnValor)) {
        alertas(
          "El EEI ITN debe iniciar con X y contener exactamente 14 dígitos después. Ejemplo: X20260317123456.",
          "warning",
        );
        if (ebitnInput) ebitnInput.focus();
        return;
      }

      if (!validarFotosApi()) {
        return;
      }

      enviando = true;

      if (btnSubmit) {
        btnSubmit.disabled = true;
        if (btnSubmit.tagName === "BUTTON") {
          btnSubmit.textContent = "Generando...";
        }
      }

      const data = new FormData(frm);
      const url = base_url + "CrearCertificados/crear";

      const http = new XMLHttpRequest();
      http.open("POST", url, true);
      http.send(data);

      http.onreadystatechange = function () {
        if (this.readyState !== 4) return;

        enviando = false;

        if (this.status !== 200) {
          if (btnSubmit) {
            btnSubmit.disabled = false;
            if (btnSubmit.tagName === "BUTTON" && originalBtnText) {
              btnSubmit.textContent = originalBtnText;
            }
          }

          alertas("Ocurrió un error al generar el certificado.", "error");
          return;
        }

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

        alertas(res.msg || "Proceso finalizado", res.icono || "info");

        if (typeof res.api_ok !== "undefined") {
          if (res.api_ok) {
            console.log("API externa OK", res.api_status, res.api_msg || "");
          } else {
            console.warn(
              "API externa FALLÓ",
              res.api_status,
              res.api_msg || "",
            );
          }
        }

        if (res.icono === "success") {
          const certActual = numeroCertInput ? numeroCertInput.value : "";

          frm.reset();

          if (qrCodeContainer) qrCodeContainer.innerHTML = "";
          if (imagenFirma) imagenFirma.src = "";
          if (firmaPreview) firmaPreview.style.display = "none";

          if (numeroCertInput && certActual) {
            numeroCertInput.value = certActual;
            actualizarNumeroCertificadoLocal();
          }

          verificarCamposCompletos();

          if (res.pdf_url) window.open(res.pdf_url, "_blank");
          if (res.zip_url) window.location.href = res.zip_url;
          return;
        }

        if (btnSubmit) {
          btnSubmit.disabled = false;
          if (btnSubmit.tagName === "BUTTON" && originalBtnText) {
            btnSubmit.textContent = originalBtnText;
          }
        }
      };
    });
  }
});

function cargarLatLonDireccion(id) {
  const url = base_url + "CrearCertificados/obtenerLatLon/" + id;
  const latInput = document.getElementById("latitud");
  const lonInput = document.getElementById("longitud");

  if (!latInput || !lonInput) return;

  if (id) {
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        latInput.value = data.latitude || "";
        lonInput.value = data.longitude || "";

        if (latInput.parentElement && latInput.parentElement.parentElement) {
          latInput.parentElement.parentElement.style.display = "none";
        }

        latInput.required = false;
        lonInput.required = false;
      })
      .catch((error) => {
        console.error("Error al obtener lat/lon:", error);
        latInput.value = "";
        lonInput.value = "";

        if (latInput.parentElement && latInput.parentElement.parentElement) {
          latInput.parentElement.parentElement.style.display = "block";
        }

        latInput.required = true;
        lonInput.required = true;
      });
  } else {
    latInput.value = "";
    lonInput.value = "";

    if (latInput.parentElement && latInput.parentElement.parentElement) {
      latInput.parentElement.parentElement.style.display = "block";
    }

    latInput.required = true;
    lonInput.required = true;
  }
}

function alertas(msg, icono) {
  Swal.fire("Aviso", String(msg || "").toUpperCase(), icono || "info");
}
