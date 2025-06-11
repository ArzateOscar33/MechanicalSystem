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
</ul>

<!-- contenido navbar tabs -->
<div class="tab-content" id="myTabContent">

    <!-- estadisticas generales -->
    <div class="tab-pane fade show active" id="estadisticasGenerales" role="tabpanel" aria-labelledby="home-tab">
        <div class="card">
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
                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin ,si quieres agregar manager || $_SESSION['rol_usuario'] == 2
                ?>
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
                        </div>
                    </div>
                </div>
                <?php endif; ?>


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
    </div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/modulos/estadisticas.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>