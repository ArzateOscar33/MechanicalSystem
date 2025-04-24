<?php include_once 'Views/template/header-admin.php'; ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-warning">
            <div class="card-body d-flex align-items-center">
                <div>
                    <p class="mb-0 text-secondary">Certificados</p>
                    <h4 class="my-1 text-warning"><?php echo $data['certificadosTotales']['total']; ?></h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                    <i class='fas fa-file-contract'></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-info">
            <div class="card-body d-flex align-items-center">
                <div>
                    <p class="mb-0 text-secondary">Estados Registrados</p>
                    <h4 class="my-1 text-info"><?php echo $data['estadosTotales']['total']; ?></h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                    <i class='fas fa-flag-usa'></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-success">
            <div class="card-body d-flex align-items-center">
                <div>
                    <p class="mb-0 text-secondary">Ciudades Registradas</p>
                    <h4 class="my-1 text-success"><?php echo $data['ciudadesTotales']['total']; ?></h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                    <i class='fas fa-city'></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-warning">
            <div class="card-body d-flex align-items-center">
                <div>
                    <p class="mb-0 text-secondary">Inspectores Registrados</p>
                    <h4 class="my-1 text-warning"><?php echo $data['inspector_name']['total']; ?></h4>
                </div>
                <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto">
                    <i class="fas fa-id-card-alt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GRÁFICOS EN GRUPOS DE 3 -->
<div class="row row-cols-1 row-cols-lg-3 g-4">

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

    <!-- Gráfico por Inspector General -->
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
            </div>
        </div>
    </div>

    <!-- Gráfico por Inspector con Filtros -->
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
                </div>
                <div class="chart-container-2 mt-4">
                    <canvas id="certificadosInspectorChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/index.js"></script>
