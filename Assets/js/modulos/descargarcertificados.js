 
const btnAccion = document.querySelector("#btnAccion"); 
let tblCertificados;


document.addEventListener("DOMContentLoaded", function() {
    tblCertificados = $("#tblCertificados").DataTable({
        ajax: {
            url: base_url + "DescargarCertificados/listar",
            dataSrc: "",
        },
        columns: [
            { data: "cert_number" },
            { data: "vin" },
            { data: "zip_file_path" },
            { data: "accion" },
        ],
        language,
        dom,
        buttons,
    });
 
});

function eliminarCertificado(cert_number) {
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
            const url = base_url + "DescargarCertificados/delete/" + cert_number;
            const http = new XMLHttpRequest();
            http.open("GET", url, true);
            http.send();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    if (res.icono == "success") {
                        tblCertificados.ajax.reload();
                    }
                    Swal.fire("Aviso?", res.msg.toUpperCase(), res.icono);
                }
            }
        }
    });
}
 
