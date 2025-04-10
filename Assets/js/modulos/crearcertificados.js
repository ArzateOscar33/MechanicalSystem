document.addEventListener("DOMContentLoaded", function() {
    const frm = document.querySelector("#formularioCertificado");
    const generarQRBtn = document.querySelector("#generarQR");

    // Función para generar el código QR
    generarQRBtn.addEventListener("click", function() {
        const vin = document.querySelector("#vin").value;
        const propietario = document.querySelector("#propietario").value;

        if (vin && propietario) {
            const qrData = `VIN: ${vin}, Propietario: ${propietario}`;
            const qrCodeContainer = document.querySelector("#codigoQR");
            qrCodeContainer.innerHTML = '';  // Limpiar el contenedor antes de generar un nuevo QR
            new QRCode(qrCodeContainer, qrData);
        } else {
            alertas("Por favor completa el VIN y el nombre del propietario para generar el código QR.", "warning");
        }
    });

    // Manejo de la presentación del formulario
    frm.addEventListener("submit", function(e) {
        e.preventDefault();

        // Aquí puedes agregar más validaciones si es necesario

        let data = new FormData(this);
        const url = base_url + "CrearCertificado/crear";  // Asegúrate que esta ruta sea la correcta
        const http = new XMLHttpRequest();
        http.open("POST", url, true);
        http.send(data);

        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                alertas(res.msg, res.icono);
            }
        }
    });

});

// Función para mostrar alertas
function alertas(msg, icono) {
    Swal.fire("Aviso", msg.toUpperCase(), icono);
}
