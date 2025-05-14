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
            { data: "employee_number" },
            { data: "nombre_completo" },
           // { data: "curp" },
           // { data: "rfc" },
         { data: "phone" },
           // { data: "email" },
           // { data: "birth_date" },
            { data: "gender" },
            { data: "departamento" },
            { data: "puesto" },
            { data: "issue_date" },
            { data: "accion" },
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

    // Submit del formulario
    frm.addEventListener("submit", function (e) {
        e.preventDefault();
        const url = base_url + "Empleados/registrar";
        const data = new FormData(frm);

        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
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

// (Preparado para futuro uso)
function editarEmpleado(id) {
    // Implementar para cargar datos en pestaña DetalleEmpleado si se activa edición
}
