<?php include_once 'Views/template/header-admin.php'; ?>
<button class="btn btn-primary mb-2" type="button" id="nuevo_registro">Nuevo</button>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblDepartamentos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Cargado por DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para registrar nuevo departamento -->
<div id="modalDepartamentos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal">Nuevo Departamento</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frmDepartamentos">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="nombre">Nombre del Departamento</label>
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Ej: Inspección">
                    </div>
                    <div class="form-group mb-2">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" placeholder="Describe brevemente el departamento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="btnAccion">Registrar</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL . 'assets/js/modulos/controlDepartamentos.js'; ?>"></script>

</body>
</html>
