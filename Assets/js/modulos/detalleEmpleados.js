const filtroDepartamento = document.querySelector("#filtro_departamento");
const filtroEmpleado = document.querySelector("#filtro_empleado");
const buscarNumero = document.querySelector("#buscar_numero");
const btnBuscarNumero = document.querySelector("#btnBuscarNumero");
const btnEditarEmpleado = document.querySelector("#btnEditarEmpleado");
const btnGenerarCredencial = document.querySelector("#btnGenerarCredencial");
const btnConfirmarEdicion = document.querySelector("#btnConfirmarEdicion");
const frmDetalle = document.querySelector("#frmDetalleEmpleado");
const btnLimpiarBusqueda = document.querySelector("#btnLimpiarBusqueda");
// Reset al cambiar pestaña
document
  .querySelector('button[data-bs-target="#detalleEmpleados"]')
  .addEventListener("click", function () {
    frmDetalle.reset();
    toggleEdicionEmpleado(false);
    document.querySelector("#imgEmpleado").src = "";
    filtroDepartamento.value = "";
    filtroEmpleado.innerHTML = '<option value="">Seleccione</option>';
    document.querySelector("#detalle_position_id").innerHTML =
      '<option value="">Seleccione</option>';
    limpiarHiddenInputs();
  });

document.addEventListener("DOMContentLoaded", function () {
  cargarDepartamentosDetalle();

  filtroDepartamento.addEventListener("change", function () {
    const idDep = this.value;

    // Reset de empleado
    filtroEmpleado.innerHTML = '<option value="">Seleccione</option>';
    filtroEmpleado.value = "";

    // Reset de detalle
    frmDetalle.reset();
    limpiarHiddenInputs();
    document.querySelector("#imgEmpleado").src = "";
    toggleEdicionEmpleado(false);

    // Reset puestos
    document.querySelector("#detalle_position_id").innerHTML =
      '<option value="">Seleccione</option>';

    // Cargar nuevos empleados si hay departamento
    if (idDep) {
      cargarEmpleadosPorDepartamento(idDep);
    }
  });
  filtroEmpleado.addEventListener("change", function () {
    if (!filtroDepartamento.value) {
      Swal.fire("Aviso", "Debe seleccionar un departamento primero", "warning");
      return;
    }
    cargarDetalleEmpleado(this.value);
    buscarNumero.value = document.querySelector(
      "#detalle_employee_number"
    ).value;
    buscarNumero.disabled = true;
    btnLimpiarBusqueda.classList.remove("d-none");
    filtroDepartamento.disabled = true;
    filtroEmpleado.disabled = true;
  });

  btnBuscarNumero.addEventListener("click", function () {
    const numero = buscarNumero.value.trim();
    if (numero !== "") buscarEmpleadoPorNumero(numero);
  });

  btnEditarEmpleado.addEventListener("click", function () {
    toggleEdicionEmpleado(true);
    btnConfirmarEdicion.classList.remove("d-none");
  });

  btnConfirmarEdicion.addEventListener("click", function () {
    if (!validarFormulario()) return;

    Swal.fire({
      title: "¿Deseas actualizar la información del empleado?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, actualizar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.isConfirmed) {
        const url = base_url + "Empleados/actualizar";
        const data = new FormData(frmDetalle);

        // Debug temporal
        for (let pair of data.entries()) {
          console.log(pair[0] + ": " + pair[1]);
        }

        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function () {
          if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
            if (res.icono === "success") {
              toggleEdicionEmpleado(false);
              btnConfirmarEdicion.classList.add("d-none");
              setTimeout(() => {
                location.reload();
              }, 1500);
            }
          }
        };
      }
    });
  });

  btnGenerarCredencial.addEventListener("click", function () {
    const id = filtroEmpleado.value;
    if (!id) {
      Swal.fire(
        "Aviso",
        "Debe seleccionar un empleado para generar la identificación",
        "warning"
      );
    } else {
      window.open(base_url + "Empleados/generarCredencial/" + id, "_blank");
    }
  });
});

function cargarDepartamentosDetalle(callback = null) {
  const url = base_url + "Departamentos/listar";
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const res = JSON.parse(this.responseText);
      filtroDepartamento.innerHTML = '<option value="">Seleccione</option>';
      const selectDetalle = document.querySelector("#detalle_department_id");
      selectDetalle.innerHTML = '<option value="">Seleccione</option>';
      res.forEach((dep) => {
        const option1 = new Option(dep.name, dep.id);
        const option2 = new Option(dep.name, dep.id);
        filtroDepartamento.appendChild(option1);
        selectDetalle.appendChild(option2);
      });

      // ✅ Ejecuta el callback si existe
      if (typeof callback === "function") {
        callback();
      }
    }
  };
}


function cargarEmpleadosPorDepartamento(idDepartamento) {
  const url = base_url + "Empleados/listarPorDepartamento/" + idDepartamento;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const res = JSON.parse(this.responseText);
      filtroEmpleado.innerHTML = '<option value="">Seleccione</option>';

      if (res.length === 0) {
        Swal.fire(
          "Aviso",
          "Este departamento no tiene empleados registrados",
          "info"
        );
        return;
      }

      res.forEach((emp) => {
        const option = new Option(
          `${emp.nombre_completo} (${emp.employee_number})`,
          emp.id
        );
        filtroEmpleado.appendChild(option);
      });
    }
  };
}

function buscarEmpleadoPorNumero(numero) {
  const url = base_url + "Empleados/buscarPorNumero/" + numero;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const data = JSON.parse(this.responseText);
      if (data.id) {
        // ✅ 1. Cargar todos los departamentos en el filtro
        cargarDepartamentosDetalle(() => {
          // ✅ 2. Seleccionar el departamento correcto
          filtroDepartamento.value = data.department_id;
        });

        // ✅ Llenar el select de empleado con una sola opción
        filtroEmpleado.innerHTML = `<option value="${data.id}">${data.nombre_completo} (${data.employee_number})</option>`;

        filtroEmpleado.value = data.id;
        buscarNumero.value = data.employee_number;

        filtroDepartamento.disabled = true;
        filtroEmpleado.disabled = true;
        buscarNumero.disabled = true;

        cargarDetalleEmpleado(data.id);

        btnLimpiarBusqueda.classList.remove("d-none");
      } else {
        Swal.fire(
          "Empleado no encontrado",
          "Verifica el número ingresado.",
          "warning"
        );
        buscarNumero.classList.add("is-invalid");
      }
    }
  };
}


function cargarDetalleEmpleado(id) {
  const url = base_url + "Empleados/editar/" + id;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const data = JSON.parse(this.responseText);
      if (data) {
        document.querySelector("#detalle_employee_number").value =
          data.employee_number;
        document.querySelector("#detalle_first_name").value = data.first_name;
        document.querySelector("#detalle_last_name").value = data.last_name;
        document.querySelector("#detalle_second_last_name").value =
          data.second_last_name;
        document.querySelector("#detalle_curp").value = data.curp;
        document.querySelector("#detalle_rfc").value = data.rfc;
        document.querySelector("#detalle_birth_date").value = data.birth_date;
        document.querySelector("#detalle_gender").value = data.gender;
        document.querySelector("#detalle_phone").value = data.phone;
        document.querySelector("#detalle_email").value = data.email;
cargarDepartamentosDetalle(() => {
  document.querySelector("#detalle_department_id").value = data.department_id;
  cargarPuestosDetalle(data.department_id, data.position_id);
});
setTimeout(() => {
  toggleEdicionEmpleado(false);
}, 150); // Tiempo corto para esperar a que termine el innerHTML

        document.querySelector("#imgEmpleado").src = base_url + data.photo_path;
        document.querySelector("#btnConfirmarEdicion").classList.add("d-none");

        limpiarHiddenInputs();
        frmDetalle.insertAdjacentHTML(
          "beforeend",
          `
                    <input type="hidden" name="id" value="${data.id}">
                    <input type="hidden" name="foto_actual" value="${data.photo_path}">
                `
        );

        toggleEdicionEmpleado(false);
      }
    }
  };
}

function cargarPuestosDetalle(idDepartamento, idSeleccionado = null) {
  const url = base_url + "Empleados/obtenerPuestos/" + idDepartamento;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const res = JSON.parse(this.responseText);
      const select = document.querySelector("#detalle_position_id");
      select.innerHTML = '<option value="">Seleccione</option>';

      res.forEach(puesto => {
        const option = document.createElement("option");
        option.value = puesto.id;
        option.textContent = puesto.name;
        select.appendChild(option);
      });

      // ✅ aplicar valor después de llenar
      if (idSeleccionado) {
        select.value = idSeleccionado;
      }
    }
  };
}



function toggleEdicionEmpleado(editar) {
  const campos = document.querySelectorAll(
    "#frmDetalleEmpleado input:not([type='hidden']), #frmDetalleEmpleado select"
  );
  campos.forEach((el) => {
    if (el.id !== "detalle_employee_number") {
      if (el.tagName === "SELECT") {
        el.disabled = !editar;
      } else {
        el.readOnly = !editar;
        el.disabled = false;
      }
    }
  });
}

function limpiarHiddenInputs() {
  const hiddenInputs = frmDetalle.querySelectorAll(
    "input[type='hidden'][name='id'], input[type='hidden'][name='foto_actual']"
  );
  hiddenInputs.forEach((input) => input.remove());
}

function validarFormulario() {
  const requeridos = [
    "detalle_first_name",
    "detalle_last_name",
    "detalle_curp",
    "detalle_rfc",
    "detalle_birth_date",
    "detalle_gender",
    "detalle_phone",
    "detalle_email",
    "detalle_department_id",
    "detalle_position_id",
  ];

  let valid = true;
  requeridos.forEach((id) => {
    const campo = document.getElementById(id);
    if (!campo.value.trim()) {
      campo.classList.add("is-invalid");
      valid = false;
    } else {
      campo.classList.remove("is-invalid");
    }
  });

  if (!valid) {
    Swal.fire(
      "Campos obligatorios",
      "Por favor completa todos los campos requeridos",
      "warning"
    );
  }

  return valid;
}
btnEditarEmpleado.addEventListener("click", function () {
  toggleEdicionEmpleado(true);
  btnConfirmarEdicion.classList.remove("d-none");

  // Scroll suave hacia el botón de confirmación
  btnConfirmarEdicion.scrollIntoView({ behavior: "smooth", block: "center" });
});

const inputCurpDetalle = document.querySelector("#detalle_curp");
const inputRfcDetalle = document.querySelector("#detalle_rfc");
const inputIdDetalle = () =>
  frmDetalle.querySelector('input[name="id"]')?.value || 0;

inputCurpDetalle?.addEventListener("blur", function () {
  validarCampoUnicoEditar("curp", this.value, inputIdDetalle());
});

inputRfcDetalle?.addEventListener("blur", function () {
  validarCampoUnicoEditar("rfc", this.value, inputIdDetalle());
});

function validarCampoUnicoEditar(tipo, valor, id) {
  const url =
    base_url +
    `Empleados/validar${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`;
  const data = new FormData();
  data.append(tipo, valor);
  data.append("id", id);

  const http = new XMLHttpRequest();
  http.open("POST", url, true);
  http.send(data);
  http.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
      const res = JSON.parse(this.responseText);
      const input = document.querySelector(`#detalle_${tipo}`);

      if (res.existe) {
        Swal.fire({
          icon: "warning",
          title: `${tipo.toUpperCase()} ya registrado por otro empleado`,
          text: "Por favor ingresa uno diferente.",
        });
        input.classList.add("is-invalid");
        btnConfirmarEdicion.disabled = true;
      } else {
        input.classList.remove("is-invalid");

        // Solo habilita si ambos están válidos
        const curpValido = !document
          .querySelector("#detalle_curp")
          .classList.contains("is-invalid");
        const rfcValido = !document
          .querySelector("#detalle_rfc")
          .classList.contains("is-invalid");
        if (curpValido && rfcValido) {
          btnConfirmarEdicion.disabled = false;
        }
      }
    }
  };
}
btnLimpiarBusqueda.addEventListener("click", function () {
  frmDetalle.reset();
  toggleEdicionEmpleado(false);
  limpiarHiddenInputs();

  filtroDepartamento.value = "";
  filtroEmpleado.innerHTML = '<option value="">Seleccione</option>';
  document.querySelector("#detalle_position_id").innerHTML =
    '<option value="">Seleccione</option>';
  document.querySelector("#imgEmpleado").src = "";

  filtroDepartamento.disabled = false;
  filtroEmpleado.disabled = false;
  buscarNumero.disabled = false;
  buscarNumero.value = "";
  buscarNumero.classList.remove("is-invalid");

  btnConfirmarEdicion.classList.add("d-none");
  btnLimpiarBusqueda.classList.add("d-none");

  // 🛠️ Esto recarga los departamentos disponibles después de limpiar
  cargarDepartamentosDetalle();
});
document.querySelector("#detalle_department_id").addEventListener("change", function () {
    const id = this.value;
    cargarPuestosDetalle(id);
});
