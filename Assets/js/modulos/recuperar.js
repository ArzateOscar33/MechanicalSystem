const frmRecuperar = document.querySelector("#formRecuperar");
const correo = document.querySelector("#correo");

document.addEventListener("DOMContentLoaded", function () {
    frmRecuperar.addEventListener("submit", function (e) {
        e.preventDefault();

        if (correo.value === "") {
            alertas("El correo es obligatorio", "warning");
        } else {
            let data = new FormData(this);
            const url = base_url + "Recuperar/solicitarToken";

            const http = new XMLHttpRequest();
            http.open("POST", url, true);
            http.send(data);

            http.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    try {
                        const res = JSON.parse(this.responseText);
                        alertas(res.msg, res.icono);

                        if (res.icono === "success") {
                            frmRecuperar.reset();
                        }
                    } catch (error) {
                        alertas("Error inesperado. Intenta de nuevo.", "error");
                        console.error("Error de respuesta JSON:", this.responseText);
                    }
                }
            };
        }
    });
});

function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
}
