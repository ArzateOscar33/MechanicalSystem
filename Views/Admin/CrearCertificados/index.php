<?php include_once 'Views/template/header-admin.php'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Certificado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ5b3Q0P6i7vD1nZzDaxpFqK5fjf6n6fRyy0e0sF7v/25D/J/Ry4/Ks/nj69" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode"></script>
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center">Crear Certificado</h3>
        <form id="formularioCertificado" enctype="multipart/form-data">
            
            <!-- Información del Vehículo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Información del Vehículo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="vin" class="form-label">VIN (Número de Identificación del Vehículo)</label>
                        <input type="text" class="form-control" id="vin" name="vin" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Año</label>
                        <input type="number" class="form-control" id="year" name="year" required>
                    </div>
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="mb-3">
                        <label for="fabricado_en" class="form-label">Fabricado en</label>
                        <input type="text" class="form-control" id="fabricado_en" name="fabricado_en" required>
                    </div>
                    <div class="mb-3">
                        <label for="placa" class="form-label">Placa</label>
                        <input type="text" class="form-control" id="placa" name="placa" required>
                    </div>
                    <div class="mb-3">
                        <label for="propietario" class="form-label">Nombre del Propietario</label>
                        <input type="text" class="form-control" id="propietario" name="propietario" required>
                    </div>
                    <div class="mb-3">
                        <label for="odometro" class="form-label">Odómetro (millas/kilómetros)</label>
                        <input type="number" class="form-control" id="odometro" name="odometro" required>
                    </div>
                </div>
            </div>

            <!-- Información de Monitoreo y Certificado -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Información de Monitoreo y Certificado</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="monitor_fallo_encendido" class="form-label">Monitor de Fallo de Encendido</label>
                        <select class="form-select" id="monitor_fallo_encendido" name="monitor_fallo_encendido" required>
                            <option value="PASA">PASA</option>
                            <option value="FALLA">FALLA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monitor_sistema_combustible" class="form-label">Monitor del Sistema de Combustible</label>
                        <select class="form-select" id="monitor_sistema_combustible" name="monitor_sistema_combustible" required>
                            <option value="PASA">PASA</option>
                            <option value="FALLA">FALLA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numero_certificado" class="form-label">Número de Certificado</label>
                        <input type="text" class="form-control" id="numero_certificado" name="numero_certificado" required>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion_geografica" class="form-label">Ubicación Geográfica (Latitud/Longitud)</label>
                        <div class="row">
                            <div class="col">
                                <input type="number" class="form-control" id="latitud" name="latitud" placeholder="Latitud" required>
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" id="longitud" name="longitud" placeholder="Longitud" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="monitor_integral_catalizador" class="form-label">Monitor Integral del Catalizador</label>
                        <select class="form-select" id="monitor_integral_catalizador" name="monitor_integral_catalizador" required>
                            <option value="PASA">PASA</option>
                            <option value="FALLA">FALLA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monitor_catalizador" class="form-label">Monitor del Catalizador</label>
                        <select class="form-select" id="monitor_catalizador" name="monitor_catalizador" required>
                            <option value="PASA">PASA</option>
                            <option value="FALLA">FALLA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monitor_sensor_c2" class="form-label">Monitor del Sensor C2</label>
                        <select class="form-select" id="monitor_sensor_c2" name="monitor_sensor_c2" required>
                            <option value="PASA">PASA</option>
                            <option value="FALLA">FALLA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="resultado_prueba" class="form-label">Resultado General de la Prueba</label>
                        <select class="form-select" id="resultado_prueba" name="resultado_prueba" required>
                            <option value="PASA">PASA</option>
                            <option value="FALLA">FALLA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Fecha y Firmas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Fecha y Firmas</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="ebitn" class="form-label">EBITN</label>
                        <input type="text" class="form-control" id="ebitn" name="ebitn" required>
                    </div>
                    <div class="mb-3">
                        <label for="inspector" class="form-label">Nombre del Inspector</label>
                        <input type="text" class="form-control" id="inspector" name="inspector" required>
                    </div>
                    <div class="mb-3">
                        <label for="firma_inspector" class="form-label">Firma del Inspector</label>
                        <input type="text" class="form-control" id="firma_inspector" name="firma_inspector" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_expiracion" class="form-label">Fecha de Expiración</label>
                        <input type="date" class="form-control" id="fecha_expiracion" name="fecha_expiracion" required>
                    </div>
                </div>
            </div>

            <!-- Imágenes y Códigos QR -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Imágenes</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imagenes" class="form-label">Fotografías del Vehículo</label>
                        <input type="file" class="form-control" id="imagenes" name="imagenes[]" accept="image/*" multiple required>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Códigos QR</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success" id="generarQR">Generar Código QR</button>
                    <div id="codigoQR" class="mt-3"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Crear Certificado</button>
        </form>
    </div>

    <script src="assets/js/crearCertificado.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<script>
    const base_url = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>assets/js/crearcertificados.js"></script>

<?php include_once 'Views/template/footer-admin.php'; ?>
