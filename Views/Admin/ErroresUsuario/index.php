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

      <div class="card mb-4">
        <div class="card-header">
          <h5>Datos del Error</h5>
        </div>

        <div class="card-body">

          <!-- Select de tipo de error -->
          <div class="mb-3">
            <label for="error" class="form-label">Tipo de Error</label>
            <select class="form-select" id="error" name="error">
              <option value="">-- Tipo de Error --</option>
            </select>
          </div>
          <input type="hidden" name="field_name" id="field_name" value="">

          <!-- Select de certificados del día -->
          <div class="mb-3">
            <label for="cert_number" class="form-label">Número de Certificado</label>
            <select class="form-select" name="cert_number" id="cert_number">
              <option value="">-- Seleccione un certificado del día --</option>
            </select>
          </div>

          <!-- Valor propuesto -->
          <div class="mb-3">
            <label for="proposed_value" class="form-label">Valor Propuesto</label>
            <input type="text" class="form-control" name="proposed_value" id="proposed_value">
          </div>

          <!-- Select de inspector -->
          <div class="mb-3" id="inspectorSelectDiv" style="display: none;">
            <label for="inspector_select" class="form-label">Seleccionar Inspector</label>
            <select class="form-select" id="inspector_select">
              <option value="">-- Seleccione un inspector --</option>
            </select>
          </div>

          <!-- Subida de imágenes -->
          <div class="mb-3" id="imagenesInput" style="display: none;">
            <label for="imagenes" class="form-label">Subir nuevas imágenes</label>
            <input type="file" class="form-control" name="imagenes[]" id="imagenes" accept="image/*" multiple>
            <small class="text-muted">Máximo 9 imágenes</small>
          </div>

          <!-- Razon del error -->
          <div class="mb-3">
            <label for="reason" class="form-label">Razón del Error</label>
            <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
          </div>

          <!-- Fecha de solicitud -->
          <div class="mb-3">
            <label for="fecha" class="form-label">Fecha de la Solicitud</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required readonly value="<?php echo date('Y-m-d'); ?>">
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
