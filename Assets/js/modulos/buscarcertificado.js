// buscarcertificado.js
document.addEventListener('DOMContentLoaded', function() {
    // Asignar evento al botón de aplicar filtros
    document.getElementById('btn-aplicar-filtros').addEventListener('click', filtrarTabla);
    
    // Asignar evento al botón de limpiar filtros
    document.getElementById('btn-limpiar-filtros').addEventListener('click', limpiarFiltros);
    
    // Opcional: Permitir filtrar al presionar Enter en cualquier campo de filtro
    const filtros = document.querySelectorAll('input[id^="filtro-"]');
    filtros.forEach(filtro => {
        filtro.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                filtrarTabla();
            }
        });
    });
});

function filtrarTabla() {
    // Obtener los valores de los filtros
    const filtroCertNumber = document.getElementById('filtro-cert-number').value.toLowerCase();
    const filtroCity = document.getElementById('filtro-city').value.toLowerCase();
    const filtroState = document.getElementById('filtro-state').value.toLowerCase();
    const filtroMake = document.getElementById('filtro-make').value.toLowerCase();
    const filtroOwnerName = document.getElementById('filtro-owner-name').value.toLowerCase();
    const filtroInspectorName = document.getElementById('filtro-inspector-name').value.toLowerCase();

    // Obtener todas las filas de la tabla
    const tabla = document.getElementById('tblCertificados');
    const tbody = tabla.querySelector('tbody');
    if (!tbody) return;
    
    const filas = tbody.querySelectorAll('tr');

    // Contador para filas visibles
    let filasVisibles = 0;

    // Recorrer las filas de la tabla
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length < 8) return; // Asegurarse de que la fila tiene todas las celdas necesarias

        // Obtener los valores de las celdas en cada fila
        const certNumber = celdas[0].textContent.toLowerCase();
        const make = celdas[1].textContent.toLowerCase();
        const ownerName = celdas[2].textContent.toLowerCase();
        const inspectorName = celdas[3].textContent.toLowerCase();
        const city = celdas[4].textContent.toLowerCase();
        const state = celdas[5].textContent.toLowerCase();
        const mfgIn = celdas[7].textContent.toLowerCase();

        // Para Inspector Name, extraemos la parte del nombre ignorando los dígitos iniciales
        const inspectorNameParts = inspectorName.match(/^\d+\s*(.*)$/);
        const inspectorNameClean = inspectorNameParts ? inspectorNameParts[1].trim() : inspectorName;

        // Verificar si la fila cumple con todos los filtros
        const mostrar = 
            certNumber.includes(filtroCertNumber) &&
            city.includes(filtroCity) &&
            state.includes(filtroState) &&
            make.includes(filtroMake) &&
            ownerName.includes(filtroOwnerName) &&
            (inspectorName.includes(filtroInspectorName) || inspectorNameClean.includes(filtroInspectorName));

        // Mostrar u ocultar la fila
        fila.style.display = mostrar ? "" : "none";
        
        // Incrementar contador si la fila es visible
        if (mostrar) filasVisibles++;
    });

    // Mostrar mensaje si no hay resultados
    const mensajeNoResultados = document.getElementById('no-resultados');
    if (mensajeNoResultados) {
        mensajeNoResultados.style.display = filasVisibles === 0 ? "block" : "none";
    }
}

function limpiarFiltros() {
    // Limpiar todos los campos de filtro
    document.querySelectorAll('input[id^="filtro-"]').forEach(input => {
        input.value = '';
    });
    
    // Volver a filtrar para mostrar todas las filas
    filtrarTabla();
}