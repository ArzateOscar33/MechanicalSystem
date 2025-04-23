document.addEventListener("DOMContentLoaded", function () {
    const frm = document.querySelector("#formularioCertificado");
    const generarQRBtn = document.querySelector("#generarQR");
    const qrCodeContainer = document.querySelector("#codigoQR");
    const selectDireccion = document.getElementById("direccion_existente");
    const camposNuevaDireccion = document.getElementById("nuevaDireccionCampos");
    const telefonoInput = document.querySelector('input[name="telefono"]');
  

    const fechaInput = document.getElementById("fecha");
    const expiracionInput = document.getElementById("fecha_expiracion");
    
    fechaInput.addEventListener("change", function () {
      if (fechaInput.value) {
        const fecha = new Date(fechaInput.value);
        fecha.setFullYear(fecha.getFullYear() + 1);
    
        // Formatear a YYYY-MM-DD
        const yyyy = fecha.getFullYear();
        const mm = String(fecha.getMonth() + 1).padStart(2, "0");
        const dd = String(fecha.getDate()).padStart(2, "0");
    
        expiracionInput.value = `${yyyy}-${mm}-${dd}`;
      }
    });

    // Todos los campos requeridos (excepto imágenes)
    const requiredInputs = document.querySelectorAll(
      "#formularioCertificado input[required], #formularioCertificado select[required]"
    );
  
    // Mostrar u ocultar campos de nueva dirección y quitar/reponer 'required' en teléfono
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
      selectDireccion.addEventListener("change", actualizarVisibilidadDireccion);
      actualizarVisibilidadDireccion(); // Estado inicial
    }
  
    // Verificar si todos los campos están completos
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
  
    // Verifica en cada cambio
    requiredInputs.forEach((input) => {
      input.addEventListener("input", verificarCamposCompletos);
      input.addEventListener("change", verificarCamposCompletos);
    });
  
    verificarCamposCompletos(); // Inicial
  
    // Generar código QR con datos clave
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
  
    // Envío del formulario
    frm.addEventListener("submit", function (e) {
      e.preventDefault();
  
      const data = new FormData(frm);
      const url = base_url + "CrearCertificados/crear";
  
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
            qrCodeContainer.innerHTML = "";
            verificarCamposCompletos();
  
            // Descargar archivos
            window.open(res.pdf_url, '_blank'); // Abre PDF
            window.location.href = res.zip_url; // Descarga ZIP
          }
        }
      };
    });
  });
  
  // Función para mostrar alertas con SweetAlert
  function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
  }
  