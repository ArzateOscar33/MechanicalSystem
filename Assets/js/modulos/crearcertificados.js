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

  // ===== Botones del nuevo flujo =====
  const btnPrevalidarDatos = document.getElementById("btnPrevalidarDatos");
  const btnPrevalidarFotos = document.getElementById("btnPrevalidarFotos");
  const btnCrearCertificado = document.getElementById("btnCrearCertificado");

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

  // ===== Estado del flujo =====
  // Controla en qué paso estamos para evitar doble envío
  let enviando = false;

  // ===== Fecha mínima hoy =====
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, "0");
  const dd = String(today.getDate()).padStart(2, "0");
  const fechaMinima = `${yyyy}-${mm}-${dd}`;
  if (fechaInput) fechaInput.setAttribute("min", fechaMinima);
  if (expiracionInput) expiracionInput.readOnly = true;

  // ===== Inputs required =====
  const requiredInputs = document.querySelectorAll(
    "#formularioCertificado input[required], #formularioCertificado select[required]",
  );

  // =========================================================
  // HELPERS
  // =========================================================

  // Convierte todos los campos de texto a mayúsculas
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

  function validarVIN(valor) {
    return /^[A-Z0-9]{17}$/.test(
      String(valor || "")
        .trim()
        .toUpperCase(),
    );
  }

  function validarEBITN(valor) {
    return /^X\d{14}$/.test(
      String(valor || "")
        .trim()
        .toUpperCase(),
    );
  }

  function getPhotoInputs() {
    return Array.from(
      frm.querySelectorAll('input[type="file"][name="imagenes[]"]'),
    );
  }

  function isJpgFile(file) {
    if (!file) return false;
    const name = (file.name || "").toLowerCase();
    const type = (file.type || "").toLowerCase();
    return (
      name.endsWith(".jpg") || name.endsWith(".jpeg") || type === "image/jpeg"
    );
  }

  // =========================================================
  // ESTADO DE BOTONES
  // =========================================================

  /**
   * Inicializa los tres botones al estado inicial:
   * - Prevalidar Datos: habilitado
   * - Prevalidar Fotos: deshabilitado
   * - Crear Certificado: deshabilitado
   */
  function resetearEstadoBotones() {
    if (btnPrevalidarDatos) btnPrevalidarDatos.disabled = false;
    if (btnPrevalidarFotos) btnPrevalidarFotos.disabled = true;
    if (btnCrearCertificado) btnCrearCertificado.disabled = true;
  }

  function bloquearCamposDatos() {
    const inputs = frm.querySelectorAll(
      "input:not([type='file']):not([type='hidden']), select, textarea",
    );
    inputs.forEach((el) => {
      if (el.tagName === "SELECT") {
        // Los select no soportan readonly, usamos pointer-events
        el.style.pointerEvents = "none";
        el.style.opacity = "0.6";
      } else {
        el.readOnly = true;
        el.style.opacity = "0.6";
      }
    });
  }

  function desbloquearCamposDatos() {
    const inputs = frm.querySelectorAll(
      "input:not([type='file']):not([type='hidden']), select, textarea",
    );

    inputs.forEach((el) => {
      if (el.id === "numero_certificado") return; // 🔥 EXCLUSIÓN CLAVE

      if (el.tagName === "SELECT") {
        el.style.pointerEvents = "";
        el.style.opacity = "";
      } else {
        el.readOnly = false;
        el.style.opacity = "";
      }
    });
  }

  // Estado inicial al cargar la página
  resetearEstadoBotones();

  // =========================================================
  // VALIDACIONES
  // =========================================================

  if (yearInput) {
    const minYear = 1950;
    const maxYear = yyyy + 1;
    yearInput.setAttribute("min", minYear);
    yearInput.setAttribute("max", maxYear);
    yearInput.addEventListener("change", function () {
      const valor = parseInt(yearInput.value, 10);
      if (isNaN(valor) || valor < minYear || valor > maxYear) {
        alertas(`El año debe estar entre ${minYear} y ${maxYear}.`, "warning");
        yearInput.value = "";
      }
    });
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
          "El EEI ITN debe iniciar con X y contener exactamente 14 dígitos. Ejemplo: X20260317123456.",
          "warning",
        );
        this.focus();
      }
    });
  }

  function validarCamposDatos() {
    const usandoDireccionExistente =
      selectDireccion && selectDireccion.value !== "";

    const incompletos = Array.from(requiredInputs).some((input) => {
      if (input.type === "file") return false;
      if (
        usandoDireccionExistente &&
        camposNuevaDireccion &&
        camposNuevaDireccion.contains(input)
      ) {
        return false;
      }
      return !String(input.value || "").trim();
    });

    if (incompletos) {
      alertas(
        "Completa todos los campos requeridos antes de prevalidar.",
        "warning",
      );
      return false;
    }

    const vinValor = vinInput ? vinInput.value.trim() : "";
    if (!validarVIN(vinValor)) {
      alertas(
        "El VIN debe contener exactamente 17 caracteres alfanuméricos.",
        "warning",
      );
      if (vinInput) vinInput.focus();
      return false;
    }

    const ebitnValor = ebitnInput ? ebitnInput.value.trim() : "";

    // ✅ SOLO validar si tiene valor
    if (ebitnValor && !validarEBITN(ebitnValor)) {
      alertas(
        "El EEI ITN debe iniciar con X y contener exactamente 14 dígitos.",
        "warning",
      );
      if (ebitnInput) ebitnInput.focus();
      return false;
    }

    const valorFecha = fechaInput ? fechaInput.value : "";
    if (!valorFecha || valorFecha < fechaMinima) {
      alertas(
        "La fecha del certificado no puede ser anterior a hoy.",
        "warning",
      );
      return false;
    }

    return true;
  }

  function validarFotosApi() {
    const photoInputs = getPhotoInputs();

    if (photoInputs.length !== EXPECTED_PHOTOS.length) {
      alertas(
        `Se requieren ${EXPECTED_PHOTOS.length} campos de foto.`,
        "error",
      );
      return false;
    }

    for (let i = 0; i < photoInputs.length; i++) {
      const files = photoInputs[i].files;
      if (!files || files.length !== 1) {
        alertas(`Falta: ${EXPECTED_PHOTOS[i]}`, "warning");
        photoInputs[i].focus();
        return false;
      }
      if (!isJpgFile(files[0])) {
        alertas(
          `La ${EXPECTED_PHOTOS[i]} debe ser JPG/JPEG. Archivo: ${files[0].name}`,
          "warning",
        );
        photoInputs[i].focus();
        return false;
      }
    }

    const ext = document.getElementById("foto_Extension");
    if (ext) ext.value = "jpg";

    return true;
  }

  // =========================================================
  // FIRMA INSPECTOR
  // =========================================================
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

  // =========================================================
  // FECHA EXPIRACIÓN
  // =========================================================
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

  // =========================================================
  // DIRECCIÓN
  // =========================================================
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

  // =========================================================
  // CARGAR LAT/LON DE DIRECCIÓN (dentro del scope correcto)
  // =========================================================
  function cargarLatLonDireccion(id) {
    const latInput = document.getElementById("latitud");
    const lonInput = document.getElementById("longitud");

    if (!latInput || !lonInput) return;

    if (id) {
      // Deshabilitar botón mientras carga para evitar envío sin coordenadas
      if (btnPrevalidarDatos) btnPrevalidarDatos.disabled = true;

      const http = new XMLHttpRequest();
      http.open(
        "GET",
        base_url + "CrearCertificados/obtenerLatLon/" + id,
        true,
      );
      http.onreadystatechange = function () {
        if (this.readyState !== 4) return;

        if (this.status === 200) {
          try {
            const data = JSON.parse(this.responseText);
            latInput.value = data.latitude || "";
            lonInput.value = data.longitude || "";
            if (latInput.parentElement?.parentElement) {
              latInput.parentElement.parentElement.style.display = "none";
            }
            latInput.required = false;
            lonInput.required = false;
            console.log("Lat cargada:", latInput.value);
            console.log("Lon cargada:", lonInput.value);
          } catch (e) {
            console.error("Error al parsear lat/lon:", e);
            latInput.value = "";
            lonInput.value = "";
            if (latInput.parentElement?.parentElement) {
              latInput.parentElement.parentElement.style.display = "block";
            }
            latInput.required = true;
            lonInput.required = true;
          }
        } else {
          latInput.value = "";
          lonInput.value = "";
          if (latInput.parentElement?.parentElement) {
            latInput.parentElement.parentElement.style.display = "block";
          }
          latInput.required = true;
          lonInput.required = true;
        }

        // Rehabilitar botón al terminar (éxito o error)
        if (btnPrevalidarDatos) btnPrevalidarDatos.disabled = false;
      };
      http.send();
    } else {
      latInput.value = "";
      lonInput.value = "";
      if (latInput.parentElement?.parentElement) {
        latInput.parentElement.parentElement.style.display = "block";
      }
      latInput.required = true;
      lonInput.required = true;
    }
  }

  if (selectDireccion) {
    selectDireccion.addEventListener("change", function () {
      actualizarVisibilidadDireccion();
      cargarLatLonDireccion(selectDireccion.value);
    });
    actualizarVisibilidadDireccion();
    cargarLatLonDireccion(selectDireccion.value);
  }

  // =========================================================
  // QR
  // =========================================================
  function verificarCamposCompletos() {
    const usandoDireccionExistente =
      selectDireccion && selectDireccion.value !== "";
    const incompletos = Array.from(requiredInputs).some((input) => {
      if (input.type === "file") return false;
      if (
        usandoDireccionExistente &&
        camposNuevaDireccion &&
        camposNuevaDireccion.contains(input)
      )
        return false;
      return !String(input.value || "").trim();
    });
    if (generarQRBtn) generarQRBtn.disabled = incompletos;
  }

  requiredInputs.forEach((input) => {
    input.addEventListener("input", verificarCamposCompletos);
    input.addEventListener("change", verificarCamposCompletos);
  });
  verificarCamposCompletos();

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

  // =========================================================
  // HELPER AJAX
  // =========================================================
  function ajaxPost(url, formData, callback) {
    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.onreadystatechange = function () {
      if (this.readyState !== 4) return;
      if (this.status !== 200) {
        callback(null, "Error de red: " + this.status);
        return;
      }

      try {
        const res = JSON.parse(this.responseText);
        console.log(this.responseText);
        callback(res, null);
      } catch (e) {
        console.error(" RAW response del servidor:", this.responseText);
        callback(null, "Respuesta inválida del servidor.");
      }
    };
    http.send(formData);
  }
  function ajaxGet(url, callback) {
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.onreadystatechange = function () {
      if (this.readyState !== 4) return;
      if (this.status !== 200) {
        callback(null, "Error de red: " + this.status);
        return;
      }
      try {
        const res = JSON.parse(this.responseText);
        callback(res, null);
      } catch (e) {
        console.error("RAW:", this.responseText);
        callback(null, "Respuesta inválida del servidor.");
      }
    };
    http.send();
  }

  function setBotonCargando(btn, cargando, textoOriginal, textoCargando) {
    if (!btn) return;
    btn.disabled = cargando;
    btn.textContent = cargando ? textoCargando : textoOriginal;
  }

  // =========================================================
  // PASO 1 — PREVALIDAR DATOS
  // =========================================================
  if (btnPrevalidarDatos) {
    btnPrevalidarDatos.addEventListener("click", function () {
      if (enviando) return;

      if (!validarCamposDatos()) return;

      // Sync inspector si aplica
      if (nuevoInspectorCheck && nuevoInspectorInput && inspectorSelect) {
        if (nuevoInspectorCheck.checked) {
          inspectorSelect.value = nuevoInspectorInput.value;
        }
      }

      enviando = true;
      setBotonCargando(
        btnPrevalidarDatos,
        true,
        "Prevalidar Datos",
        "Validando...",
      );

      const data = new FormData(frm);

      ajaxPost(
        base_url + "CrearCertificados/prevalidarDatos",
        data,
        function (res, err) {
          enviando = false;
          setBotonCargando(
            btnPrevalidarDatos,
            false,
            "Prevalidar Datos",
            "Validando...",
          );

          if (err) {
            alertas(err, "error");
            return;
          }

          if (res.icono === "success") {
            alertas(res.msg, "success");
            // Bloquear campos de datos
            bloquearCamposDatos();
            // Deshabilitar botón paso 1, habilitar paso 2
            btnPrevalidarDatos.disabled = true;
            btnPrevalidarFotos.disabled = false;
          } else {
            alertas(
              res.msg || "Error en la prevalidación de datos.",
              res.icono || "error",
            );
          }
        },
      );
    });
  }

  // =========================================================
  // PASO 2 — PREVALIDAR FOTOS
  // =========================================================
  if (btnPrevalidarFotos) {
    btnPrevalidarFotos.addEventListener("click", function () {
      if (enviando) return;

      if (!validarFotosApi()) return;

      enviando = true;
      setBotonCargando(
        btnPrevalidarFotos,
        true,
        "Prevalidar Fotos",
        "Enviando fotos...",
      );

      // Solo enviamos las fotos en este paso
      const data = new FormData();
      const photoInputs = getPhotoInputs();
      photoInputs.forEach((input) => {
        if (input.files[0]) {
          data.append("imagenes[]", input.files[0]);
        }
      });
      // VIN también lo necesita el servidor para llamar a Secomext
      data.append("vin", vinInput ? vinInput.value.trim() : "");

      ajaxPost(
        base_url + "CrearCertificados/prevalidarFotos",
        data,
        function (res, err) {
          enviando = false;
          setBotonCargando(
            btnPrevalidarFotos,
            false,
            "Prevalidar Fotos",
            "Enviando fotos...",
          );

          if (err) {
            alertas(err, "error");
            return;
          }

          if (res.icono === "success") {
            alertas(res.msg, "success");
            // Bloquear inputs de fotos también
            getPhotoInputs().forEach((input) => {
              input.style.pointerEvents = "none";
              input.style.opacity = "0.6";
            });
            // Deshabilitar paso 2, habilitar paso 3

            btnPrevalidarFotos.disabled = true;
            btnCrearCertificado.disabled = false;
          } else {
            alertas(
              res.msg || "Error en la prevalidación de fotos.",
              res.icono || "error",
            );
          }
        },
      );
    });
  }

  // =========================================================
  // PASO 3 — CREAR CERTIFICADO
  // =========================================================
  if (btnCrearCertificado) {
    btnCrearCertificado.addEventListener("click", function () {
      if (enviando) return;

      enviando = true;
      setBotonCargando(
        btnCrearCertificado,
        true,
        "Crear Certificado",
        "Generando...",
      );

      // Enviamos todos los datos del form (sin fotos, ya están en el servidor)
      const data = new FormData(frm);
      console.log("📤 Datos enviados al servidor:");
      for (let [key, value] of data.entries()) {
        console.log(`  ${key}:`, value);
      }

      ajaxPost(base_url + "CrearCertificados/crear", data, function (res, err) {
        enviando = false;
        setBotonCargando(
          btnCrearCertificado,
          false,
          "Crear Certificado",
          "Generando...",
        );

        if (err) {
          alertas(err, "error");
          return;
        }
        console.log("📦 Respuesta crear:", res);

        if (res.icono === "success") {
          // Log API externa
          if (typeof res.api_ok !== "undefined") {
            if (res.api_ok) {
              console.log(
                "API SmogsBackups OK",
                res.api_status,
                res.api_msg || "",
              );
            } else {
              console.warn(
                "API SmogsBackups FALLÓ",
                res.api_status,
                res.api_msg || "",
              );
            }
          }

          alertas(res.msg, "success");

          // Resetear formulario completamente
          frm.reset();
          desbloquearCamposDatos();
          getPhotoInputs().forEach((input) => {
            input.style.pointerEvents = "";
            input.style.opacity = "";
          });
          resetearEstadoBotones();

          if (qrCodeContainer) qrCodeContainer.innerHTML = "";
          if (imagenFirma) imagenFirma.src = "";
          if (firmaPreview) firmaPreview.style.display = "none";

          verificarCamposCompletos();
          // ✅ Actualizar número de certificado
          ajaxGet(
            base_url + "CrearCertificados/obtenerSiguienteCertNumber",
            function (data, err) {
              if (!err && data && data.cert_number) {
                if (numeroCertInput) numeroCertInput.value = data.cert_number;
              }
            },
          );

          // Abrir PDF y descargar ZIP
          if (res.pdf_url) window.open(res.pdf_url, "_blank");
          if (res.zip_url) window.location.href = res.zip_url;
        } else {
          alertas(
            res.msg || "Error al crear el certificado.",
            res.icono || "error",
          );
        }
      });
    });
  }
});

function alertas(msg, icono) {
  Swal.fire("Aviso", String(msg || "").toUpperCase(), icono || "info");
}
