<?php include_once 'Views/template/header-admin.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Certificado</title>
    <script src="<?php echo BASE_URL; ?>assets/js/qrcode.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Crear Certificado</h3>
        <?php var_dump(extension_loaded('soap')); ?>
        <form id="formularioCertificado" enctype="multipart/form-data">
            <!-- Dirección del Certificado -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dirección del Certificado</h5>
                </div>
                <!-- Dirección del Certificado -->
                <div class="card-body">
                    <div class="mb-3">
                        <label for="direccion_existente" class="form-label">Seleccionar Dirección Existente</label>
                        <select class="form-select" id="direccion_existente" name="direccion_existente">
                            <option value="">-- Nueva Dirección --</option>
                            <?php foreach ($data['direcciones'] as $dir): ?>
                                <option value="<?= $dir['id']; ?>"><?= $dir['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>


                    <!-- Campos para nueva dirección -->
                    <div id="nuevaDireccionCampos">
                        <div class="mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" name="numero" id="numero">
                        </div>
                        <div class="mb-3">
                            <label for="calle" class="form-label">Calle</label>
                            <input type="text" class="form-control" name="calle" id="calle">
                        </div>
                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" name="ciudad" id="ciudad">
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" name="estado" id="estado">
                        </div>
                        <div class="mb-3">
                            <label for="zip" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" name="zip" id="zip">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion_geografica" class="form-label">Ubicación Geográfica (Latitud/Longitud)</label>
                            <div class="row mb-3">
                                <div class="col mb-3">
                                    <input type="number" class="form-control" id="latitud" name="latitud" placeholder="Latitud" step="any" min="-180" max="180" required>
                                </div>
                                <div class="col mb-3">
                                    <input type="number" class="form-control" id="longitud" name="longitud" placeholder="Longitud" step="any" min="-180" max="180" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Vehículo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Información del Vehículo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="vin" class="form-label">VIN (Número de Identificación del Vehículo)</label>
                        <input type="text" class="form-control" id="vin" name="vin" minlength="17"
                            maxlength="17" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Año</label>
                        <input type="number" class="form-control" id="year" name="year" max="2030" min="1980" required>
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
                        <label for="odometro" class="form-label">Odómetro (millas)</label>
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
                        <input type="text" class="form-control" id="numero_certificado" name="numero_certificado" value="<?= $data['cert_number']; ?>" readonly>
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
                        <label for="monitor_sensor_c2" class="form-label">Monitor del Sensor O2</label>
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
                        <label for="ebitn" class="form-label">EEI ITN</label>
                        <input type="text" class="form-control" id="ebitn" name="ebitn">
                    </div>
                    <div class="mb-3">
                        <label for="inspector" class="form-label">Nombre del Inspector</label>
                        <select class="form-select" id="inspector" name="inspector" required>
                            <option value="">-- Seleccionar Inspector --</option>
                            <?php foreach ($data['inspectores'] as $inspector): ?>
                                <option value="<?= $inspector['id']; ?>"><?= $inspector['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="firmaPreview" class="mt-3" style="display: none; text-align: center;">
                            <p><strong>Firma del Inspector:</strong></p>
                            <img id="imagenFirma" src="" alt="Firma del inspector" style="height: 50px; max-width: 120px;">
                        </div>

                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_expiracion" class="form-label">Fecha de Expiración</label>
                            <input type="date" class="form-control" id="fecha_expiracion" name="fecha_expiracion" required readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" id="btnPrevalidarDatos">Prevalidar Datos</button>
                    </div>
                </div>

                <!-- Imágenes y Códigos QR -->
                <!-- Imágenes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Imágenes </h5>
                    </div>

                    <div class="card-body">


                        <!--
      Mantengo id="imagenes" y name="imagenes[]" para NO romper tu JS/controlador.
      Uso múltiples inputs con el MISMO name="imagenes[]" para que PHP siga armando $_FILES['imagenes'] como arreglo.
      El ORDEN de los inputs define el índice [0..7] que usarás luego para mapear a fotoVin..fotoTaller.
    -->

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">1) Foto VIN (fotoVin)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                                <div class="form-text">Debe verse claramente el VIN.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">2) Foto Frente (fotoFrente)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">3) Foto Atrás (fotoAtras)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">4) Foto Piloto (fotoPiloto)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">5) Foto Pasajero (fotoPasajero)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">6) Foto Puerta (fotoPuerta)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">7) Foto Scanner (fotoScanner)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">8) Foto Taller (fotoTaller)</label>
                                <input type="file" class="form-control" name="imagenes[]" accept="image/jpeg" required>
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary " id="btnPrevalidarFotos">Prevalidar Fotografias</button>
                            </div>

                        </div>

                        <input type="hidden" name="foto_Extension" id="foto_Extension" value="jpg">
                    </div>
                </div>


                <div class="card mb-4" hidden>
                    <div class="card-header">
                        <h5>Códigos QR</h5>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-priamary" id="generarQR" disabled>Generar Código QR</button>

                        <div id="codigoQR" class="mt-3"></div>
                    </div>
                </div>

                <button type="button" class="btn btn-success w-100 " id="btnCrearCertificado">Crear Certificado</button>
        </form>
    </div>


</body>

</html>


<script src="<?php echo BASE_URL; ?>assets/js/modulos/crearcertificados.js"></script>
<!--
<script src="<? //php echo BASE_URL; 
                ?>assets/js/modulos/prevalidarDatos.js"></script> -->
<?php include_once 'Views/template/footer-admin.php'; ?>

<script>
    function forzarEnteroPositivo(inputId, opts = {}) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const maxLen = Number.isInteger(opts.maxLen) ? opts.maxLen : null;
        const allowEmpty = opts.allowEmpty !== undefined ? !!opts.allowEmpty : true;

        // Evita caracteres típicos en <input type="number"> como e, -, +, .
        input.addEventListener("keydown", function(e) {
            const blocked = ["e", "E", "+", "-", ".", ","];
            if (blocked.includes(e.key)) e.preventDefault();
        });

        input.addEventListener("input", function() {
            const start = this.selectionStart;
            const end = this.selectionEnd;

            // 1) Solo dígitos
            let v = String(this.value).replace(/[^\d]/g, "");

            // 2) Quitar ceros a la izquierda (opcional, pero útil para año/odómetro)
            //    Si NO quieres esto para ZIP, lo controlas con opts.keepLeadingZeros
            if (!opts.keepLeadingZeros) {
                v = v.replace(/^0+(?=\d)/, "");
            }

            // 3) Max length (si aplica)
            if (maxLen !== null) {
                v = v.slice(0, maxLen);
            }

            // 4) Si no permites vacío, fuerza "0" o ""
            if (!allowEmpty && v === "") v = "0";

            this.value = v;

            // Reposiciona cursor (lo mejor posible)
            const newPos = Math.min(start, this.value.length);
            this.setSelectionRange(newPos, newPos);
        });
    }

    // ===== Uso =====
    // ZIP: normalmente quieres permitir ceros a la izquierda y limitar longitud (5 o 9)
    forzarEnteroPositivo("zip", {
        maxLen: 9,
        keepLeadingZeros: true
    });

    // AÑO: 4 dígitos, sin ceros a la izquierda (opcional)
    forzarEnteroPositivo("year", {
        maxLen: 4
    });

    // ODÓMETRO: solo dígitos (puedes poner maxLen si quieres)
    forzarEnteroPositivo("odometro", {
        maxLen: 7
    });

    // (Si tu input "numero" debe ser solo dígitos también)
    forzarEnteroPositivo("numero", {
        maxLen: 6,
        keepLeadingZeros: true
    });

    // Teléfono (si lo quieres solo numérico, igual aplica)
    forzarEnteroPositivo("telefono", {
        maxLen: 15,
        keepLeadingZeros: true
    });
</script>