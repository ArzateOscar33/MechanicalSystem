const nuevo = document.querySelector("#nuevo_registro");
const frm = document.querySelector("#frmRegistro");
const titleModal = document.querySelector("#titleModal");
const btnAccion = document.querySelector("#btnAccion");
const myModal = new bootstrap.Modal(document.getElementById("nuevoModal"));
let tblClientes;

document.addEventListener("DOMContentLoaded", function () {
  tblClientes = $("#tblClientes").DataTable({
    ajax: {
      url: base_url + "clientes/listar",
      dataSrc: "",
    },
    columns: [
      { data: "id", title: "#" },
      { data: "nombre_cliente", title: "Nombre Cliente" },
      {
        data: "accion",
        title: "Acciones",
        orderable: false,
        searchable: false,
      },
    ],
    language,
    dom,
    buttons,
  });

  nuevo.addEventListener("click", function () {
    frm.reset();
    document.querySelector("#id").value = "";
    titleModal.textContent = "NUEVO CLIENTE";
    btnAccion.textContent = "Registrar";
    myModal.show();
  });

  frm.addEventListener("submit", function (e) {
    e.preventDefault();

    const nombreCliente = document
      .querySelector("#nombre_cliente")
      .value.trim();

    if (nombreCliente === "") {
      Swal.fire("Aviso", "EL NOMBRE DEL CLIENTE ES REQUERIDO", "warning");
      return;
    }

    const data = new FormData(frm);
    const url = base_url + "clientes/registrar";
    const http = new XMLHttpRequest();

    http.open("POST", url, true);
    http.send(data);

    http.onreadystatechange = function () {
      if (this.readyState === 4) {
        if (this.status === 200) {
          let res;

          try {
            res = JSON.parse(this.responseText);
          } catch (error) {
            Swal.fire("Error", "RESPUESTA INVÁLIDA DEL SERVIDOR", "error");
            return;
          }

          if (res.icono === "success") {
            myModal.hide();
            tblClientes.ajax.reload(null, false);
          }

          Swal.fire(
            "Aviso",
            (res.msg || "OPERACIÓN REALIZADA").toUpperCase(),
            res.icono || "info",
          );
        } else {
          Swal.fire("Error", "NO SE PUDO PROCESAR LA SOLICITUD", "error");
        }
      }
    };
  });
});

function eliminarCliente(idCliente) {
  Swal.fire({
    title: "Aviso",
    text: "¿ESTÁS SEGURO DE ELIMINAR EL CLIENTE?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "clientes/delete/" + idCliente;
      const http = new XMLHttpRequest();

      http.open("GET", url, true);
      http.send();

      http.onreadystatechange = function () {
        if (this.readyState === 4) {
          if (this.status === 200) {
            let res;

            try {
              res = JSON.parse(this.responseText);
            } catch (error) {
              Swal.fire("Error", "RESPUESTA INVÁLIDA DEL SERVIDOR", "error");
              return;
            }

            if (res.icono === "success") {
              tblClientes.ajax.reload(null, false);
            }

            Swal.fire(
              "Aviso",
              (res.msg || "OPERACIÓN REALIZADA").toUpperCase(),
              res.icono || "info",
            );
          } else {
            Swal.fire("Error", "NO SE PUDO ELIMINAR EL CLIENTE", "error");
          }
        }
      };
    }
  });
}

function editCliente(idCliente) {
  const url = base_url + "clientes/edit/" + idCliente;
  const http = new XMLHttpRequest();

  http.open("GET", url, true);
  http.send();

  http.onreadystatechange = function () {
    if (this.readyState === 4) {
      if (this.status === 200) {
        let res;

        try {
          res = JSON.parse(this.responseText);
        } catch (error) {
          Swal.fire("Error", "RESPUESTA INVÁLIDA DEL SERVIDOR", "error");
          return;
        }

        document.querySelector("#id").value = res.id || "";
        document.querySelector("#nombre_cliente").value =
          res.nombre_cliente || "";

        titleModal.textContent = "MODIFICAR CLIENTE";
        btnAccion.textContent = "Actualizar";
        myModal.show();
      } else {
        Swal.fire("Error", "NO SE PUDO OBTENER EL CLIENTE", "error");
      }
    }
  };
}
