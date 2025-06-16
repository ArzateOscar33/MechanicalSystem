function aplicarFiltro() {
    const fecha = document.getElementById("filtroMes").value;
    const estado = document.getElementById("filtroEstado").value;
    const ciudad = document.getElementById("filtroCiudad").value;

    if (fecha) {
        const [anio, mes] = fecha.split("-");
        certificadosPorDia(anio, mes, estado, ciudad);
    }
}
function configurarFiltroMes() {
    const filtroMes = document.getElementById("filtroMes");
    if (!filtroMes) return;

    const fechaHoy = new Date();
    const anio = fechaHoy.getFullYear();
    const mes = String(fechaHoy.getMonth() + 1).padStart(2, '0');
    const hoy = `${anio}-${mes}`;

    filtroMes.min = "2024-12";
    filtroMes.max = hoy;
    filtroMes.value = hoy;
}


