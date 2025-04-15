<?php include_once 'Views/template/header-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busqueda de Certificados</title>
    <!-- Incluir los enlaces de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <!-- Card para los filtros -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filtrar Certificados</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Filtro Cert Number -->
                <div class="col-md-2">
                    <label for="filtro-cert-number" class="form-label">Cert Number</label>
                    <input type="text" id="filtro-cert-number" class="form-control" placeholder="Buscar...">
                </div>
                <!-- Filtro City -->
                <div class="col-md-2">
                    <label for="filtro-city" class="form-label">City</label>
                    <input type="text" id="filtro-city" class="form-control" placeholder="Buscar...">
                </div>
                <!-- Filtro State -->
                <div class="col-md-2">
                    <label for="filtro-state" class="form-label">State</label>
                    <input type="text" id="filtro-state" class="form-control" placeholder="Buscar...">
                </div>
                <!-- Filtro Make -->
                <div class="col-md-2">
                    <label for="filtro-make" class="form-label">Make</label>
                    <input type="text" id="filtro-make" class="form-control" placeholder="Buscar...">
                </div>
                <!-- Filtro Owner's Name -->
                <div class="col-md-2">
                    <label for="filtro-owner-name" class="form-label">Owner's Name</label>
                    <input type="text" id="filtro-owner-name" class="form-control" placeholder="Buscar...">
                </div>
                <!-- Filtro Inspector Name -->
                <div class="col-md-2">
                    <label for="filtro-inspector-name" class="form-label">Inspector</label>
                    <input type="text" id="filtro-inspector-name" class="form-control" placeholder="Buscar...">
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="button" id="btn-aplicar-filtros" class="btn btn-primary">
                    <i class="fas fa-search"></i> Aplicar Filtros
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-filtros">
                    <i class="fas fa-eraser"></i> Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de Certificados -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Listado de Certificados</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info" id="no-resultados" style="display: none;">
                No se encontraron certificados con los criterios de búsqueda.
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblCertificados">
                    <thead class="table-dark">
                        <tr>
                            <th>Cert Number</th>
                            <th>Make</th>
                            <th>Owner's Name</th>
                            <th>Inspector Name</th>
                            <th>City</th>
                            <th>State</th>
                            <th>ZIP</th>
                            <th>Mfg In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['certificados'])) { ?>
                            <?php foreach ($data['certificados'] as $certificado) { ?>
                            <tr>
                                <td><?php echo $certificado['cert_number']; ?></td>
                                <td><?php echo $certificado['make']; ?></td>
                                <td><?php echo $certificado['owner_name']; ?></td>
                                <td><?php echo $certificado['inspector_name']; ?></td>
                                <td><?php echo $certificado['city']; ?></td>
                                <td><?php echo $certificado['state']; ?></td>
                                <td><?php echo $certificado['zip']; ?></td>
                                <td><?php echo $certificado['mfg_in']; ?></td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay certificados disponibles</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el JS de Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
 
<script src="<?php echo BASE_URL; ?>assets/js/modulos/buscarcertificado.js"></script>
</body>
</html>

<?php include_once 'Views/template/footer-admin.php'; ?>