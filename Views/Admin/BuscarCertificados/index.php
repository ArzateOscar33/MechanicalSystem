

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
        <div class="card-header">
            <h5>Filtrar Certificados</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Filtro Cert Number -->
                <div class="col-md-2">
                    <input type="text" id="filtro-cert-number" class="form-control" onkeyup="filtrarTabla()" placeholder="Filtrar por Cert Number">
                </div>
                <!-- Filtro City -->
                <div class="col-md-2">
                    <input type="text" id="filtro-city" class="form-control" onkeyup="filtrarTabla()" placeholder="Filtrar por City">
                </div>
                <!-- Filtro State -->
                <div class="col-md-2">
                    <input type="text" id="filtro-state" class="form-control" onkeyup="filtrarTabla()" placeholder="Filtrar por State">
                </div>
                <!-- Filtro Make -->
                <div class="col-md-2">
                    <input type="text" id="filtro-make" class="form-control" onkeyup="filtrarTabla()" placeholder="Filtrar por Make">
                </div>
                <!-- Filtro Owner's Name -->
                <div class="col-md-2">
                    <input type="text" id="filtro-owner-name" class="form-control" onkeyup="filtrarTabla()" placeholder="Filtrar por Owner's Name">
                </div>
                <!-- Filtro Mfg In -->
                <div class="col-md-2">
                    <input type="text" id="filtro-mfg-in" class="form-control" onkeyup="filtrarTabla()" placeholder="Filtrar por Mfg In">
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Certificados -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblCertificados">
                    <thead>
                        <tr>
                            <th>Cert Number</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Make</th>
                            <th>Owner's Name</th>
                            <th>Mfg In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí deberías cargar dinámicamente los datos de los certificados -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Incluir el JS de Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
        const base_url = '<?php echo BASE_URL; ?>';
    </script>
<script src="<?php echo BASE_URL; ?>assets/js/buscarcertificados.js"></script>
</body>
</html>

<?php include_once 'Views/template/footer-admin.php'; ?>

