<?php include_once 'Views/Template/header-principal.php'; ?>

<style>
    :root {
        --ms-primary: #0f4c81;
        --ms-primary-dark: #0b365b;
        --ms-accent: #2ea44f;
        --ms-soft: #f4f8fb;
        --ms-border: rgba(15, 76, 129, 0.10);
        --ms-text: #374151;
        --ms-muted: #6b7280;
        --ms-dark: #1f2937;
        --ms-shadow: 0 16px 36px rgba(15, 76, 129, 0.08);
        --ms-radius: 20px;
    }

    body {
        background: linear-gradient(180deg, #f8fbfd 0%, #f2f6fa 100%);
        color: var(--ms-text);
    }

    .cert-page {
        padding: 50px 0 70px;
    }

    .cert-hero {
        background: linear-gradient(135deg, rgba(15, 76, 129, .06), rgba(46, 164, 79, .05));
        border: 1px solid var(--ms-border);
        border-radius: 28px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--ms-shadow);
    }

    .cert-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        color: var(--ms-dark);
        margin-bottom: .7rem;
    }

    .cert-subtitle {
        color: var(--ms-muted);
        font-size: 1.02rem;
        max-width: 760px;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .cert-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid var(--ms-border);
        box-shadow: var(--ms-shadow);
        overflow: hidden;
    }

    .cert-card-header {
        padding: 1.3rem 1.5rem;
        border-bottom: 1px solid rgba(15, 76, 129, .08);
        background: linear-gradient(135deg, #ffffff, #f8fbff);
    }

    .cert-card-header h4 {
        margin: 0;
        font-weight: 800;
        color: var(--ms-dark);
    }

    .cert-card-body {
        padding: 1.4rem;
    }

    .cert-status-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 1rem;
    }

    .cert-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eef6fb;
        color: var(--ms-primary);
        border: 1px solid rgba(15, 76, 129, .10);
        border-radius: 999px;
        padding: .55rem .95rem;
        font-size: .92rem;
        font-weight: 700;
    }

    .table-responsive {
        border-radius: 18px;
        overflow: hidden;
    }

    table.dataTable thead th {
        background: #f4f8fb !important;
        color: var(--ms-primary-dark) !important;
        font-weight: 800 !important;
        border-bottom: 1px solid rgba(15, 76, 129, .10) !important;
        white-space: nowrap;
    }

    table.dataTable tbody td {
        vertical-align: middle !important;
        color: var(--ms-text);
    }

    table.dataTable.stripe tbody tr.odd,
    table.dataTable.display tbody tr.odd {
        background-color: #fcfdff !important;
    }

    table.dataTable tbody tr:hover {
        background-color: #f4faff !important;
    }

    .cert-link {
        color: #0d6efd;
        font-weight: 700;
        text-decoration: none;
    }

    .cert-link:hover {
        text-decoration: underline;
    }

    .badge-cert {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #edf7ee;
        color: #1f7a35;
        border: 1px solid rgba(46, 164, 79, .16);
        border-radius: 999px;
        padding: .35rem .7rem;
        font-size: .84rem;
        font-weight: 700;
    }

    .dt-buttons .btn,
    .dataTables_filter input,
    .dataTables_length select {
        border-radius: 12px !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgba(15, 76, 129, .14);
        padding: .45rem .7rem;
        margin-left: .5rem;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid rgba(15, 76, 129, .14);
        padding: .35rem 2rem .35rem .7rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 10px !important;
    }

    .last-update {
        font-size: .92rem;
        color: var(--ms-muted);
    }

    @media (max-width: 768px) {
        .cert-hero {
            padding: 1.4rem;
        }

        .cert-card-body {
            padding: 1rem;
        }
    }
</style>

<div class="container cert-page">
    <!-- Encabezado -->
    <section class="cert-hero">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h1 class="cert-title">Lista de Certificados</h1>
                <p class="cert-subtitle">
                    Consulte los certificados de emisiones registrados en el sistema.
                    En caso de que no se encuentre el certifiado presione el boton de <strong>Recargar</strong>.
                </p>
            </div>

        </div>
    </section>

    <!-- Tabla -->
    <div class="cert-card-body">
        <div class="row g-3 align-items-end mb-3" id="cert-paginacion-info">
            <div class="col-md-4">
                <label for="buscar" class="form-label fw-semibold">Buscar certificado, VIN o fecha</label>
                <input type="text" class="form-control" id="buscar" placeholder="Ej. 2026-03-20 / VIN / certificado">
            </div>

            <div class="col-md-2">
                <label for="certificadosPerPage" class="form-label fw-semibold">Mostrar</label>
                <select id="certificadosPerPage" class="form-select">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="1000">1000</option>
                    <option value="5000">5000</option>
                    <option value="all">Todos</option>
                </select>
            </div>



            <div class="col-md-3 text-md-end">
                <button type="button" id="btnRecargarCertificados" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-1"></i> Recargar
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="tablaCertificados" class="table table-hover align-middle w-100">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Placa</th>
                        <th>VIN</th>
                        <th>Certificado</th>
                        <th>Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llena por AJAX -->
                </tbody>
            </table>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                <div id="certificadosPaginacionInfo" class="last-update"></div>
                <div class="col-md-3">
                    <div class="last-update" id="certificadosResumen">
                        Cargando resultados...
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnPrevCertificados" class="btn btn-outline-secondary btn-sm">
                        Anterior
                    </button>
                    <span id="certificadosPaginaActual" class="fw-semibold">Página 1</span>
                    <button type="button" id="btnNextCertificados" class="btn btn-outline-secondary btn-sm">
                        Siguiente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->

<?php include_once 'Views/Template/footer-principal.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/listarCertificados.js"></script>