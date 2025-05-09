<?php include_once 'Views/template/header-admin.php'; ?>


<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblInspectores">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Nombre</th>
                        <th>Firma</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modalInspectores" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmInspectores">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="nombre">Nombre</label>
                        <input id="name" class="form-control" type="text" name="name" placeholder="Nombre">
                    </div>
                    <input type="hidden" name="firma_actual" id="firma_actual">

                    <div class="form-group mb-2">
                        <label for="firma">Firma actual</label><br>
                        <img id="imgFirma" src="" alt="Firma" width="200" class="img-thumbnail mb-2"><br>

                        <label for="firma">Subir nueva firma (opcional)</label>
                        <input class="form-control" type="file" name="firma" id="firma" accept="image/*">
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

<script src="<?php echo BASE_URL . 'assets/js/modulos/controlInspectores.js'; ?>"></script>

</body>

</html>