<?php include_once 'Views/template/header-admin.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Error</title>
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Crear Reporte de Error</h3>
        <form id="frmErroresUsuario" enctype="multipart/form-data">
            <!-- Tipo de Error -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Datos del Error</h5>
                </div>
                <!-- DTipo  -->
                <div class="card-body">
                <div class="mb-3">
  <label for="error" class="form-label">Tipo de Error</label>
  <select class="form-select" id="error" name="error">
    <option value="">-- Tipo de Error --</option>
    <?php foreach ($data['campos'] as $campo): ?>
        <option value="<?= $campo['field_name']; ?>"><?= $campo['label']; ?></option>
    <?php endforeach; ?>
  </select>
</div>

<!-- Campo oculto real que se enviará en el form -->
<input type="hidden" name="field_name" id="field_name" value="">


                    <!-- Campos para nueva dirección -->
                    <div id="nuevaDireccionCampos">
                        <div class="mb-3">
                            <label for="cert_number" class="form-label">Número de Certificado</label>
                            <select class="form-select" name="cert_number" id="cert_number">
                                <option value="">-- Seleccione un certificado del día --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="user_id" value="<?= $_SESSION['id_usuario']; ?>">

                        </div>
 

                        <div class="mb-3">
                            <label for="ciudad" class="form-label">Valor Propuesto</label>
                            <input type="text" class="form-control" name="proposed_value" id="proposed_value">
                        </div>

                        <div class="mb-3">
                            <label for="calle" class="form-label">Razon del Error</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="zip" class="form-label">Fecha de la Solicitud</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required readonly value="<?php echo date('Y-m-d'); ?>">

                        </div>
                    </div>
                </div>
            </div>



 



            <button type="submit" class="btn btn-primary w-100">Crear Reporte</button>
        </form>
    </div>


</body>

</html>


<script src="<?php echo BASE_URL; ?>assets/js/modulos/erroresusuario.js"></script>
<?php include_once 'Views/template/footer-admin.php'; ?>