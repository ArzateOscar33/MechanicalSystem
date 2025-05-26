const frmRestablecer = document.querySelector("#formRestablecer");
const password = document.querySelector("#password");
const confirmar = document.querySelector("#confirmar");

document.addEventListener("DOMContentLoaded", function () {
    frmRestablecer.addEventListener("submit", function (e) {
        e.preventDefault();

        if (password.value === "" || confirmar.value === "") {
            alertas("Todos los campos son obligatorios", "warning");
        } else if (password.value !== confirmar.value) {
            alertas("Las contraseñas no coinciden", "error");
        } else {
            let data = new FormData(this);
            const url = base_url + "Recuperar/cambiarPassword";

            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);

            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    try {
                        const res = JSON.parse(this.responseText);
                        alertas(res.msg, res.icono);
                        if (res.icono === "success") {
                            setTimeout(() => {
                                window.location = base_url + "admin";
                            }, 2000);
                        }
                    } catch (error) {
                        alertas("Ocurrió un error inesperado", "error");
                        console.error("Respuesta no válida:", this.responseText);
                    }
                }
            };
        }
    });
});

function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
}
