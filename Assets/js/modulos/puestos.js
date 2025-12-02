const frmPuestos = document.querySelector("#frmPuestos");
let tblPuestos;
let modalPuestos;

// Cuando el DOM está listo
document.addEventListener("DOMContentLoaded", function () {
    // Inicializar DataTable de Puestos
    tblPuestos = $("#tblPuestos").DataTable({
        ajax: {
            url: base_url + "Puestos/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id", title: "ID" },
            { data: "name", title: "Puesto" },
            { data: "departamento", title: "Departamento" },
            { data: "description", title: "Descripción" },
            { data: "accion", title: "Acciones" },
        ],
        language,
        dom,
        buttons,
    });

    // Instanciar modal de Bootstrap
    const modalElement = document.getElementById("modalPuestos");
    modalPuestos = new bootstrap.Modal(modalElement);

    // Botón "Nuevo" → abrir modal en modo registrar
    const btnNuevo = document.querySelector("#nuevo_registro");
    if (btnNuevo) {
        btnNuevo.addEventListener("click", function () {
            frmPuestos.reset();
            document.querySelector("#id").value = "";
            document.querySelector("#titleModal").textContent = "Nuevo Puesto";
            document.querySelector("#btnAccion").textContent = "Registrar";

            const selectDepto = document.querySelector("#departamento");
            if (selectDepto) {
                selectDepto.disabled = false; // en nuevo se puede elegir
            }

            cargarDepartamentosSelect(); // llenar select
            modalPuestos.show();
        });
    }

    // Envío del formulario (Registrar / Actualizar)
    if (frmPuestos) {
        frmPuestos.addEventListener("submit", function (e) {
            e.preventDefault();

            const id = document.querySelector("#id").value;
            const nombre = document.querySelector("#nombre");
            const departamento = document.querySelector("#departamento");
            const descripcion = document.querySelector("#descripcion");

            // Validaciones simples
            if (!nombre.value.trim()) {
                nombre.classList.add("is-invalid");
                Swal.fire("Aviso", "El nombre del puesto es obligatorio", "warning");
                return;
            } else {
                nombre.classList.remove("is-invalid");
            }

            if (!departamento.value.trim()) {
                departamento.classList.add("is-invalid");
                Swal.fire("Aviso", "Debe seleccionar un departamento", "warning");
                return;
            } else {
                departamento.classList.remove("is-invalid");
            }

            // Determinar si es registrar o actualizar
            let url = "";
            if (id === "" || id === "0") {
                url = base_url + "Puestos/registrar";
            } else {
                url = base_url + "Puestos/actualizar";
            }

            const data = new FormData(frmPuestos);
            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);
            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    // console.log(this.responseText); // debug si quieres
                    let res;
                    try {
                        res = JSON.parse(this.responseText);
                    } catch (error) {
                        console.error("Respuesta no válida:", this.responseText);
                        Swal.fire("Error", "No se pudo procesar la respuesta del servidor.", "error");
                        return;
                    }

                    Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
                    if (res.icono === "success") {
                        tblPuestos.ajax.reload();
                        frmPuestos.reset();
                        // Cerrar modal
                        modalPuestos.hide();
                    }
                }
            };
        });
    }
});

/**
 * Cargar departamentos en el select del modal de Puestos
 * @param {number|string} selectedId (opcional) id del departamento a seleccionar
 */
function cargarDepartamentosSelect(selectedId = "") {
    const url = base_url + "Puestos/getDepartamentos";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let res;
            try {
                res = JSON.parse(this.responseText);
            } catch (error) {
                console.error("Respuesta no válida:", this.responseText);
                return;
            }

            const select = document.querySelector("#departamento");
            if (!select) return;

            select.innerHTML = '<option value="">Seleccione un departamento</option>';
            res.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.id;
                option.textContent = dep.name;
                select.appendChild(option);
            });

            // Si mandamos un id para preseleccionar (en editar)
            if (selectedId !== "" && selectedId !== null) {
                select.value = selectedId;
            }
        }
    };
}

/**
 * Editar puesto (abre el modal y carga datos)
 */
function editarPuesto(id) {
    const url = base_url + "Puestos/editar/" + id;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            let res;
            try {
                res = JSON.parse(this.responseText);
            } catch (error) {
                console.error("Respuesta no válida:", this.responseText);
                Swal.fire("Error", "No se pudo cargar la información del puesto.", "error");
                return;
            }

            // Llenar el formulario
            document.querySelector("#id").value = res.id;
            document.querySelector("#nombre").value = res.name;
            document.querySelector("#descripcion").value = res.description || "";

            // Cargar departamentos y seleccionar el del puesto
            cargarDepartamentosSelect(res.department_id);

            // Título y botón
            document.querySelector("#titleModal").textContent = "Editar Puesto";
            document.querySelector("#btnAccion").textContent = "Actualizar";

            const selectDepto = document.querySelector("#departamento");
           /* if (selectDepto) {
                // Si quieres que NO cambien el departamento al editar:
                selectDepto.disabled = true;
            }*/

            // Mostrar modal
            modalPuestos.show();
        }
    };
}

/**
 * Eliminar puesto
 */
function eliminarPuesto(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "Puestos/eliminar/" + id;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let res;
                    try {
                        res = JSON.parse(this.responseText);
                    } catch (error) {
                        console.error("Respuesta no válida:", this.responseText);
                        Swal.fire("Error", "No se pudo procesar la respuesta del servidor.", "error");
                        return;
                    }

                    Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
                    if (res.icono === "success") {
                        tblPuestos.ajax.reload();
                    }
                }
            };
        }
    });
}
