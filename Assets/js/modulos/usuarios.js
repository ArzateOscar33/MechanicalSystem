const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblUsuario;
document.addEventListener("DOMContentLoaded", function() {
    tblUsuario = $("#tblUsuarios").DataTable({
        ajax: {
            url: base_url + "usuarios/listar",
            dataSrc: "",
        },
        columns: [
            { data: "id" },
            { data: "username" },
            { data: "first_name" },
            { data: "last_name" },
            { data: "correo" },
            { data: "phone" },
            { data: "rol" },
            { data: "address_user" },
            { data: "accion" },
        ],
        language,
        dom,
        buttons,
    });
    
    //levantar modal
    nuevo.addEventListener("click", function() {
        cargarDirecciones();
        document.querySelector('#id').value = '';
        titleModal.textContent = "NUEVO USUARIO";
        btnAccion.textContent = 'Registrar';
        frm.reset();
        document.querySelector('#clave').removeAttribute('readonly');
        myModal.show();
    });
    //submit usuarios
    frm.addEventListener("submit", function(e) {
        e.preventDefault();
        let data = new FormData(this);
        const url = base_url + "usuarios/registrar";
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                const res = JSON.parse(this.responseText);
                if (res.icono == "success") {
                    myModal.hide();
                    tblUsuario.ajax.reload();
                }
                Swal.fire("Aviso?", res.msg.toUpperCase(), res.icono);
            }
        }
    });
});

function eliminarUser(idUser) {
    Swal.fire({
        title: "Aviso?",
        text: "Esta seguro de eliminar el registro!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Eliminar!",
    }).then((result) => {
        if (result.isConfirmed) {
            const url = base_url + "usuarios/delete/" + idUser;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    if (res.icono == "success") {
                        tblUsuario.ajax.reload();
                    }
                    Swal.fire("Aviso?", res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
}
function cargarDirecciones() {
    const url = base_url + "usuarios/obtenerDirecciones";
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            const select = document.querySelector("#address_id");
            select.innerHTML = '<option value="">Seleccione Direccion</option>'; // reset
            res.forEach((dir) => {
                const option = document.createElement("option");
                option.value = dir.id;
                option.textContent = dir.full_address;
                select.appendChild(option);
            });
        }
    };
}


function editUser(idUser) {
    const url = base_url + "usuarios/edit/" + idUser;
    const http = new XMLHttpRequest();
    http.open("GET", url, true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            
            // Cargar direcciones primero
            const urlDir = base_url + "usuarios/obtenerDirecciones";
            const httpDir = new XMLHttpRequest();
            httpDir.open("GET", urlDir, true);
            httpDir.send();
            httpDir.onreadystatechange = function () {
                if (httpDir.readyState == 4 && httpDir.status == 200) {
                    const dirRes = JSON.parse(httpDir.responseText);
                    const select = document.querySelector("#address_id");
                    select.innerHTML = '<option value="">Seleccione Direccion</option>'; // limpiar
                    dirRes.forEach((dir) => {
                        const option = document.createElement("option");
                        option.value = dir.id;
                        option.textContent = dir.full_address;
                        // seleccionar si coincide con el usuario
                        if (dir.id == res.address_id) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });

                    // Ahora sí llenar el resto del formulario
                    document.querySelector('#id').value = res.id;
                    document.querySelector('#username').value = res.username;
                    document.querySelector('#nombre').value = res.first_name;
                    document.querySelector('#apellido').value = res.last_name;
                    document.querySelector('#correo').value = res.correo;
                    document.querySelector('#phone').value = res.phone;
                    document.querySelector('#rol').value = res.role_id;
                    document.querySelector('#clave').setAttribute('readonly', 'readonly');
                    btnAccion.textContent = 'Actualizar';
                    titleModal.textContent = "MODIFICAR USUARIO";
                    myModal.show();
                }
            };
        }
    };
}

