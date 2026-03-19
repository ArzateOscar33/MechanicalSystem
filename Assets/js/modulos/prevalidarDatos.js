//Botones para prevalidar datos y fotografías antes de crear el certificado
const btnPrevalidar = document.getElementById("btnPrevalidador");
const btnPrevalidarFotos = document.getElementById("btnPrevalidadorFotos");
const btnCrearCertificado = document.getElementById("btnCrearCertificado");

//Inputs del formulario
const frm = document.querySelector("#formularioCertificado");
const generarQRBtn = document.querySelector("#generarQR");
const qrCodeContainer = document.querySelector("#codigoQR");
const selectDireccion = document.getElementById("direccion_existente");
const camposNuevaDireccion = document.getElementById("nuevaDireccionCampos");
const telefonoInput = document.querySelector('input[name="telefono"]');
const fechaInput = document.getElementById("fecha");
const expiracionInput = document.getElementById("fecha_expiracion");
const firmaPreview = document.getElementById("firmaPreview");
const imagenFirma = document.getElementById("imagenFirma");
const inspectorSelect = document.getElementById("inspector");
const nuevoInspectorCheck = document.getElementById("nuevo_inspector_check");
const nuevoInspectorInput = document.getElementById("nuevo_inspector");
const yearInput = document.getElementById("year");
const numeroCertInput = document.getElementById("numero_certificado");
const vinInput = document.getElementById("vin");
const ebitnInput = document.getElementById("ebitn");

btnPrevalidar.addEventListener("click", () => {
  btnPrevalidarFotos.classList.remove("disabled");
  alertas("Se activo el boton de prevalidar datos", "success");

  //desactivar el boton de prevalidar datos para evitar múltiples clics
  btnPrevalidar.classList.add("disabled");
  //desactivar inputs para evitar cambios después de la prevalidación
  frm.querySelectorAll("input, select").forEach((input) => {
    input.setAttribute("disabled", "disabled");
  });
});

btnPrevalidarFotos.addEventListener("click", () => {
  // Aquí puedes agregar la lógica para prevalidar las fotografías
  // Por ejemplo, puedes verificar que se hayan subido las imágenes requeridas
  // o que tengan el formato y tamaño adecuados
  alertas("Se activo el boton de crear certificado", "success");
  btnCrearCertificado.classList.remove("disabled");

  //desactivar el boton de prevalidar fotos para evitar múltiples clics
  btnPrevalidarFotos.classList.add("disabled");

  //desactivar inputs de fotografías para evitar cambios después de la prevalidación
  const fotoInputs = frm.querySelectorAll('input[type="file"]');
  fotoInputs.forEach((input) => {
    input.setAttribute("disabled", "disabled");
  });
});
