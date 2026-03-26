document.addEventListener("DOMContentLoaded", function () {
  const base =
    (typeof window.base_url !== "undefined" && window.base_url) ||
    (typeof window.BASE_URL !== "undefined" && window.BASE_URL) ||
    (typeof BASE_URL !== "undefined" && BASE_URL) ||
    "";

  const endpoint = base + "Principal/listarCertificados";
  const endpointDescarga = base + "Principal/descargarCertificado";

  const inputBuscar = document.getElementById("buscar");
  const tbody = document.querySelector("#tablaCertificados tbody");
  const perPageSelect = document.getElementById("certificadosPerPage");
  const resumen = document.getElementById("certificadosResumen");
  const pagInfo = document.getElementById("certificadosPaginacionInfo");
  const paginaActual = document.getElementById("certificadosPaginaActual");
  const btnPrev = document.getElementById("btnPrevCertificados");
  const btnNext = document.getElementById("btnNextCertificados");
  const btnRecargar = document.getElementById("btnRecargarCertificados");

  let currentPage = 1;
  let currentPerPage = perPageSelect ? perPageSelect.value : "25";
  let totalPages = 1;
  let searchTimer = null;
  // let autoRefresh = null; // Ya no se usa

  function getBuscarValue() {
    return inputBuscar ? inputBuscar.value.trim() : "";
  }

  function renderRows(rows) {
    let html = "";

    if (Array.isArray(rows) && rows.length > 0) {
      rows.forEach((cert) => {
        html += `
          <tr>
            <td>${cert.fecha_mostrar ?? ""}</td>
            <td>${cert.license_plate ?? ""}</td>
            <td>${cert.vin ?? ""}</td>
            <td>
              <span class="badge-cert">
                <i class="fas fa-certificate"></i>
                ${cert.cert_number ?? ""}
              </span>
            </td>
            <td>
              ${
                cert.archivo_url
                  ? `<a href="#" class="cert-link" data-file="${cert.archivo_url}">
                      Descargar
                    </a>`
                  : `<span class="text-muted">Sin archivo</span>`
              }
            </td>
          </tr>
        `;
      });
    } else {
      html = `
        <tr>
          <td colspan="5" class="text-center text-muted py-4">
            No se encontraron certificados
          </td>
        </tr>
      `;
    }

    tbody.innerHTML = html;
  }

  function actualizarPaginacion(data) {
    const total = Number(data.total || 0);
    const page = Number(data.page || 1);
    const perPage = data.per_page;
    totalPages = Number(data.total_pages || 1);

    paginaActual.textContent = `Página ${page} de ${totalPages}`;

    btnPrev.disabled = page <= 1;
    btnNext.disabled = page >= totalPages;

    if (perPage === "all") {
      resumen.textContent = `Mostrando todos los registros (${total.toLocaleString()})`;
      pagInfo.textContent = `Consulta completa de ${total.toLocaleString()} certificados`;
    } else {
      const perPageNum = Number(perPage || 25);
      const desde = total === 0 ? 0 : (page - 1) * perPageNum + 1;
      const hasta = Math.min(page * perPageNum, total);

      resumen.textContent = `Mostrando ${desde.toLocaleString()} - ${hasta.toLocaleString()} de ${total.toLocaleString()} certificados`;
      pagInfo.textContent = `Página ${page} de ${totalPages}`;
    }
  }

  function renderError(msg) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-danger py-4">
          ${msg}
        </td>
      </tr>
    `;
  }

  function cargarCertificados(page = 1, perPage = "25") {
    const buscar = getBuscarValue();

    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4 text-muted">
          Cargando certificados...
        </td>
      </tr>
    `;

    const xhr = new XMLHttpRequest();
    const url =
      `${endpoint}?page=${encodeURIComponent(page)}` +
      `&per_page=${encodeURIComponent(perPage)}` +
      `&buscar=${encodeURIComponent(buscar)}`;

    xhr.open("GET", url, true);

    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) return;

      if (xhr.status === 200) {
        let res = null;

        try {
          res = JSON.parse(xhr.responseText);
        } catch (e) {
          console.error("JSON inválido:", e, xhr.responseText);
          renderError("Error al procesar la respuesta del servidor");
          return;
        }

        renderRows(res.rows || []);
        actualizarPaginacion(res);

        currentPage = Number(res.page || 1);
        currentPerPage = res.per_page || perPage;
      } else {
        renderError("Error al cargar certificados");
      }
    };

    xhr.send();
  }

  function confirmarCargaPesada(value) {
    return new Promise((resolve) => {
      if (value !== "5000" && value !== "all") {
        resolve(true);
        return;
      }

      let mensaje = "";
      let titulo = "Consulta pesada";

      if (value === "5000") {
        mensaje =
          "Vas a consultar 5000 registros. La consulta puede tardar varios segundos.";
      } else {
        mensaje =
          "Vas a consultar TODOS los certificados. Esto puede tardar bastante y consumir más memoria.";
      }

      Swal.fire({
        icon: "warning",
        title: titulo,
        html: `<p>${mensaje}</p><p><strong>¿Deseas continuar?</strong></p>`,
        showCancelButton: true,
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        reverseButtons: true,
      }).then((result) => {
        resolve(result.isConfirmed);
      });
    });
  }

  function validarYDescargar(filePath) {
    if (!filePath) {
      Swal.fire({
        icon: "error",
        title: "Archivo no disponible",
        text: "Este certificado no tiene un archivo asociado.",
      });
      return;
    }

    Swal.fire({
      title: "Validando archivo...",
      text: "Espere un momento",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    const xhr = new XMLHttpRequest();
    const url = `${endpointDescarga}?file=${encodeURIComponent(filePath)}`;

    xhr.open("HEAD", url, true);

    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) return;

      Swal.close();

      if (xhr.status === 200) {
        window.location.href = url;
      } else if (xhr.status === 404) {
        Swal.fire({
          icon: "error",
          title: "Archivo no encontrado",
          text: "El archivo ya no existe en el servidor o fue eliminado.",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "No fue posible descargar",
          text: "Ocurrió un problema al validar el archivo.",
        });
      }
    };

    xhr.onerror = function () {
      Swal.close();
      Swal.fire({
        icon: "error",
        title: "Error de conexión",
        text: "No se pudo verificar el archivo.",
      });
    };

    xhr.send();
  }

  if (inputBuscar) {
    inputBuscar.addEventListener("input", function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(function () {
        currentPage = 1;
        cargarCertificados(currentPage, currentPerPage);
      }, 400);
    });

    inputBuscar.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        clearTimeout(searchTimer);
        currentPage = 1;
        cargarCertificados(currentPage, currentPerPage);
      }
    });
  }

  if (perPageSelect) {
    perPageSelect.addEventListener("change", async function () {
      const nuevoValor = this.value;

      const confirmado = await confirmarCargaPesada(nuevoValor);

      if (!confirmado) {
        this.value = String(currentPerPage);
        return;
      }

      currentPage = 1;
      cargarCertificados(currentPage, nuevoValor);
    });
  }

  if (btnPrev) {
    btnPrev.addEventListener("click", function () {
      if (currentPage > 1) {
        cargarCertificados(currentPage - 1, currentPerPage);
      }
    });
  }

  if (btnNext) {
    btnNext.addEventListener("click", function () {
      if (currentPage < totalPages) {
        cargarCertificados(currentPage + 1, currentPerPage);
      }
    });
  }

  if (btnRecargar) {
    btnRecargar.addEventListener("click", function () {
      cargarCertificados(currentPage, currentPerPage);
    });
  }

  document.addEventListener("click", function (e) {
    const link = e.target.closest(".cert-link");
    if (!link) return;

    e.preventDefault();

    const filePath = link.getAttribute("data-file");
    validarYDescargar(filePath);
  });

  cargarCertificados(1, currentPerPage);

  /*
  autoRefresh = setInterval(function () {
    if (
      currentPerPage === "all" ||
      currentPerPage === 5000 ||
      currentPerPage === "5000"
    ) {
      return;
    }

    cargarCertificados(currentPage, currentPerPage);
  }, 15000);
  */
});
