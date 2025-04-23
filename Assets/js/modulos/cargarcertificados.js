// Selección de elementos del DOM
const frm = document.querySelector("#formulario");
const fileUpload = document.querySelector("#fileUpload");

// Evento que se ejecuta cuando el DOM ha cargado completamente
document.addEventListener("DOMContentLoaded", function() {
    // Agregar evento submit al formulario
    frm.addEventListener("submit", function(e) {
        // Prevenir el comportamiento predeterminado del formulario
        e.preventDefault();

        // Obtener el archivo seleccionado
        const file = fileUpload.files[0];
        
        // Verificar si se ha seleccionado un archivo
        if (!file) {
            alertas("Por favor selecciona un archivo JSON.", "warning");
            return;
        }

        // Verificar si el archivo tiene extensión JSON
        if (file.type !== "application/json") {
            alertas("El archivo debe ser un JSON.", "error");
            return;
        }

        // Crear objeto FormData para enviar el archivo
        let data = new FormData();
        data.append("fileUpload", file);

        // Definir la URL del endpoint
        const url = base_url + "CargarCertificados/cargar";
        
        // Crear y configurar objeto XMLHttpRequest
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        
        // Mostrar indicador de carga (opcional)
        const btnSubmit = frm.querySelector("button[type='submit']");
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        }
        
        // Enviar los datos
        http.send(data);
        
        // Controlar los cambios de estado de la petición
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                // Parsear la respuesta JSON
                const res = JSON.parse(this.responseText);
               
                
                // Mostrar mensaje de resultado
                alertas(res.msg, res.icono);
                
                // Si fue exitoso, limpiar el formulario
                if (res.icono == 'success') {
                    frm.reset();
                }
                
                // Restaurar el botón de envío (opcional)
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Importar Certificados';
                }
            }
        }
    });
});

// Función para mostrar alertas utilizando SweetAlert2
function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
}

document.addEventListener("DOMContentLoaded", function () {
    const frmZip = document.querySelector("#formularioZIP");
    const fileZip = document.querySelector("#fileUploadZIP");

    frmZip.addEventListener("submit", function (e) {
        e.preventDefault();
        const files = fileZip.files;

        if (!files.length) {
            alertas("Por favor selecciona uno o más archivos ZIP.", "warning");
            return;
        }

        let total = files.length;
        let subidos = 0;

        Swal.fire({
            title: 'Subiendo archivos...',
            html: '<div id="swal-progress" style="width:100%;background:#ddd;height:20px;"><div id="swal-bar" style="width:0%;height:100%;background:#4caf50;"></div></div><p id="swal-status">0 de ' + total + ' completados</p>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                subirArchivo(0);
            }
        });

        function subirArchivo(index) {
            if (index >= total) {
                Swal.update({
                    html: '<p>Todos los archivos fueron subidos.</p>',
                    icon: 'success',
                    showConfirmButton: true
                });
                return;
            }

            const formData = new FormData();
            formData.append("fileUpload", files[index]);

            const xhr = new XMLHttpRequest();
            xhr.open("POST", base_url + "CargarCertificados/subirZIP", true);

            xhr.onload = function () {
                const res = JSON.parse(xhr.responseText);
                if (res.icono === "success") {
                    subidos++;
                }

                let porcentaje = Math.round((subidos / total) * 100);
                document.getElementById("swal-bar").style.width = porcentaje + "%";
                document.getElementById("swal-status").textContent = subidos + " de " + total + " completados";

                subirArchivo(index + 1);
            };

            xhr.send(formData);
        }
    });
});

