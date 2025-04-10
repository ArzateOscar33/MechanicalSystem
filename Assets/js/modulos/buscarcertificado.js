// script.js
function filtrarTabla() {
    // Obtener los valores de los filtros
    let filtroCertNumber = document.getElementById('filtro-cert-number').value.toLowerCase();
    let filtroCity = document.getElementById('filtro-city').value.toLowerCase();
    let filtroState = document.getElementById('filtro-state').value.toLowerCase();
    let filtroMake = document.getElementById('filtro-make').value.toLowerCase();
    let filtroOwnerName = document.getElementById('filtro-owner-name').value.toLowerCase();
    let filtroMfgIn = document.getElementById('filtro-mfg-in').value.toLowerCase();

    // Obtener todas las filas de la tabla
    let filas = document.getElementById('tblCertificados').getElementsByTagName('tr');

    // Recorrer las filas de la tabla (empezando desde la fila 1 para omitir el encabezado)
    for (let i = 1; i < filas.length; i++) {
        let fila = filas[i];
        let celdas = fila.getElementsByTagName('td');

        // Obtener los valores de las celdas en cada fila
        let certNumber = celdas[0].textContent.toLowerCase();
        let city = celdas[1].textContent.toLowerCase();
        let state = celdas[2].textContent.toLowerCase();
        let make = celdas[3].textContent.toLowerCase();
        let ownerName = celdas[4].textContent.toLowerCase();
        let mfgIn = celdas[5].textContent.toLowerCase();

        // Verificar si la fila cumple con todos los filtros
        if (
            certNumber.includes(filtroCertNumber) &&
            city.includes(filtroCity) &&
            state.includes(filtroState) &&
            make.includes(filtroMake) &&
            ownerName.includes(filtroOwnerName) &&
            mfgIn.includes(filtroMfgIn)
        ) {
            fila.style.display = ""; // Mostrar fila
        } else {
            fila.style.display = "none"; // Ocultar fila
        }
    }
}
