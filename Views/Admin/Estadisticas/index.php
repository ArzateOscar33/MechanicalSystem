<?php include_once 'Views/template/header-admin.php'; ?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#estadisticasGenerales"
            type="button" role="tab" aria-controls="estadisticasGenerales" aria-selected="true">Estadisticas
            Generales</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#estadisticasPorEstado"
            type="button" role="tab" aria-controls="estadisticasPorEstado" aria-selected="false">Estadisticas por Estado
            y Ciudad</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#estadisticasPorFecha"
            type="button" role="tab" aria-controls="estadisticasPorFecha" aria-selected="false">Estadisticas por
            Fecha</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#estadisticasPorInspector"
            type="button" role="tab" aria-controls="estadisticasPorInspector" aria-selected="false">Estadisticas por
            Inspector</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#estadisticasErrores"
            type="button" role="tab" aria-controls="estadisticasErrores" aria-selected="false">Estadisticas de Errores
        </button>
    </li>

</ul>

<!-- contenido navbar tabs -->
<div class="tab-content" id="myTabContent">

    <!-- estadisticas generales -->
    <div class="tab-pane fade show active" id="estadisticasGenerales" role="tabpanel" aria-labelledby="home-tab">
        <div class="card radius-10">
            <div class="card-body">
                <!-- Gráfico por Ciudad -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Ciudad</h6>
                            <div class="chart-container-2 mt-4">
                                <canvas id="ciudadesGrafico"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Gráfico por Estado -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Estado</h6>
                            <div class="chart-container-2 mt-4">
                                <canvas id="estadosGrafico"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico por Inspector General -->
                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin ,si quieres agregar manager || $_SESSION['rol_usuario'] == 2
                ?>
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Inspector</h6>
                            <div class="chart-container-2 mt-4">
                                <canvas id="inspectoresChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- estadisticas por estado y ciudad -->
    <div class="tab-pane fade " id="estadisticasPorEstado" role="tabpanel" aria-labelledby="home-tab">
        <div class="card">
            <div class="card-body">

                <!-- Gráfico por Ciudad Filtrado por Inspector -->
                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin ,si quieres agregar manager || $_SESSION['rol_usuario'] == 2
                ?>
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Ciudad (Filtrado por Inspector)</h6>
                            <div class="mt-2">
                                <label for="filtroInspectorCiudad" class="form-label">Inspector</label>
                                <select id="filtroInspectorCiudad" class="form-select mb-2">
                                    <option value="">Selecciona Inspector</option>
                                </select>
                            </div>
                            <div class="chart-container-2 mt-4">
                                <canvas id="ciudadesInspectorGrafico"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Sucursal</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Desde</label>
                                    <input type="date" id="fechaDesdeSucursales" class="form-control mb-2"
                                        max="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" id="fechaHastaSucursales" class="form-control mb-2"
                                        max="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <canvas id="graficoSucursales" width="600" height="100px"></canvas>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- estadisticas por Fecha -->
    <div class="tab-pane fade " id="estadisticasPorFecha" role="tabpanel" aria-labelledby="home-tab">
        <div class="card">
            <div class="card-body">
                <!-- Gráfico Mensual por Fecha -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Fecha (Mensual)</h6>
                            <div class="chart-container-2 mt-4">
                                <canvas id="fechaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Gráfico por Día con Filtros -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Día (filtrados)</h6>
                            <div class="mt-3">
                                <label for="filtroMes" class="form-label">Mes</label>
                                <input type="month" id="filtroMes" class="form-control mb-2">
                                <label for="filtroEstado" class="form-label">Estado</label>
                                <select id="filtroEstado" class="form-select mb-2" onchange="bloquearCiudad()">
                                    <option value="">Selecciona Estado</option>
                                </select>
                                <label for="filtroCiudad" class="form-label">Ciudad</label>
                                <select id="filtroCiudad" class="form-select mb-2" onchange="bloquearEstado()">
                                    <option value="">Selecciona Ciudad</option>
                                </select>
                            </div>
                            <div class="chart-container-2 mt-4">
                                <canvas id="certificadosDiaChart" height="200"></canvas>
                            </div>
                            <button class="btn btn-warning mt-3" id="generarGraficosEspeciales">Generar Informe de
                                Gráficos Especiales</button>
                        </div>

                    </div>

                </div>
                <!-- Tabla Estadisticas por semana -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Ciudad Semanal</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Desde</label>
                                    <input type="date" id="filtroDesdeRangoTabla" class="form-control mb-2"
                                        max="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" id="filtroHastaRangoTabla" class="form-control mb-2"
                                        max="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <table class="table table-bordered table-striped table-hover" style="width: 100%;"
                                id="tblEstadisticasSemanal">
                                <thead>
                                    <tr>
                                        <th>Dia de la Semana</th>
                                        <th>Calexico</th>
                                        <th>El Paso</th>
                                        <th>National City</th>
                                        <th>Nogales</th>
                                        <th>San Diego</th>
                                        <th>Tijuana</th>
                                        <th>Total por Dia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Lunes</td>
                                        <td id="lunesCalexico"></td>
                                        <td id="lunesElPaso"></td>
                                        <td id="lunesNationalCity"></td>
                                        <td id="lunesNogales"></td>
                                        <td id="lunesSanDiego"></td>
                                        <td id="lunesTijuana"></td>
                                        <td id="totalLunes"></td>
                                    </tr>
                                    <tr>
                                        <td>Martes</td>
                                        <td id="martesCalexico"></td>
                                        <td id="martesElPaso"></td>
                                        <td id="martesNationalCity"></td>
                                        <td id="martesNogales"></td>
                                        <td id="martesSanDiego"></td>
                                        <td id="martesTijuana"></td>
                                        <td id="totalMartes"></td>
                                    </tr>
                                    <tr>
                                        <td>Miércoles</td>
                                        <td id="miercolesCalexico"></td>
                                        <td id="miercolesElPaso"></td>
                                        <td id="miercolesNationalCity"></td>
                                        <td id="miercolesNogales"></td>
                                        <td id="miercolesSanDiego"></td>
                                        <td id="miercolesTijuana"></td>
                                        <td id="totalMiercoles"></td>
                                    </tr>
                                    <tr>
                                        <td>Jueves</td>
                                        <td id="juevesCalexico"></td>
                                        <td id="juevesElPaso"></td>
                                        <td id="juevesNationalCity"></td>
                                        <td id="juevesNogales"></td>
                                        <td id="juevesSanDiego"></td>
                                        <td id="juevesTijuana"></td>
                                        <td id="totalJueves"></td>
                                    </tr>
                                    <tr>
                                        <td>Viernes</td>
                                        <td id="viernesCalexico"></td>
                                        <td id="viernesElPaso"></td>
                                        <td id="viernesNationalCity"></td>
                                        <td id="viernesNogales"></td>
                                        <td id="viernesSanDiego"></td>
                                        <td id="viernesTijuana"></td>
                                        <td id="totalViernes"></td>
                                    </tr>
                                    <tr>
                                        <td>Sabado</td>
                                        <td id="sabadoCalexico"></td>
                                        <td id="sabadoElPaso"></td>
                                        <td id="sabadoNationalCity"></td>
                                        <td id="sabadoNogales"></td>
                                        <td id="sabadoSanDiego"></td>
                                        <td id="sabadoTijuana"></td>
                                        <td id="totalSabado"></td>
                                    </tr>
                                    <tr>
                                        <td>Domingo</td>
                                        <td id="domingoCalexico"></td>
                                        <td id="domingoElPaso"></td>
                                        <td id="domingoNationalCity"></td>
                                        <td id="domingoNogales"></td>
                                        <td id="domingoSanDiego"></td>
                                        <td id="domingoTijuana"></td>
                                        <td id="totalDomingo"></td>
                                    <tr>
                                        <td><strong>Total Por Ciudad</strong></td>
                                        <td id="totalCalexico"></td>
                                        <td id="totalElPaso"></td>
                                        <td id="totalNationalCity"></td>
                                        <td id="totalNogales"></td>
                                        <td id="totalSanDiego"></td>
                                        <td id="totalTijuana"></td>
                                        <td id="totalGeneral"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button class="btn btn-warning mt-3" id="imprimirTabla">Imprimir Tabla Semanal</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- estadisticas por Inspector -->
    <div class="tab-pane fade " id="estadisticasPorInspector" role="tabpanel" aria-labelledby="home-tab">
        <div class="card">
            <div class="card-body">

                <!-- Gráfico por Inspector con Filtros -->
                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin ,si quieres agregar manager || $_SESSION['rol_usuario'] == 2
                ?>
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Inspector (filtrados)</h6>
                            <div class="mt-3">
                                <label for="filtroAnioInspector" class="form-label">Año</label>
                                <select id="filtroAnioInspector" class="form-select mb-2"></select>
                                <label for="filtroInspector" class="form-label">Inspector</label>
                                <select id="filtroInspector" class="form-select mb-2">
                                    <option value="">Selecciona Inspector</option>
                                </select>
                                <p id="origenInspector" class="mt-2 text-muted small fst-italic">Origen: <span
                                        id="origenTexto">--</span></p>

                            </div>
                            <div class="chart-container-2 mt-4">
                                <canvas id="certificadosInspectorChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="col mt-4">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Inspector (por día)</h6>
                            <div class="mt-3">
                                <label for="filtroFechaDia" class="form-label">Selecciona una fecha</label>
                                <input type="date" id="filtroFechaDia" class="form-control mb-3"
                                    max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="chart-container-2 mt-4">
                                <canvas id="graficoInspectorPorFecha" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mt-4">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Distribución por Ciudad (Inspector y Fecha)</h6>
                            <div class="mt-3">
                                <label class="form-label">Inspector</label>
                                <select id="filtroInspectorFechaCiudad" class="form-select mb-2">
                                    <option value="">Selecciona Inspector</option>
                                </select>

                                <label class="form-label">Fecha</label>
                                <input type="date" id="filtroFechaCiudad" class="form-control mb-3"
                                    max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="chart-container-2 mt-4">
                                <canvas id="graficoCiudadInspectorFecha" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col mt-4">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Distribución por Ciudad (Por Rango de Fechas)</h6>
                            <div class="mt-3">
                                <label class="form-label">Inspector</label>
                                <select id="filtroInspectorRango" class="form-select mb-2">
                                    <option value="">Selecciona Inspector</option>
                                </select>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Desde</label>
                                        <input type="date" id="filtroDesdeRango" class="form-control mb-2"
                                            max="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" id="filtroHastaRango" class="form-control mb-2"
                                            max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="chart-container-2 mt-4">
                                <canvas id="graficoCiudadInspectorRango" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 mb-3 d-flex justify-content-between">
                    <button id="btnGenerarPdfInspectores" class="btn btn-primary ">
                        Generar PDF con gráficos por Inspector
                    </button>

                    <button class="btn btn-success " onclick="generarPdfInspectoresPorRango()">
                        Generar PDF por Rango de Fechas
                    </button>
                </div>
                <!-- contenedor invisible para los gráficos -->
                <div id="contenedorGraficosInspectores" style="display:none;"></div>
            </div>
        </div>
    </div>

    <!-- estadisticas de Errores -->
    <div class="tab-pane fade" id="estadisticasErrores" role="tabpanel" aria-labelledby="home-tab">
        <div class="card radius-10 mb-5">
            <div class="card-body">
                <!-- Tabla de vin duplicados  -->
                <!-- Contenido de la tabla -->
                <div class="card">
                    <div class="card-body">
                        <!-- Contenido de la tabla errores pendientes -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" style="width: 100%;"
                                id="tblDuplicados" name="tblDuplicados">
                                <thead>
                                    <tr>
                                        <th>Certificado</th>
                                        <th>VIN Duplicado</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Año</th>
                                        <th>Propietario</th>
                                        <th>id Direccion</th>
                                        <th>Inspector que expidio el duplicado</th>
                                        <th>Ciudad</th>
                                        <th>Estado</th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Gráfico por Estado -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados por Estado</h6>
                            <div class="chart-container-2 mt-4">
                                <canvas id="estadosGraficoDuplicados"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Contenido de la tabla -->
                <div class="card">
                    <div class="card-body">
                        <!-- Contenido de la tabla errores pendientes -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" style="width: 100%;"
                                id="tblDuplicadosCantidadDeIncidencias">
                                <thead>
                                    <tr>

                                        <th>Inspector</th>
                                        <th>Cantidad de veces duplicado</th>

                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Errores por ciudad  -->
                <div class="col">
                    <div class="card radius-10">
                        <div class="card-body">
                            <h6 class="mb-0">Certificados Duplicados por Ciudad</h6>
                            <div class="chart-container-2 mt-4">
                                <canvas id="ciudadesDuplicadosGrafico"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Gráfico de Duplicados por Ciudad (Mensual) -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body mb-5">
                            <h6 class="mb-0">Duplicados por Ciudad (Mensual)</h6>
                            <div class="chart-container-2 mt-4 mb-5 ">
                                <canvas id="duplicadosCiudadMensual" height="55" class="mb-5 w-100 h-80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="<?php echo BASE_URL; ?>assets/js/modulos/estadisticas.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/modulos/graficosEstadisticosDuplicados.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/modulos/tablasDuplicados.js"></script>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/generarInformesGraficos.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/modulos/tablaCertificadosSemanal.js"></script>