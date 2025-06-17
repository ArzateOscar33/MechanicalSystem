const frm = document.querySelector("#frmEmpleados");
const tblBody = document.querySelector("#tblEmpleados");
let tblEmpleados;

document.addEventListener("DOMContentLoaded", function () {
    // Inicializar DataTable
    tblEmpleados = $("#tblEmpleados").DataTable({
        ajax: {
            url: base_url + "Empleados/listar",
            dataSrc: "",
        },
        columns: [
            { data: "employee_number",title: "Número de Empleado"},
            { data: "nombre_completo",title: "Nombre Completo"},
           // { data: "curp" },
           // { data: "rfc" },
         { data: "phone",title: "Teléfono"},
           // { data: "email" },
           // { data: "birth_date" },
            { data: "gender",title: "Género"},
            { data: "departamento", title: "Departamento"},
            { data: "puesto" ,title: "Puesto"},
            { data: "issue_date", title: "Fecha de Emisión"},
            { data: "accion",title: "Acciones"},
             
        ],
        language,
        dom,
        buttons,
    });

    // Detectar cuando se cambia a la pestaña "Nuevo Empleado"
    document.querySelector('button[data-bs-target="#nuevoEmpleado"]').addEventListener("click", () => {
        frm.reset();
        generarNumeroEmpleado();
        cargarDepartamentos();
        document.querySelector("#position_id").innerHTML = '<option value="">Seleccione un departamento primero</option>';
    });

frm.addEventListener("submit", function (e) {
    e.preventDefault();

    // Validar campos requeridos
    const camposRequeridos = [
        "first_name",
        "last_name",
        "curp",
        "rfc",
        "employee_number",
        "phone",
        "email",
        "birth_date",
        "gender",
        "department_id",
        "position_id"
    ];

    let camposVacios = [];

    camposRequeridos.forEach(id => {
        const campo = frm.querySelector(`#${id}`);
        if (!campo || campo.value.trim() === "") {
            camposVacios.push(id);
            campo.classList.add("is-invalid");
        } else {
            campo.classList.remove("is-invalid");
        }
    });

    if (camposVacios.length > 0) {
        Swal.fire({
            icon: "warning",
            title: "Campos requeridos",
            text: "Por favor completa todos los campos obligatorios antes de continuar.",
        });
        return;
    }

    // Continuar con el envío si todo está bien
    const url = base_url + "Empleados/registrar";
    const data = new FormData(frm);

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
           console.log(this.responseText); // debug
            const res = JSON.parse(this.responseText);
            Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
            if (res.icono === "success") {
                tblEmpleados.ajax.reload();
                frm.reset();
                generarNumeroEmpleado(); // genera nuevo número para el siguiente
            }
        }
    };
});


    // Cuando se selecciona un departamento, cargar sus puestos
    document.querySelector("#department_id").addEventListener("change", function () {
        const id = this.value;
        cargarPuestos(id);
    });
});

// Función para generar número único de empleado
function generarNumeroEmpleado() {
    const url = base_url + "Empleados/generarNumeroEmpleado";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector("#employee_number").value = res.numero;
        }
    };
}

// Cargar departamentos en el select
function cargarDepartamentos() {
    const url = base_url + "Departamentos/listar";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const select = document.querySelector("#department_id");
            select.innerHTML = '<option value="">Seleccione</option>';
            res.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.id;
                option.textContent = dep.name;
                select.appendChild(option);
            });
        }
    };
}

// Cargar puestos por departamento
function cargarPuestos(idDepartamento) {
    const url = base_url + "Empleados/obtenerPuestos/" + idDepartamento;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const res = JSON.parse(this.responseText);
            const select = document.querySelector("#position_id");
            select.innerHTML = '<option value="">Seleccione</option>';
            res.forEach(puesto => {
                const option = document.createElement("option");
                option.value = puesto.id;
                option.textContent = puesto.name;
                select.appendChild(option);
            });
        }
    };
}

// Eliminar empleado
function eliminarEmpleado(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "Empleados/eliminar/" + id;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    const res = JSON.parse(this.responseText);
                    Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
                    if (res.icono === "success") {
                        tblEmpleados.ajax.reload();
                    }
                }
            };
        }
    });
}

 
const inputCurpNuevo = document.querySelector("#curp");
const inputRfcNuevo = document.querySelector("#rfc");

inputCurpNuevo?.addEventListener("blur", function () {
    validarCampoUnicoNuevo("curp", this.value);
});

inputRfcNuevo?.addEventListener("blur", function () {
    validarCampoUnicoNuevo("rfc", this.value);
});

function validarCampoUnicoNuevo(tipo, valor) {
    const url = base_url + `Empleados/validar${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`;
    const data = new FormData();
    data.append(tipo, valor);
    data.append("id", 0); // en nuevo empleado no hay ID

    const http = new XMLHttpRequest();
    http.open("POST", url, true);
    http.send(data);
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          console.log(this.responseText);
            const res = JSON.parse(this.responseText);
            if (res.existe) {
                Swal.fire({
                    icon: "warning",
                    title: `El ${tipo.toUpperCase()} ya está registrado`,
                    text: "Por favor ingresa uno diferente",
                });
                document.querySelector(`#${tipo}`).classList.add("is-invalid");
                frm.querySelector('button[type="submit"]').disabled = true;
            } else {
                document.querySelector(`#${tipo}`).classList.remove("is-invalid");
                frm.querySelector('button[type="submit"]').disabled = false;
            }
        }
    };
}

function generarTodasLasCredenciales() {
    Swal.fire({
        title: "Generando credenciales...",
        text: "Por favor espera unos segundos.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    const url = base_url + "Empleados/generarTodasCredenciales";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();

    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            Swal.close(); // Cierra el loading

            try {
                const res = JSON.parse(this.responseText);
                if (res.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "¡Credenciales generadas!",
                        text: "El archivo PDF se ha creado correctamente.",
                        confirmButtonText: "Descargar",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(res.url, "_blank"); // abrir nueva pestaña o iniciar descarga
                        }
                    });
                } else {
                    Swal.fire("Error", res.msg || "Hubo un problema al generar el archivo.", "error");
                }
            } catch (e) {
                Swal.fire("Error", "No se pudo procesar la respuesta del servidor.", "error");
                console.error("Respuesta no válida:", this.responseText);
            }
        }
    };
}

