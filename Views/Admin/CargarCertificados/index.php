<?php include_once 'Views/template/header-admin.php'; ?> 
<?php echo $_SESSION['id_usuario'];?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <!-- Incluye tus estilos CSS aquí -->
</head>

<body>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#cargarJSON" type="button" role="tab" aria-controls="cargarJSON" aria-selected="true">Cargar Desde JSON</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#cargarZIP" type="button" role="tab" aria-controls="cargarZIP" aria-selected="false">Cargar ZIP</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="cargarJSON" role="tabpanel" aria-labelledby="home-tab">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center">Cargar Certificados</h1>
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5>Seleccionar Archivo JSON</h5>
                                </div>
                                <div class="card-body">
                                    <form id="formulario" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="fileUpload">Archivo JSON</label>
                                            <input type="file" class="form-control" id="fileUpload" accept=".json">
                                            <small class="form-text text-muted">Selecciona un archivo JSON con los certificados a importar.</small>
                                        </div>
                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary">Importar Certificados</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header bg-info text-white">
                                    <h5>Instrucciones</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>El archivo debe estar en formato ZIP.</li>
                                        <li>Cada certificado debe tener un número único (cert_number).</li>
                                        <li>Los archivos ZIP asociados deben ser subidos por separado.</li>
                                        <li>El sistema verificará certificados duplicados y los actualizará.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="cargarZIP" role="tabpanel" aria-labelledby="profile-tab">
        <div class="card">
                <div class="card-body">
                    <h1 class="text-center">Cargar Archivos ZIP</h1>
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5>Seleccionar Archivo ZIP</h5>
                                </div>
                                <div class="card-body">
                                    <form id="formulario" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="fileUpload">Archivo ZI  P</label>
                                            <input type="file" class="form-control" id="fileUpload" accept=".zip">
                                            <small class="form-text text-muted">Selecciona un archivo ZIP con los certificados a importar.</small>
                                        </div>
                                        <div class="form-group mt-3">
                                            <button type="submit" class="btn btn-primary">Importar Certificados</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header bg-info text-white">
                                    <h5>Instrucciones</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>El archivo debe estar en formato ZIP.</li>
                                        <li>Cada certificado debe tener un número único (cert_number).</li> 
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <!-- Incluye tus scripts JS aquí -->
</body>

</html>


<?php include_once 'Views/template/footer-admin.php'; ?>


 
<script src="<?php echo BASE_URL; ?>assets/js/modulos/cargarcertificados.js"></script>
 


</body>

</html>