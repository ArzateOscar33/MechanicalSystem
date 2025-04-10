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