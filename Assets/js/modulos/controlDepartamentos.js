const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmDepartamentos");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const modalDepartamentos = new bootstrap.Modal(document.getElementById("modalDepartamentos"));
let tblDepartamentos;

document.addEventListener("DOMContentLoaded", function () {
    tblDepartamentos = $("#tblDepartamentos").DataTable({
        ajax: {
            url: base_url + "Departamentos/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "name" },
            { data: "description" },
            { data: "accion" },
        ],
        language,
        dom,
        buttons,
    });

    // Botón NUEVO
    nuevo.addEventListener("click", function () {
        document.querySelector("#id").value = "";
        titleModal.textContent = "NUEVO DEPARTAMENTO";
        btnAccion.textContent = "Registrar";
        frm.reset();
        modalDepartamentos.show();
    });

    // Formulario enviar (registrar o actualizar)
    frm.addEventListener("submit", function (e) {
        e.preventDefault();
        const id = document.querySelector("#id").value;
        const url = id === "" ? base_url + "Departamentos/registrar" : base_url + "Departamentos/actualizar";
        const formData = new FormData(frm);

        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(formData);
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                if (res.icono === "success") {
                    modalDepartamentos.hide();
                    tblDepartamentos.ajax.reload();
                }
                Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
                frm.reset();
                document.querySelector("#id").value = "";
                btnAccion.textContent = "Registrar";
                titleModal.textContent = "NUEVO DEPARTAMENTO";
            }
        };
    });
});

// Función para editar
function editarDepartamento(id) {
    const url = base_url + "Departamentos/editar/" + id;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector("#id").value = res.id;
            document.querySelector("#nombre").value = res.name;
            document.querySelector("#descripcion").value = res.description;
            titleModal.textContent = "MODIFICAR DEPARTAMENTO";
            btnAccion.textContent = "Actualizar";
            modalDepartamentos.show();
        }
    };

}

function eliminarDepartamento(id) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡Esta acción no se puede deshacer!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, eliminar",
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "Departamentos/eliminar/" + id;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    const res = JSON.parse(this.responseText);
                    Swal.fire("Aviso", res.msg.toUpperCase(), res.icono);
                    if (res.icono === "success") {
                        tblDepartamentos.ajax.reload();
                    }
                }
            };
        }
    });
}

