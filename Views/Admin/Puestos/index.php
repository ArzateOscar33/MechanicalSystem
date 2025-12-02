<?php include_once 'Views/template/header-admin.php'; ?>
<button class="btn btn-primary mb-2" type="button" id="nuevo_registro">Nuevo</button>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblPuestos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Puesto</th>
                        <th>Departamento</th>
                        <th>Descripción</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Cargado por DataTables / JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para registrar/editar puesto -->
<div id="modalPuestos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal">Nuevo Puesto</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frmPuestos">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">

                    <div class="form-group mb-2">
                        <label for="nombre">Nombre del Puesto</label>
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Ej: Supervisor de Ventas">
                    </div>

                    <div class="form-group mb-2">
                        <label for="departamento">Departamento</label>
                        <select id="departamento" name="departamento" class="form-select">
                            <option value="">Seleccione un departamento</option>
                            <!-- Opciones cargadas por JavaScript -->
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" placeholder="Describe brevemente el puesto..."></textarea>
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
<script src="<?php echo BASE_URL . 'assets/js/modulos/puestos.js'; ?>"></script>

</body>
</html>
