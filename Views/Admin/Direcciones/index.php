<?php include_once 'Views/template/header-admin.php'; ?>


<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblDirecciones">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Numero</th>
                        <th>Calle</th>
                        <th>Ciudad</th> 
                        <th>Estado</th>
                        <th>Codigo Postal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modalDirecciones" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmDirecciones">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="nombre">Numero</label>
                        <input id="number" class="form-control" type="text" name="number" placeholder="###">
                    </div>
                    <div class="form-group mb-2">
                        <label for="nombre">Calle</label>
                        <input id="street" class="form-control" type="text" name="street" placeholder="Calle">
                    </div>
                    <div class="form-group mb-2">
                        <label for="apellido">Ciudad</label>
                        <input id="city" class="form-control" type="text" name="city" placeholder="Ciudad">
                    </div>
                    <div class="form-group mb-2">
                        <label for="correo">Estado</label>
                        <input id="state" class="form-control" type="text" name="state" placeholder="Estado">
                    </div>
                    <div class="form-group mb-2">
                        <label for="clave">Codigo Postal</label>
                        <input id="zip" class="form-control" type="text" name="zip" placeholder="#####">
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

<script src="<?php echo BASE_URL . 'assets/js/modulos/direcciones.js'; ?>"></script>

</body>

</html>