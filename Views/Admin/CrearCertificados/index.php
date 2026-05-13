<?php include_once 'Views/template/header-admin.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Certificado</title>
    <script src="<?php echo BASE_URL; ?>assets/js/qrcode.min.js"></script>

    <style>
        .provider-card {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: 2px solid #dee2e6;
        }

        .provider-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 0.25rem 0.75rem rgba(13, 110, 253, 0.15);
        }

        .provider-card.active {
            border-color: #0d6efd;
            background: #f0f7ff;
        }

        .provider-badge {
            font-size: 0.75rem;
        }

        .prevalidation-status-box {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            background: #fff;
        }

        .d-none-force {
            display: none !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <h3 class="text-center mb-4">Crear Certificado</h3>

        <form id="formularioCertificado" enctype="multipart/form-data">

            <!-- ===================================================== -->
            <!-- MÉTODO DE PREVALIDACIÓN -->
            <!-- ===================================================== -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Método de Prevalidación</h5>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        Selecciona el proveedor con el que se prevalidará este certificado.
                        De momento esta selección solo es visual; después se conectará con base de datos.
                    </div>

                    <div class="row g-3">

                        <!-- OPCIÓN SECOMEXT -->
                        <div class="col-md-6">
                            <div class="provider-card rounded p-3 h-100 active" id="cardProveedorSecomext" data-provider="secomext">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="proveedor_prevalidacion" id="proveedor_secomext" value="secomext" checked>
                                            <label class="form-check-label fw-bold" for="proveedor_secomext">
                                                Secomext
                                            </label>
                                        </div>
                                    </div>
                                    <span class="badge bg-success provider-badge">Automático</span>
                                </div>

                                <p class="text-muted small mt-3 mb-0">
                                    Usa el flujo actual del sistema: prevalidar datos, prevalidar fotografías
                                    y después generar el certificado.
                                </p>
                            </div>
                        </div>

                        <!-- OPCIÓN CAAAREM -->
                        <div class="col-md-6">
                            <div class="provider-card rounded p-3 h-100" id="cardProveedorCaaarem" data-provider="caaarem">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="proveedor_prevalidacion" id="proveedor_caaarem" value="caaarem">
                                            <label class="form-check-label fw-bold" for="proveedor_caaarem">
                                                CAAAREM
                                            </label>
                                        </div>
                                    </div>
                                    <span class="badge bg-warning text-dark provider-badge">Manual / Portal</span>
                                </div>

                                <p class="text-muted small mt-3 mb-0">
                                    El usuario realizará la prevalidación fuera del sistema y después cargará
                                    el acuse o respuesta de CAAAREM.
                                </p>
                            </div>
                        </div>

                    </div>

                    <input type="hidden" id="proveedorSeleccionado" name="proveedorSeleccionado" value="secomext">
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- ESTADO VISUAL DE PREVALIDACIÓN -->
            <!-- ===================================================== -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estado de Prevalidación</h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <div class="prevalidation-status-box text-center">
                                <small class="text-muted d-block">Proveedor seleccionado</small>
                                <strong id="estadoProveedorTexto">Secomext</strong>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="prevalidation-status-box text-center">
                                <small class="text-muted d-block">Estatus</small>
                                <strong id="estadoPrevalidacionTexto">Pendiente</strong>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="prevalidation-status-box text-center">
                                <small class="text-muted d-block">Folio / Acuse</small>
                                <strong id="estadoFolioTexto">N/A</strong>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- DIRECCIÓN DEL CERTIFICADO -->
            <!-- ===================================================== -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dirección del Certificado</h5>
                </div>

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

            <!-- ===================================================== -->
            <!-- INFORMACIÓN DEL VEHÍCULO -->
            <!-- ===================================================== -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Información del Vehículo</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="vin" class="form-label">VIN (Número de Identificación del Vehículo)</label>
                        <input type="text" class="form-control" id="vin" name="vin" minlength="17" maxlength="17" required>
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

            <!-- ===================================================== -->
            <!-- INFORMACIÓN DE MONITOREO Y CERTIFICADO -->
            <!-- ===================================================== -->
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
                        <label for="numero_certificado" class="form-label">Número de Certificado (Preliminar)</label>
                        <input type="text" class="form-control disabled" id="numero_certificado" name="numero_certificado" value="<?= $data['cert_number']; ?>" readonly>
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

            <!-- ===================================================== -->
            <!-- FECHA Y FIRMAS -->
            <!-- ===================================================== -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Fecha y Firmas</h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id" required>
                            <option value="">-- Seleccionar Cliente --</option>
                            <?php foreach ($data['clientes'] as $cliente): ?>
                                <option value="<?= $cliente['id_customer']; ?>">
                                    <?= htmlspecialchars($cliente['nombre_cliente']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

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
            </div>

            <!-- ===================================================== -->
            <!-- BLOQUE SECOMEXT -->
            <!-- ===================================================== -->
            <div class="card mb-4" id="bloqueSecomext">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Prevalidación Secomext</h5>
                </div>

                <div class="card-body">
                    <div class="alert alert-primary">
                        Flujo actual del sistema. Primero prevalidas los datos y después las fotografías.
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" id="btnPrevalidarDatos">
                            Prevalidar Datos Secomext
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- IMÁGENES SECOMEXT -->
            <!-- Se ocultan cuando se selecciona CAAAREM para simular flujo separado -->
            <!-- ===================================================== -->
            <div class="card mb-4" id="bloqueImagenesSecomext">
                <div class="card-header">
                    <h5>Imágenes Secomext</h5>
                </div>

                <div class="card-body">
                    <div class="alert alert-secondary">
                        Estas fotografías se usan para la prevalidación automática con Secomext.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">1) Foto VIN (fotoVin)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                            <div class="form-text">Debe verse claramente el VIN.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">2) Foto Frente (fotoFrente)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">3) Foto Atrás (fotoAtras)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">4) Foto Piloto (fotoPiloto)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">5) Foto Pasajero (fotoPasajero)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">6) Foto Puerta (fotoPuerta)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">7) Foto Scanner (fotoScanner)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">8) Foto Taller (fotoTaller)</label>
                            <input type="file" class="form-control input-imagen-secomext" name="imagenes[]" accept="image/jpeg" required>
                        </div>

                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="btnPrevalidarFotos">
                                Prevalidar Fotografías Secomext
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="foto_Extension" id="foto_Extension" value="jpg">
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- BLOQUE CAAAREM -->
            <!-- De momento solo visual / simulación JS -->
            <!-- ===================================================== -->
            <div class="card mb-4 d-none-force" id="bloqueCaaarem">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Prevalidación CAAAREM</h5>
                </div>

                <div class="card-body">
                    <div class="alert alert-warning">
                        Este flujo es manual. Primero realiza la prevalidación en el portal o sistema de CAAAREM.
                        Después carga aquí el acuse o respuesta para simular que la prevalidación fue aprobada.
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-primary w-100" id="btnGenerarExpedienteCaaarem">
                                Generar Expediente CAAAREM
                            </button>
                        </div>

                        <div class="col-md-4">
                            <a href="#" target="_blank" class="btn btn-outline-secondary w-100" id="btnAbrirPortalCaaarem">
                                Abrir Portal CAAAREM
                            </a>
                        </div>

                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-dark w-100" id="btnMarcarEnviadoCaaarem">
                                Marcar como Enviado
                            </button>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Cargar Respuesta / Acuse CAAAREM</h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="estatus_caaarem" class="form-label">Resultado CAAAREM</label>
                            <select class="form-select" id="estatus_caaarem" name="estatus_caaarem">
                                <option value="">-- Seleccionar Resultado --</option>
                                <option value="aprobado">Aprobado</option>
                                <option value="rechazado">Rechazado</option>
                                <option value="error_tecnico">Error Técnico</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="folio_caaarem" class="form-label">Folio / Acuse CAAAREM</label>
                            <input type="text" class="form-control" id="folio_caaarem" name="folio_caaarem" placeholder="Ej. CAAAREM-123456">
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_respuesta_caaarem" class="form-label">Fecha de Respuesta</label>
                            <input type="date" class="form-control" id="fecha_respuesta_caaarem" name="fecha_respuesta_caaarem">
                        </div>

                        <div class="col-md-12">
                            <label for="acuse_caaarem" class="form-label">Archivo de Acuse / Respuesta</label>
                            <input type="file" class="form-control" id="acuse_caaarem" name="acuse_caaarem" accept=".pdf,.jpg,.jpeg,.png,.xml,.txt">

                            <div class="form-text">
                                De momento solo se simula la carga. Después se guardará este archivo en el servidor.
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="observaciones_caaarem" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones_caaarem" name="observaciones_caaarem" rows="3" placeholder="Ejemplo: Prevalidación aprobada desde portal CAAAREM."></textarea>
                        </div>

                        <div class="col-md-12">
                            <button type="button" class="btn btn-warning" id="btnGuardarRespuestaCaaarem">
                                Guardar Respuesta CAAAREM
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- QR OCULTO -->
            <!-- ===================================================== -->
            <div class="card mb-4" hidden>
                <div class="card-header">
                    <h5>Códigos QR</h5>
                </div>

                <div class="card-body">
                    <button type="button" class="btn btn-primary" id="generarQR" disabled>
                        Generar Código QR
                    </button>

                    <div id="codigoQR" class="mt-3"></div>
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- CREAR CERTIFICADO -->
            <!-- ===================================================== -->
            <button type="button" class="btn btn-success w-100 mb-4" id="btnCrearCertificado">
                Crear Certificado
            </button>

        </form>
    </div>
</body>

</html>

<script src="<?php echo BASE_URL; ?>assets/js/modulos/crearcertificados.js"></script>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script>
    // ======================================================
    // SIMULACIÓN VISUAL SECOMEXT / CAAAREM
    // De momento NO guarda en base de datos.
    // Solo muestra/oculta bloques y cambia textos.
    // ======================================================

    document.addEventListener("DOMContentLoaded", function() {
        const proveedorSecomext = document.getElementById("proveedor_secomext");
        const proveedorCaaarem = document.getElementById("proveedor_caaarem");

        const cardProveedorSecomext = document.getElementById("cardProveedorSecomext");
        const cardProveedorCaaarem = document.getElementById("cardProveedorCaaarem");

        const proveedorSeleccionado = document.getElementById("proveedorSeleccionado");

        const bloqueSecomext = document.getElementById("bloqueSecomext");
        const bloqueImagenesSecomext = document.getElementById("bloqueImagenesSecomext");
        const bloqueCaaarem = document.getElementById("bloqueCaaarem");

        const estadoProveedorTexto = document.getElementById("estadoProveedorTexto");
        const estadoPrevalidacionTexto = document.getElementById("estadoPrevalidacionTexto");
        const estadoFolioTexto = document.getElementById("estadoFolioTexto");

        const inputsImagenesSecomext = document.querySelectorAll(".input-imagen-secomext");

        const btnGenerarExpedienteCaaarem = document.getElementById("btnGenerarExpedienteCaaarem");
        const btnMarcarEnviadoCaaarem = document.getElementById("btnMarcarEnviadoCaaarem");
        const btnGuardarRespuestaCaaarem = document.getElementById("btnGuardarRespuestaCaaarem");

        const estatusCaaarem = document.getElementById("estatus_caaarem");
        const folioCaaarem = document.getElementById("folio_caaarem");
        const fechaRespuestaCaaarem = document.getElementById("fecha_respuesta_caaarem");
        const acuseCaaarem = document.getElementById("acuse_caaarem");

        function cambiarProveedor(proveedor) {
            proveedorSeleccionado.value = proveedor;

            if (proveedor === "secomext") {
                proveedorSecomext.checked = true;
                proveedorCaaarem.checked = false;

                cardProveedorSecomext.classList.add("active");
                cardProveedorCaaarem.classList.remove("active");

                bloqueSecomext.classList.remove("d-none-force");
                bloqueImagenesSecomext.classList.remove("d-none-force");
                bloqueCaaarem.classList.add("d-none-force");

                inputsImagenesSecomext.forEach(input => {
                    input.setAttribute("required", "required");
                });

                estadoProveedorTexto.textContent = "Secomext";
                estadoPrevalidacionTexto.textContent = "Pendiente";
                estadoFolioTexto.textContent = "N/A";
            }

            if (proveedor === "caaarem") {
                proveedorSecomext.checked = false;
                proveedorCaaarem.checked = true;

                cardProveedorSecomext.classList.remove("active");
                cardProveedorCaaarem.classList.add("active");

                bloqueSecomext.classList.add("d-none-force");
                bloqueImagenesSecomext.classList.add("d-none-force");
                bloqueCaaarem.classList.remove("d-none-force");

                // Importante:
                // Al ocultar Secomext quitamos required de las imágenes para que el navegador
                // no bloquee el envío del formulario por campos ocultos.
                inputsImagenesSecomext.forEach(input => {
                    input.removeAttribute("required");
                });

                estadoProveedorTexto.textContent = "CAAAREM";
                estadoPrevalidacionTexto.textContent = "Pendiente de acuse";
                estadoFolioTexto.textContent = "N/A";
            }
        }

        cardProveedorSecomext.addEventListener("click", function() {
            cambiarProveedor("secomext");
        });

        cardProveedorCaaarem.addEventListener("click", function() {
            cambiarProveedor("caaarem");
        });

        proveedorSecomext.addEventListener("change", function() {
            cambiarProveedor("secomext");
        });

        proveedorCaaarem.addEventListener("change", function() {
            cambiarProveedor("caaarem");
        });

        if (btnGenerarExpedienteCaaarem) {
            btnGenerarExpedienteCaaarem.addEventListener("click", function() {
                estadoPrevalidacionTexto.textContent = "Expediente generado";

                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "info",
                        title: "Expediente generado",
                        text: "Simulación: aquí después generarás el expediente para subirlo al portal de CAAAREM."
                    });
                } else {
                    alert("Simulación: expediente CAAAREM generado.");
                }
            });
        }

        if (btnMarcarEnviadoCaaarem) {
            btnMarcarEnviadoCaaarem.addEventListener("click", function() {
                estadoPrevalidacionTexto.textContent = "Enviado manualmente";

                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "success",
                        title: "Marcado como enviado",
                        text: "Simulación: el expediente fue marcado como enviado manualmente a CAAAREM."
                    });
                } else {
                    alert("Simulación: marcado como enviado a CAAAREM.");
                }
            });
        }

        if (btnGuardarRespuestaCaaarem) {
            btnGuardarRespuestaCaaarem.addEventListener("click", function() {
                const estatus = estatusCaaarem.value;
                const folio = folioCaaarem.value.trim();
                const fecha = fechaRespuestaCaaarem.value;
                const archivo = acuseCaaarem.files[0];

                if (!estatus) {
                    if (typeof Swal !== "undefined") {
                        Swal.fire("Atención", "Selecciona el resultado de CAAAREM.", "warning");
                    } else {
                        alert("Selecciona el resultado de CAAAREM.");
                    }
                    return;
                }

                if (estatus === "aprobado" && folio === "") {
                    if (typeof Swal !== "undefined") {
                        Swal.fire("Atención", "Captura el folio o número de acuse CAAAREM.", "warning");
                    } else {
                        alert("Captura el folio o número de acuse CAAAREM.");
                    }
                    return;
                }

                if (estatus === "aprobado" && !archivo) {
                    if (typeof Swal !== "undefined") {
                        Swal.fire("Atención", "Carga el archivo de acuse o respuesta de CAAAREM.", "warning");
                    } else {
                        alert("Carga el archivo de acuse o respuesta de CAAAREM.");
                    }
                    return;
                }

                let textoEstatus = "Pendiente";

                if (estatus === "aprobado") {
                    textoEstatus = "Aprobado";
                } else if (estatus === "rechazado") {
                    textoEstatus = "Rechazado";
                } else if (estatus === "error_tecnico") {
                    textoEstatus = "Error técnico";
                } else if (estatus === "pendiente") {
                    textoEstatus = "Pendiente";
                }

                estadoProveedorTexto.textContent = "CAAAREM";
                estadoPrevalidacionTexto.textContent = textoEstatus;
                estadoFolioTexto.textContent = folio || "N/A";

                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: estatus === "aprobado" ? "success" : "info",
                        title: "Respuesta CAAAREM guardada",
                        html: `
                            <p class="mb-1"><strong>Estatus:</strong> ${textoEstatus}</p>
                            <p class="mb-1"><strong>Folio:</strong> ${folio || "N/A"}</p>
                            <p class="mb-1"><strong>Fecha:</strong> ${fecha || "Sin fecha"}</p>
                            <p class="mb-0"><strong>Archivo:</strong> ${archivo ? archivo.name : "Sin archivo"}</p>
                        `
                    });
                } else {
                    alert("Simulación: respuesta CAAAREM guardada.");
                }
            });
        }

        // Estado inicial
        cambiarProveedor("secomext");
    });


    // ======================================================
    // VALIDACIONES NUMÉRICAS EXISTENTES
    // ======================================================

    function forzarEnteroPositivo(inputId, opts = {}) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const maxLen = Number.isInteger(opts.maxLen) ? opts.maxLen : null;
        const allowEmpty = opts.allowEmpty !== undefined ? !!opts.allowEmpty : true;

        input.addEventListener("keydown", function(e) {
            const blocked = ["e", "E", "+", "-", ".", ","];
            if (blocked.includes(e.key)) e.preventDefault();
        });

        input.addEventListener("input", function() {
            const start = this.selectionStart;

            let v = String(this.value).replace(/[^\d]/g, "");

            if (!opts.keepLeadingZeros) {
                v = v.replace(/^0+(?=\d)/, "");
            }

            if (maxLen !== null) {
                v = v.slice(0, maxLen);
            }

            if (!allowEmpty && v === "") v = "0";

            this.value = v;

            const newPos = Math.min(start, this.value.length);
            this.setSelectionRange(newPos, newPos);
        });
    }

    forzarEnteroPositivo("zip", {
        maxLen: 9,
        keepLeadingZeros: true
    });

    forzarEnteroPositivo("year", {
        maxLen: 4
    });

    forzarEnteroPositivo("odometro", {
        maxLen: 7
    });

    forzarEnteroPositivo("numero", {
        maxLen: 6,
        keepLeadingZeros: true
    });

    forzarEnteroPositivo("telefono", {
        maxLen: 15,
        keepLeadingZeros: true
    });
</script>